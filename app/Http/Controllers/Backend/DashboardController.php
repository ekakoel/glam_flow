<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenantQuery = User::query()->where('role', '!=', 'super_admin');
        $totalTenants = (clone $tenantQuery)->count();
        $activeSubscribers = Subscription::query()->whereIn('plan', ['pro', 'premium'])->count();
        $freeSubscribers = Subscription::query()->where('plan', 'free')->count();
        $planBreakdown = Subscription::query()
            ->selectRaw('plan, COUNT(*) as total')
            ->groupBy('plan')
            ->orderBy('plan')
            ->get();

        return view('backend.dashboard', [
            'totalTenants' => $totalTenants,
            'activeSubscribers' => $activeSubscribers,
            'freeSubscribers' => $freeSubscribers,
            'planBreakdown' => $planBreakdown,
        ]);
    }
}

