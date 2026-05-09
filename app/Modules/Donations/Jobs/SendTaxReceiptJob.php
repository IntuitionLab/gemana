<?php

namespace App\Modules\Donations\Jobs;

use App\Modules\Donations\Models\TaxReceipt;
use App\Modules\Donations\Mail\TaxReceiptMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SendTaxReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public TaxReceipt $receipt,
    ) {}

    public function handle(): void
    {
        // 1. Generate PDF
        $pdf  = Pdf::loadView('donations::receipt-pdf', ['receipt' => $this->receipt]);
        $path = 'receipts/' . $this->receipt->financial_year . '/' . $this->receipt->receipt_number . '.pdf';

        Storage::disk('private')->put($path, $pdf->output());

        // 2. Store path on receipt
        $this->receipt->update(['pdf_path' => $path]);

        // 3. Email if we have a donor email
        $email = $this->receipt->donor_email;

        if ($email) {
            Mail::to($email)->send(new TaxReceiptMail($this->receipt));

            $this->receipt->update([
                'emailed_at' => now(),
                'emailed_to' => $email,
            ]);
        }

        Log::info('Tax receipt issued', [
            'receipt_number' => $this->receipt->receipt_number,
            'emailed_to'     => $email ?? 'none',
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendTaxReceiptJob failed', [
            'receipt_id' => $this->receipt->id,
            'error'      => $e->getMessage(),
        ]);
    }
}
