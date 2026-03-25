<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $plan = getUserPlan($user);
        $trialDaysLeft = getRemainingDays($user);
        $expiresAt = $user?->subscription?->expired_at;

        $plans = [
            'free' => [
                'name' => 'Free',
                'price' => 'Rp 0',
                'booking_limit' => 'Max 10 bookings/month',
                'features' => ['Booking Calendar', 'Customer Management', 'Basic Reports'],
                'theme' => 'stone',
            ],
            'pro' => [
                'name' => 'Pro',
                'price' => 'Rp 199K / month',
                'booking_limit' => 'Unlimited bookings',
                'features' => ['Everything in Free', 'Unlimited Bookings', 'Priority Support'],
                'theme' => 'rose',
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => 'Rp 399K / month',
                'booking_limit' => 'Unlimited + advanced',
                'features' => ['Everything in Pro', 'Advanced Analytics', 'Dedicated Assistance'],
                'theme' => 'amber',
            ],
        ];

        return view('billing.index', [
            'plan' => $plan,
            'trialDaysLeft' => $trialDaysLeft,
            'expiresAt' => $expiresAt,
            'currentPlan' => $plans[$plan] ?? $plans['free'],
            'plans' => $plans,
        ]);
    }
}
