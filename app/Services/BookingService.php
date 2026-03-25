<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ServiceRepository;
use App\Services\Integrations\GoogleCalendarService;
use App\Services\Payments\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly GoogleCalendarService $googleCalendarService,
        private readonly PaymentService $paymentService,
        private readonly NotificationService $notificationService,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->bookingRepository->paginate($perPage);
    }

    public function findOrFail(int $id): Booking
    {
        return $this->bookingRepository->findOrFail($id);
    }

    public function create(array $data): Booking
    {
        $tenantId = (int) Auth::id();
        $this->subscriptionService->assertBookingCreationAllowed($tenantId);
        $this->customerRepository->findOrFail((int) $data['customer_id']);
        $serviceItems = $this->normalizeServiceItems($data);
        $primaryServiceId = (int) $serviceItems->first()['service_id'];
        $totalDuration = (int) $serviceItems->sum('duration_minutes');
        $totalPeople = (int) $serviceItems->sum('people_count');

        $startTime = $this->normalizeTime($data['booking_time']);
        $endTime = $this->calculateEndTimeByDuration($startTime, $totalDuration);

        $this->assertBookingDateTime($data['booking_date'], $startTime, $data['status']);
        $this->checkAvailability($data['booking_date'], $startTime, $endTime);

        $data['tenant_id'] = $tenantId;
        $data['service_id'] = $primaryServiceId;
        $data['total_people'] = $totalPeople;
        $data['booking_time'] = $startTime;
        $data['end_time'] = $endTime;
        $booking = DB::transaction(function () use ($data, $serviceItems, $tenantId) {
            $booking = $this->bookingRepository->create($data);
            $this->syncBookingItems($booking, $serviceItems, $tenantId);

            return $booking;
        });
        $this->syncWithCalendar($booking);
        $this->paymentService->createForBooking($booking->load('service', 'customer', 'bookingItems'));
        $this->notificationService->sendWhatsApp(
            $booking->customer->phone,
            sprintf(
                'Hi %s, your booking for %s on %s is confirmed.',
                $booking->customer->name,
                $booking->service->name,
                $booking->booking_date?->format('d M Y')
            )
        );

        return $booking;
    }

    public function update(Booking $booking, array $data): Booking
    {
        if (isset($data['customer_id'])) {
            $this->customerRepository->findOrFail((int) $data['customer_id']);
        }

        $shouldReplaceItems = array_key_exists('services', $data) || array_key_exists('service_id', $data);
        $normalizedItems = $shouldReplaceItems
            ? $this->normalizeServiceItems([
                'service_id' => $data['service_id'] ?? $booking->service_id,
                'services' => $data['services'] ?? null,
            ])
            : $this->normalizedItemsFromBooking($booking);
        $bookingDate = $data['booking_date'] ?? $booking->booking_date?->format('Y-m-d');
        $bookingTime = $data['booking_time'] ?? $booking->booking_time ?? '00:00:00';
        $status = $data['status'] ?? $booking->status;
        $serviceId = (int) $normalizedItems->first()['service_id'];
        $totalDuration = (int) $normalizedItems->sum('duration_minutes');
        $totalPeople = (int) $normalizedItems->sum('people_count');
        $startTime = $this->normalizeTime($bookingTime);
        $endTime = $this->calculateEndTimeByDuration($startTime, $totalDuration);

        if ($bookingDate && $bookingTime) {
            $this->assertBookingDateTime($bookingDate, $startTime, $status);
            $this->checkAvailability($bookingDate, $startTime, $endTime, $booking->id);
        }

        $data['service_id'] = $serviceId;
        $data['total_people'] = $totalPeople;
        $data['booking_time'] = $startTime;
        $data['end_time'] = $endTime;
        $updatedBooking = DB::transaction(function () use ($booking, $data, $normalizedItems, $shouldReplaceItems) {
            $updatedBooking = $this->bookingRepository->update($booking, $data);
            if ($shouldReplaceItems) {
                $this->syncBookingItems($updatedBooking, $normalizedItems, (int) $updatedBooking->tenant_id);
            }

            return $updatedBooking;
        });
        $this->syncWithCalendar($updatedBooking);

        return $updatedBooking;
    }

    public function delete(Booking $booking): void
    {
        $this->bookingRepository->delete($booking);
        $this->googleCalendarService->detachBooking($booking);
    }

    public function getTotalBookings(): int
    {
        return $this->bookingRepository->countAll();
    }

    public function getTotalRevenue(): float
    {
        return $this->bookingRepository->totalRevenue();
    }

    public function getUpcomingBookings(int $limit = 5): Collection
    {
        return $this->bookingRepository->getUpcoming($limit);
    }

    public function getCalendarEvents(?string $startDate = null, ?string $endDate = null): SupportCollection
    {
        return $this->bookingRepository->getInRange($startDate, $endDate)
            ->map(function (Booking $booking) {
                $eventStart = Carbon::parse(
                    $booking->booking_date?->format('Y-m-d').' '.$this->normalizeTime($booking->booking_time ?? '00:00:00')
                );
                $eventEnd = Carbon::parse(
                    $booking->booking_date?->format('Y-m-d').' '.$this->normalizeTime($booking->end_time ?? $booking->booking_time ?? '00:00:00')
                );
                $color = $this->getColor($booking->status);
                $servicesSummary = $booking->bookingItems->isNotEmpty()
                    ? $booking->bookingItems
                        ->map(fn ($item) => sprintf('%s x %d org', $item->service?->name ?? 'Service', (int) $item->people_count))
                        ->implode(', ')
                    : sprintf('%s x %d org', $booking->service?->name ?? 'Service', (int) ($booking->total_people ?? 1));

                return [
                    'id' => $booking->id,
                    'title' => sprintf(
                        '%s - %s (%d org)',
                        $booking->service?->name ?? 'Service',
                        $booking->customer?->name ?? 'Customer',
                        (int) ($booking->total_people ?? 1)
                    ),
                    'start' => $eventStart->format('Y-m-d\TH:i:s'),
                    'end' => $eventEnd->format('Y-m-d\TH:i:s'),
                    'status' => $booking->status,
                    'service_name' => $booking->service?->name,
                    'customer_name' => $booking->customer?->name,
                    'customer_phone' => $booking->customer?->phone,
                    'total_people' => (int) ($booking->total_people ?? 1),
                    'services_summary' => $servicesSummary,
                    'location' => $booking->location,
                    'notes' => $booking->notes,
                    'color' => $color,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#1f2937',
                ];
            });
    }

    public function checkAvailability(string $date, string $startTime, string $endTime, ?int $ignoreBookingId = null): void
    {
        if ($this->bookingRepository->hasConflict($date, $startTime, $endTime, $ignoreBookingId)) {
            throw new InvalidArgumentException('Selected schedule overlaps with another booking.');
        }
    }

    public function rescheduleFromCalendar(Booking $booking, string $startDateTime): Booking
    {
        $start = Carbon::parse($startDateTime);
        $startTime = $start->format('H:i:s');
        $totalDuration = (int) $booking->bookingItems()
            ->selectRaw('COALESCE(SUM(duration_minutes), 0) AS total_duration')
            ->value('total_duration');
        if ($totalDuration <= 0) {
            $totalDuration = (int) $this->serviceRepository->findOrFail((int) $booking->service_id)->duration;
        }
        $endTime = $this->calculateEndTimeByDuration($startTime, $totalDuration);
        $bookingDate = $start->format('Y-m-d');

        $this->assertBookingDateTime($bookingDate, $startTime, $booking->status);
        $this->checkAvailability($bookingDate, $startTime, $endTime, $booking->id);

        return $this->update($booking, [
            'booking_date' => $bookingDate,
            'booking_time' => $startTime,
        ]);
    }

    private function assertBookingDateTime(string $date, string $time, string $status): void
    {
        if (! in_array($status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)) {
            return;
        }

        $bookingAt = Carbon::parse($date.' '.$time);

        if ($bookingAt->isPast()) {
            throw new InvalidArgumentException('Booking date and time must be in the future.');
        }
    }

    private function syncWithCalendar(Booking $booking): void
    {
        $this->googleCalendarService->syncBooking($booking);
    }

    private function calculateEndTimeByDuration(string $startTime, int $durationMinutes): string
    {
        $start = Carbon::createFromFormat('H:i:s', $startTime);

        return $start->copy()->addMinutes(max(1, $durationMinutes))->format('H:i:s');
    }

    private function normalizeTime(string $time): string
    {
        if (preg_match('/^\d{2}:\d{2}$/', $time) === 1) {
            return $time.':00';
        }

        return Carbon::createFromFormat('H:i:s', $time)->format('H:i:s');
    }

    private function getColor(string $status): string
    {
        return match ($status) {
            Booking::STATUS_PENDING => '#facc15',
            Booking::STATUS_CONFIRMED => '#22c55e',
            Booking::STATUS_COMPLETED => '#3b82f6',
            Booking::STATUS_CANCELED => '#ef4444',
            default => '#9ca3af',
        };
    }

    private function normalizeServiceItems(array $data): SupportCollection
    {
        $rows = collect($data['services'] ?? [])
            ->filter(fn (mixed $row) => is_array($row) && ! empty($row['service_id']))
            ->values();

        if ($rows->isEmpty()) {
            $singleServiceId = (int) ($data['service_id'] ?? 0);
            if ($singleServiceId <= 0) {
                throw new InvalidArgumentException('At least one service is required.');
            }
            $rows = collect([[
                'service_id' => $singleServiceId,
                'people_count' => 1,
            ]]);
        }

        return $rows->map(function (array $row) {
            $serviceId = (int) ($row['service_id'] ?? 0);
            $peopleCount = max(1, (int) ($row['people_count'] ?? 1));
            $service = $this->serviceRepository->findOrFail($serviceId);

            return [
                'service_id' => $serviceId,
                'people_count' => $peopleCount,
                'unit_price' => (float) $service->price,
                'duration_minutes' => (int) $service->duration * $peopleCount,
                'subtotal' => (float) $service->price * $peopleCount,
            ];
        });
    }

    private function syncBookingItems(Booking $booking, SupportCollection $items, int $tenantId): void
    {
        $booking->bookingItems()->delete();
        $payload = $items->map(fn (array $item) => [
            'tenant_id' => $tenantId,
            'service_id' => $item['service_id'],
            'people_count' => $item['people_count'],
            'unit_price' => $item['unit_price'],
            'duration_minutes' => $item['duration_minutes'],
            'subtotal' => $item['subtotal'],
        ])->all();

        if ($payload !== []) {
            $booking->bookingItems()->createMany($payload);
        }
    }

    private function normalizedItemsFromBooking(Booking $booking): SupportCollection
    {
        $booking->loadMissing('bookingItems');
        if ($booking->bookingItems->isNotEmpty()) {
            return $booking->bookingItems->map(fn ($item) => [
                'service_id' => (int) $item->service_id,
                'people_count' => (int) $item->people_count,
                'unit_price' => (float) $item->unit_price,
                'duration_minutes' => (int) $item->duration_minutes,
                'subtotal' => (float) $item->subtotal,
            ]);
        }

        $service = $this->serviceRepository->findOrFail((int) $booking->service_id);
        return collect([[
            'service_id' => (int) $booking->service_id,
            'people_count' => (int) ($booking->total_people ?? 1),
            'unit_price' => (float) $service->price,
            'duration_minutes' => (int) $service->duration * (int) ($booking->total_people ?? 1),
            'subtotal' => (float) $service->price * (int) ($booking->total_people ?? 1),
        ]]);
    }
}
