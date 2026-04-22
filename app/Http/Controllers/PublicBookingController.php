<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPublicBookingRequest;
use App\Models\Service;
use App\Models\User;
use App\Services\PublicBookingFormService;
use App\Services\PublicBookingSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class PublicBookingController extends Controller
{
    public function __construct(
        private readonly PublicBookingFormService $publicBookingFormService,
        private readonly PublicBookingSubmissionService $publicBookingSubmissionService
    ) {
    }

    public function show(string $token): View
    {
        $form = $this->publicBookingFormService->findByToken($token);
        if ($form === null || ! $form->isAccessible()) {
            return view('public-booking.expired');
        }

        $services = Service::withoutGlobalScopes()
            ->where('tenant_id', $form->tenant_id)
            ->whereIn('id', $this->publicBookingFormService->getAllowedServiceIds($form))
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'duration']);

        if ($services->isEmpty()) {
            return view('public-booking.expired', [
                'message' => 'No services are available for this booking link.',
            ]);
        }

        return view('public-booking.form', [
            'form' => $form,
            'services' => $services,
            'token' => $token,
            'tenant' => User::query()->find($form->tenant_id),
        ]);
    }

    public function store(SubmitPublicBookingRequest $request, string $token): RedirectResponse
    {
        try {
            $this->publicBookingSubmissionService->submit($token, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['booking' => $exception->getMessage()]);
        }

        return redirect()
            ->route('public.booking.show', $token)
            ->with('success', 'Permintaan booking berhasil dikirim.');
    }
}
