<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private readonly PlanService $planService
    ) {
    }

    public function sendWhatsApp(string $phone, string $message): void
    {
        // Placeholder for real WhatsApp gateway integration.
        Log::info('WhatsApp notification queued', [
            'phone' => $phone,
            'message' => $message,
        ]);
    }

    public function sendWelcomeMessage(User $user, Subscription $subscription): void
    {
        $planDetail = $this->planService->detail($subscription->plan);
        $message = sprintf(
            'Selamat datang! Paket Anda: %s. %s.',
            strtoupper((string) ($planDetail['name'] ?? $subscription->plan)),
            (string) ($planDetail['booking_limit_label'] ?? '')
        );

        Log::info('Welcome onboarding message', [
            'user_id' => $user->id,
            'email' => $user->email,
            'message' => $message,
        ]);
    }

    public function sendPublicBookingAlert(User $tenant, Booking $booking): void
    {
        $message = sprintf(
            'Booking publik baru: %s memesan %s pada %s pukul %s.',
            $booking->customer->name,
            $booking->service->name,
            $booking->booking_date?->format('d M Y'),
            substr((string) $booking->booking_time, 0, 5)
        );

        Log::info('Public booking alert', [
            'tenant_id' => $tenant->id,
            'tenant_email' => $tenant->email,
            'booking_id' => $booking->id,
            'message' => $message,
        ]);

        try {
            Mail::raw($message, static function ($mail) use ($tenant): void {
                $mail->to($tenant->email)->subject('Booking Publik Baru');
            });
        } catch (\Throwable $exception) {
            Log::warning('Failed to send tenant public booking email.', [
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function sendTomorrowBookingReminderEmail(User $tenant, Booking $booking): void
    {
        $message = sprintf(
            "Pengingat booking besok:\n- Pelanggan: %s\n- Layanan: %s\n- Tanggal: %s\n- Jam: %s\n- Lokasi: %s",
            (string) ($booking->customer->name ?? '-'),
            (string) ($booking->service->name ?? '-'),
            (string) ($booking->booking_date?->translatedFormat('d F Y') ?? '-'),
            substr((string) ($booking->booking_time ?? ''), 0, 5),
            (string) ($booking->location ?: '-')
        );

        try {
            Mail::raw($message, static function ($mail) use ($tenant): void {
                $mail->to($tenant->email)->subject('Pengingat Booking Besok');
            });
        } catch (\Throwable $exception) {
            Log::warning('Failed to send tomorrow booking email reminder.', [
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function sendTomorrowBookingReminderWhatsApp(User $tenant, Booking $booking): void
    {
        $phone = $booking->customer->phone ?: null;
        if ($phone === null || trim($phone) === '') {
            return;
        }

        $message = sprintf(
            'Pengingat: Besok ada booking %s untuk %s pukul %s. Lokasi: %s.',
            (string) ($booking->service->name ?? '-'),
            (string) ($booking->customer->name ?? '-'),
            substr((string) ($booking->booking_time ?? ''), 0, 5),
            (string) ($booking->location ?: '-')
        );

        $this->sendWhatsApp($phone, $message);

        Log::info('Tomorrow booking WhatsApp reminder sent.', [
            'tenant_id' => $tenant->id,
            'booking_id' => $booking->id,
            'phone' => $phone,
        ]);
    }
}
