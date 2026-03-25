<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Payment::query()
            ->with(['booking.customer', 'booking.service'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Payment
    {
        return Payment::query()
            ->with(['booking.customer', 'booking.service'])
            ->findOrFail($id);
    }

    public function findByBookingId(int $bookingId): ?Payment
    {
        return Payment::query()
            ->where('booking_id', $bookingId)
            ->first();
    }

    public function create(array $data): Payment
    {
        return Payment::query()->create($data);
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);

        return $payment->refresh();
    }

    public function sumPaidRevenue(): float
    {
        return (float) Payment::query()
            ->where('status', Payment::STATUS_PAID)
            ->sum('amount');
    }

    public function sumPaidRevenueCurrentMonth(): float
    {
        return (float) Payment::query()
            ->where('status', Payment::STATUS_PAID)
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');
    }

    public function countByStatus(string $status): int
    {
        return Payment::query()
            ->where('status', $status)
            ->count();
    }
}
