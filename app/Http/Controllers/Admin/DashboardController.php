<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\CustomerService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly CustomerService $customerService
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();
        $plan = getUserPlan($user);
        $trialDaysLeft = getRemainingDays($user);
        $servicesCount = $user?->services()->count() ?? 0;

        return view('admin.dashboard', [
            'totalBookings' => $this->bookingService->getTotalBookings(),
            'totalCustomers' => $this->customerService->getTotalCustomers(),
            'totalRevenue' => $this->bookingService->getTotalRevenue(),
            'upcomingBookings' => $this->bookingService->getUpcomingBookings(5),
            'plan' => $plan,
            'trialDaysLeft' => $trialDaysLeft,
            'trialExpiry' => $user?->subscription?->expired_at,
            'servicesCount' => $servicesCount,
        ]);
    }
}
