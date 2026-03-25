<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
    }

    public function index(): View
    {
        $summary = $this->reportService->getDashboardMetrics();

        return view('admin.reports.index', [
            'totalRevenue' => $summary['total_revenue'],
            'monthlyRevenue' => $summary['monthly_revenue'],
            'paidCount' => $summary['paid_count'],
            'pendingCount' => $summary['pending_count'],
            'conversionRate' => $summary['conversion_rate'],
            'totalBookings' => $summary['total_bookings'],
        ]);
    }
}
