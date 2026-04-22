<?php

namespace App\Services;

use App\Models\PublicBookingForm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PublicBookingFormService
{
    public function listForTenant(int $tenantId): Collection
    {
        return PublicBookingForm::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->latest()
            ->get();
    }

    public function createForTenant(int $tenantId, array $serviceIds, ?int $maxSubmissions = null): PublicBookingForm
    {
        return PublicBookingForm::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId,
            'token' => Str::random(48),
            'expires_at' => now()->addHours(48),
            'is_active' => true,
            'settings' => [
                'service_ids' => array_values(array_unique($serviceIds)),
            ],
            'max_submissions' => $maxSubmissions,
            'submission_count' => 0,
        ]);
    }

    public function findAccessibleByToken(string $token): ?PublicBookingForm
    {
        $form = PublicBookingForm::withoutGlobalScopes()
            ->where('token', $token)
            ->first();

        if ($form === null) {
            return null;
        }

        if (! $form->isAccessible()) {
            return null;
        }

        return $form;
    }

    public function findByToken(string $token): ?PublicBookingForm
    {
        return PublicBookingForm::withoutGlobalScopes()
            ->where('token', $token)
            ->first();
    }

    public function incrementSubmission(PublicBookingForm $form): PublicBookingForm
    {
        $form->submission_count++;
        if ($form->max_submissions !== null && $form->submission_count >= $form->max_submissions) {
            $form->is_active = false;
        }
        $form->save();

        return $form->refresh();
    }

    public function deactivate(PublicBookingForm $form): void
    {
        $form->is_active = false;
        $form->save();
    }

    public function extendBy48Hours(PublicBookingForm $form): PublicBookingForm
    {
        $base = $form->expires_at->isFuture() ? $form->expires_at : now();
        $form->expires_at = $base->copy()->addHours(48);
        $form->is_active = true;
        $form->save();

        return $form->refresh();
    }

    public function getAllowedServiceIds(PublicBookingForm $form): array
    {
        $ids = $form->settings['service_ids'] ?? [];

        return array_values(array_map('intval', array_filter($ids, fn ($id) => is_numeric($id))));
    }

    public function isExpired(PublicBookingForm $form): bool
    {
        return Carbon::parse($form->expires_at)->isPast();
    }
}
