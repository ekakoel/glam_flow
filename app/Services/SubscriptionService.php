<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\SubscriptionRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly BookingRepository $bookingRepository,
        private readonly BillingLogService $billingLogService
    ) {
    }

    public function ensureUserSubscription(int $userId): Subscription
    {
        return $this->subscriptionRepository->firstOrCreateForUser($userId);
    }

    public function createTrialForUser(int $userId, string $plan, int $trialDays = 7): Subscription
    {
        $subscription = $this->subscriptionRepository->firstOrCreateForUser($userId);

        return $this->subscriptionRepository->update($subscription, [
            'plan' => $plan,
            'expired_at' => Carbon::now()->addDays($trialDays),
        ]);
    }

    public function getCurrentPlanForUser(int $userId): string
    {
        $subscription = $this->subscriptionRepository->findByUserId($userId);

        return $subscription?->plan ?? Subscription::PLAN_FREE;
    }

    public function assertBookingCreationAllowed(int $userId): void
    {
        $user = User::query()->find($userId);
        if ($user?->isSuperAdmin()) {
            return;
        }

        $plan = $this->getCurrentPlanForUser($userId);

        if ($plan !== Subscription::PLAN_FREE) {
            return;
        }

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $count = $this->bookingRepository->getInRange($startOfMonth, $endOfMonth)->count();

        if ($count >= 10) {
            throw ValidationException::withMessages([
                'plan' => 'Free plan limit reached. Upgrade to Pro for unlimited monthly bookings.',
            ]);
        }
    }

    public function upgradePlan(int $userId, string $plan, ?Carbon $expiredAt = null): Subscription
    {
        $subscription = $this->ensureUserSubscription($userId);
        $updated = $this->subscriptionRepository->update($subscription, [
            'plan' => $plan,
            'expired_at' => $expiredAt,
        ]);

        $this->billingLogService->logSubscriptionUpgrade($userId, $plan, $expiredAt);

        return $updated;
    }
}
