<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()
            ->loadCount(['services', 'customers', 'bookings'])
            ->load('paymentAccounts');
        $planKey = $this->planService->normalizePlan(getUserPlan($user));
        $bookingUsage = $this->subscriptionService->getBookingUsage((int) $user->id);

        return view('profile.edit', [
            'user' => $user,
            'planKey' => $planKey,
            'planDetail' => $this->planService->detail($planKey),
            'bookingUsage' => $bookingUsage,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $removeLogo = (bool) ($validated['remove_logo'] ?? false);
        $paymentAccounts = (array) ($validated['payment_accounts'] ?? []);
        $primaryAccountIndex = array_key_exists('primary_account_index', $validated)
            ? (int) $validated['primary_account_index']
            : null;

        unset(
            $validated['logo'],
            $validated['remove_logo'],
            $validated['payment_accounts'],
            $validated['primary_account_index']
        );

        DB::transaction(function () use (
            $request,
            $user,
            $validated,
            $removeLogo,
            $paymentAccounts,
            $primaryAccountIndex
        ): void {
            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            if ($removeLogo && $user->logo_path) {
                Storage::disk('public')->delete($user->logo_path);
                $user->logo_path = null;
            }

            if ($request->hasFile('logo')) {
                if ($user->logo_path) {
                    Storage::disk('public')->delete($user->logo_path);
                }

                $user->logo_path = $request->file('logo')->store(
                    'tenants/'.$user->id.'/branding',
                    'public'
                );
            }

            $normalizedAccounts = collect($paymentAccounts)
                ->map(function (array $row, int $index): array {
                    return [
                        'bank_name' => trim((string) ($row['bank_name'] ?? '')),
                        'account_number' => trim((string) ($row['account_number'] ?? '')),
                        'account_name' => trim((string) ($row['account_name'] ?? '')),
                        'contact' => trim((string) ($row['contact'] ?? '')),
                        'notes' => trim((string) ($row['notes'] ?? '')),
                        'sort_order' => $index,
                    ];
                })
                ->filter(fn (array $row): bool => $row['bank_name'] !== '' && $row['account_number'] !== '' && $row['account_name'] !== '')
                ->values();

            $validPrimaryIndex = $primaryAccountIndex !== null
                && $primaryAccountIndex >= 0
                && $primaryAccountIndex < $normalizedAccounts->count()
                ? $primaryAccountIndex
                : null;

            $user->paymentAccounts()->delete();

            $normalizedAccounts->each(function (array $row, int $index) use ($user, $validPrimaryIndex): void {
                $user->paymentAccounts()->create([
                    'bank_name' => $row['bank_name'],
                    'account_number' => $row['account_number'],
                    'account_name' => $row['account_name'],
                    'contact' => $row['contact'] !== '' ? $row['contact'] : null,
                    'notes' => $row['notes'] !== '' ? $row['notes'] : null,
                    'is_primary' => $validPrimaryIndex !== null ? $index === $validPrimaryIndex : $index === 0,
                    'sort_order' => $index,
                ]);
            });

            $primaryAccount = $user->paymentAccounts()->where('is_primary', true)->first();

            $user->payment_bank_name = $primaryAccount?->bank_name;
            $user->payment_account_number = $primaryAccount?->account_number;
            $user->payment_account_name = $primaryAccount?->account_name;
            $user->payment_contact = $primaryAccount?->contact;

            $user->save();
        });

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->logo_path) {
            Storage::disk('public')->delete($user->logo_path);
        }

        $user->paymentAccounts()->delete();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
