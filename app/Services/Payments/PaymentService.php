<?php

namespace App\Services\Payments;

use App\Models\Booking;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Services\BillingLogService;
use App\Services\NotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly NotificationService $notificationService,
        private readonly BillingLogService $billingLogService
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paymentRepository->paginate($perPage);
    }

    public function findOrFail(int $id): Payment
    {
        return $this->paymentRepository->findOrFail($id);
    }

    public function createForBooking(Booking $booking): Payment
    {
        $existingPayment = $this->paymentRepository->findByBookingId($booking->id);

        if ($existingPayment !== null) {
            return $existingPayment;
        }

        $booking->loadMissing(['service', 'bookingItems']);
        $amountFromItems = (float) $booking->bookingItems->sum('subtotal');
        $amount = $amountFromItems > 0
            ? $amountFromItems
            : (float) $booking->service->price;

        return $this->paymentRepository->create([
            'tenant_id' => $booking->tenant_id,
            'booking_id' => $booking->id,
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
            'payment_method' => Payment::METHOD_MANUAL,
            'paid_at' => null,
        ]);
    }

    public function markAsPaid(Payment $payment, string $paymentMethod = Payment::METHOD_MANUAL): Payment
    {
        $updated = $this->paymentRepository->update($payment, [
            'status' => Payment::STATUS_PAID,
            'payment_method' => $paymentMethod,
            'paid_at' => Carbon::now(),
        ]);
        $this->billingLogService->logPayment($updated, 'payment_marked_paid');

        $booking = $updated->booking;
        $this->notificationService->sendWhatsApp(
            $booking->customer->phone,
            sprintf(
                'Hi %s, your payment for %s on %s has been received. Thank you!',
                $booking->customer->name,
                $booking->service->name,
                $booking->booking_date?->format('d M Y')
            )
        );

        return $updated;
    }

    public function updateStatus(Payment $payment, array $data): Payment
    {
        $status = $data['status'];
        $paymentMethod = $data['payment_method'] ?? $payment->payment_method;

        if ($status === Payment::STATUS_PAID) {
            return $this->markAsPaid($payment, $paymentMethod);
        }

        $updated = $this->paymentRepository->update($payment, [
            'tenant_id' => $payment->tenant_id,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'paid_at' => null,
        ]);

        $this->billingLogService->logPayment($updated, 'payment_status_updated');

        return $updated;
    }

    public function getRevenueSummary(): array
    {
        $paidCount = $this->paymentRepository->countByStatus(Payment::STATUS_PAID);
        $pendingCount = $this->paymentRepository->countByStatus(Payment::STATUS_PENDING);
        $denominator = $paidCount + $pendingCount;

        return [
            'total_revenue' => $this->paymentRepository->sumPaidRevenue(),
            'monthly_revenue' => $this->paymentRepository->sumPaidRevenueCurrentMonth(),
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'conversion_rate' => $denominator > 0 ? round(($paidCount / $denominator) * 100, 2) : 0.0,
        ];
    }

    public function processBookingPayment(Booking $booking, float $amount): void
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payment amount must be greater than zero.',
            ]);
        }

        $payment = $this->createForBooking($booking);

        $this->paymentRepository->update($payment, [
            'amount' => $amount,
        ]);
    }
}
