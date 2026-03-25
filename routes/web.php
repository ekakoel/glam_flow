<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'home'])->name('landing.home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('landing.pricing');
Route::get('/features', [LandingController::class, 'features'])->name('landing.features');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'subscription'])->name('dashboard');

Route::middleware(['auth', 'subscription'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
    Route::patch('/calendar/events/{booking}/reschedule', [CalendarController::class, 'reschedule'])->name('calendar.reschedule');
    Route::resource('services', ServiceController::class)->except('show');
    Route::resource('customers', CustomerController::class)->except('show');
    Route::get('/bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
    Route::post('/bookings/{booking}/pay-now', [BookingController::class, 'payNow'])->name('bookings.pay-now');
    Route::resource('bookings', BookingController::class);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::patch('/payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::middleware('super_admin')->group(function () {
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/profile', [OnboardingController::class, 'updateProfile'])->name('onboarding.profile');
    Route::post('/onboarding/service', [OnboardingController::class, 'storeFirstService'])->name('onboarding.service');
    Route::post('/onboarding/booking', [OnboardingController::class, 'storeFirstBooking'])->name('onboarding.booking');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
