<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
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
        $daysLeft = max(0, now()->diffInDays($subscription->expired_at, false));
        $message = sprintf(
            'Welcome! You are on %s PLAN. Trial ends in %d day(s).',
            strtoupper($subscription->plan),
            $daysLeft
        );

        Log::info('Welcome onboarding message', [
            'user_id' => $user->id,
            'email' => $user->email,
            'message' => $message,
        ]);
    }
}
