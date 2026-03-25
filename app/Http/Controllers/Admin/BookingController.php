<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use App\Services\CustomerService;
use App\Services\InvoiceService;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly CustomerService $customerService,
        private readonly ServiceService $serviceService
    ) {
    }

    public function index(): View
    {
        return view('admin.bookings.index', [
            'bookings' => $this->bookingService->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('admin.bookings.create', [
            'customers' => $this->customerService->allForSelect(),
            'services' => $this->serviceService->allForSelect(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $this->bookingService->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['booking_time' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking created successfully.');
    }

    public function edit(int $booking): View
    {
        return view('admin.bookings.edit', [
            'booking' => $this->bookingService->findOrFail($booking),
            'customers' => $this->customerService->allForSelect(),
            'services' => $this->serviceService->allForSelect(),
        ]);
    }

    public function update(StoreBookingRequest $request, int $booking): RedirectResponse
    {
        $model = $this->bookingService->findOrFail($booking);

        try {
            $this->bookingService->update($model, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['booking_time' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    public function show(int $booking): View
    {
        return view('admin.bookings.show', [
            'booking' => $this->bookingService->findOrFail($booking),
        ]);
    }

    public function destroy(int $booking): RedirectResponse
    {
        $model = $this->bookingService->findOrFail($booking);
        $this->bookingService->delete($model);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function invoice(int $booking, InvoiceService $invoiceService): Response
    {
        $model = $this->bookingService->findOrFail($booking);

        return $invoiceService->downloadBookingInvoice($model);
    }

    public function payNow(int $booking): RedirectResponse
    {
        $model = $this->bookingService->findOrFail($booking);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', "Pay Now placeholder for booking #{$model->id}. Midtrans integration ready for next step.");
    }
}
