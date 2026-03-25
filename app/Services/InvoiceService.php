<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{
    public function downloadBookingInvoice(Booking $booking): Response
    {
        $invoiceNumber = sprintf('INV-%s-%04d', now()->format('Ymd'), $booking->id);

        $pdf = Pdf::loadView('admin.invoices.booking', [
            'invoiceNumber' => $invoiceNumber,
            'booking' => $booking->loadMissing(['customer', 'service', 'payment', 'bookingItems.service']),
        ]);

        return $pdf->download($invoiceNumber.'.pdf');
    }
}
