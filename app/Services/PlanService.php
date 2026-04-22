<?php

namespace App\Services;

use App\Models\PlanOverride;
use App\Models\Subscription;
use Illuminate\Support\Facades\Schema;

class PlanService
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $plans = (array) config('plans.plans', []);

        foreach ($this->overrides() as $planKey => $override) {
            if (! array_key_exists($planKey, $plans)) {
                continue;
            }

            $plans[$planKey] = array_merge($plans[$planKey], $override);
        }

        return $plans;
    }

    public function defaultPlan(): string
    {
        $default = (string) config('plans.default', Subscription::PLAN_FREE);

        return $this->isKnownPlan($default) ? $default : Subscription::PLAN_FREE;
    }

    public function trialDays(): int
    {
        return max(0, (int) config('plans.trial_days', 7));
    }

    public function normalizePlan(?string $plan): string
    {
        $value = strtolower(trim((string) $plan));

        return $this->isKnownPlan($value) ? $value : $this->defaultPlan();
    }

    public function isKnownPlan(?string $plan): bool
    {
        if ($plan === null) {
            return false;
        }

        return array_key_exists(strtolower($plan), $this->all());
    }

    /**
     * @return array<string, mixed>
     */
    public function detail(?string $plan): array
    {
        $normalized = $this->normalizePlan($plan);
        $all = $this->all();
        $detail = (array) ($all[$normalized] ?? []);

        $limit = $detail['booking_limit_total'] ?? null;
        $isUnlimited = $limit === null;
        $limitLabel = $isUnlimited
            ? 'Booking tanpa batas'
            : sprintf('Maksimal %d booking total', (int) $limit);

        $detail['key'] = $normalized;
        $detail['booking_limit_total'] = $isUnlimited ? null : max(0, (int) $limit);
        $detail['is_unlimited'] = $isUnlimited;
        $detail['booking_limit_label'] = $limitLabel;
        $detail['cta_label'] = sprintf('Pilih %s', $detail['name'] ?? ucfirst($normalized));

        return $detail;
    }

    /**
     * @return array<int, string>
     */
    public function allowedPlans(): array
    {
        return array_keys($this->all());
    }

    public function totalBookingLimit(?string $plan): ?int
    {
        $detail = $this->detail($plan);

        return $detail['booking_limit_total'];
    }

    public function canAccessFeature(?string $plan, string $feature): bool
    {
        $detail = $this->detail($plan);
        $flags = (array) ($detail['feature_flags'] ?? []);

        return (bool) ($flags[$feature] ?? false);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function overrides(): array
    {
        if (! Schema::hasTable('plan_overrides')) {
            return [];
        }

        return PlanOverride::query()
            ->get()
            ->keyBy(fn (PlanOverride $override) => strtolower((string) $override->plan_key))
            ->map(function (PlanOverride $override): array {
                return array_filter([
                    'name' => $override->name,
                    'price' => $override->price,
                    'billing_cycle' => $override->billing_cycle,
                    'booking_limit_total' => $override->booking_limit_total,
                    'benefit' => $override->benefit,
                    'features' => $override->features,
                    'feature_flags' => $override->feature_flags,
                ], static fn (mixed $value): bool => $value !== null);
            })
            ->all();
    }
}
