<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $plan = $this->planService->normalizePlan(getUserPlan($user));
        $trialDaysLeft = getRemainingDays($user);
        $expiresAt = $user?->subscription?->expired_at;
        $bookingUsage = $this->subscriptionService->getBookingUsage((int) ($user?->id ?? 0));
        $plans = collect($this->planService->all())
            ->map(fn (array $detail, string $key) => $this->planService->detail($key))
            ->all();

        return view('billing.index', [
            'plan' => $plan,
            'trialDaysLeft' => $trialDaysLeft,
            'expiresAt' => $expiresAt,
            'currentPlan' => $plans[$plan] ?? $this->planService->detail($this->planService->defaultPlan()),
            'plans' => $plans,
            'bookingUsage' => $bookingUsage,
        ]);
    }
}
