<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\CustomerService;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly CustomerService $customerService,
        private readonly PlanService $planService,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $plan = $this->planService->normalizePlan(getUserPlan($user));
        $trialDaysLeft = getRemainingDays($user);
        $servicesCount = $user?->services()->count() ?? 0;
        $planDetail = $this->planService->detail($plan);
        $bookingUsage = $this->subscriptionService->getBookingUsage((int) ($user?->id ?? 0));

        return view('admin.dashboard', [
            'totalBookings' => $this->bookingService->getTotalBookings(),
            'totalCustomers' => $this->customerService->getTotalCustomers(),
            'totalRevenue' => $this->bookingService->getTotalRevenue(),
            'upcomingBookings' => $this->bookingService->getUpcomingBookings(5),
            'plan' => $plan,
            'planDetail' => $planDetail,
            'trialDaysLeft' => $trialDaysLeft,
            'trialExpiry' => $user?->subscription?->expired_at,
            'bookingUsage' => $bookingUsage,
            'servicesCount' => $servicesCount,
        ]);
    }
}
