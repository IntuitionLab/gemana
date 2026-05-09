<?php

namespace App\Modules\Donations\Mail;

use App\Modules\Donations\Models\TaxReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TaxReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TaxReceipt $receipt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Tax Receipt — ' . $this->receipt->receipt_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'donations::receipt-email',
        );
    }

    public function attachments(): array
    {
        if (! $this->receipt->hasPdf()) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('private', $this->receipt->pdf_path)
                ->as($this->receipt->receipt_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
