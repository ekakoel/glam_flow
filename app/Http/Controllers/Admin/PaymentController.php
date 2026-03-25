<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
    }

    public function index(): View
    {
        return view('admin.payments.index', [
            'payments' => $this->paymentService->paginate(),
        ]);
    }

    public function update(UpdatePaymentStatusRequest $request, int $payment): RedirectResponse
    {
        $model = $this->paymentService->findOrFail($payment);
        $this->paymentService->updateStatus($model, $request->validated());

        return back()->with('success', 'Payment updated successfully.');
    }

    public function markAsPaid(int $payment): RedirectResponse
    {
        $model = $this->paymentService->findOrFail($payment);
        $this->paymentService->markAsPaid($model, Payment::METHOD_MANUAL);

        return back()->with('success', 'Payment marked as paid.');
    }
}
