<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Services\BookingService;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class OnboardingController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
        private readonly BookingService $bookingService
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $servicesCount = $user->services()->count();
        $bookingsCount = $user->bookings()->count();
        $plan = getUserPlan($user);
        $trialDaysLeft = getRemainingDays($user);
        $planBenefits = [
            'free' => 'Perfect to start. Up to 10 bookings/month with core features.',
            'pro' => 'Best for growing MUA business. Unlimited bookings and smoother operations.',
            'premium' => 'Advanced plan with priority support and deeper analytics.',
        ];

        return view('onboarding', [
            'plan' => $plan,
            'planBenefit' => $planBenefits[$plan] ?? $planBenefits['free'],
            'trialDaysLeft' => $trialDaysLeft,
            'trialExpiry' => $user->subscription?->expired_at,
            'servicesCount' => $servicesCount,
            'bookingsCount' => $bookingsCount,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profile updated.');
    }

    public function storeFirstService(StoreServiceRequest $request): RedirectResponse
    {
        $this->serviceService->create($request->validated());

        return back()->with('success', 'First service created.');
    }

    public function storeFirstBooking(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $this->bookingService->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['booking_time' => $exception->getMessage()]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Onboarding completed.');
    }
}
