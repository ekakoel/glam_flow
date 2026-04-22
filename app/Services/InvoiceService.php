<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{
    public function downloadBookingInvoice(Booking $booking): Response
    {
        [$pdf, $invoiceNumber] = $this->buildPdf($booking);

        return $pdf->download($invoiceNumber.'.pdf');
    }

    public function previewBookingInvoice(Booking $booking): Response
    {
        [$pdf, $invoiceNumber] = $this->buildPdf($booking);

        return $pdf->stream($invoiceNumber.'.pdf');
    }

    /**
     * @return array{0:mixed,1:string}
     */
    private function buildPdf(Booking $booking): array
    {
        $invoiceNumber = sprintf('INV-%s-%04d', now()->format('Ymd'), $booking->id);
        $booking = $booking->loadMissing([
            'customer',
            'service',
            'payment',
            'bookingItems.service',
            'tenant.paymentAccounts',
        ]);

        $pdf = Pdf::loadView('admin.invoices.booking', [
            'invoiceNumber' => $invoiceNumber,
            'booking' => $booking,
            'tenantLogoDataUri' => $this->tenantLogoDataUri($booking),
            'tenantPaymentAccounts' => $booking->tenant?->paymentAccounts ?? collect(),
        ]);

        return [$pdf, $invoiceNumber];
    }

    private function tenantLogoDataUri(Booking $booking): ?string
    {
        $logoPath = (string) ($booking->tenant?->logo_path ?? '');
        if ($logoPath === '') {
            return null;
        }

        $absolutePath = storage_path('app/public/'.$logoPath);
        if (! File::exists($absolutePath)) {
            return null;
        }

        $binary = File::get($absolutePath);
        $mimeType = File::mimeType($absolutePath) ?: 'image/png';

        return 'data:'.$mimeType.';base64,'.base64_encode($binary);
    }
}
