<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceSendMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Invoice $invoice;
    public string $emailBody;
    protected string $emailSubject;
    protected string $pdfContent;
    protected string $pdfFileName;

    public function __construct(Invoice $invoice, string $emailSubject, string $emailBody, string $pdfContent, string $pdfFileName)
    {
        $this->invoice = $invoice;
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
        $this->pdfContent = $pdfContent;
        $this->pdfFileName = $pdfFileName;
    }

    public function build(): self
    {
        return $this->subject($this->emailSubject)
            ->view('emails.invoice-send')
            ->with([
                'invoice' => $this->invoice,
                'emailBody' => $this->emailBody,
            ])
            ->attachData($this->pdfContent, $this->pdfFileName, [
                'mime' => 'application/pdf',
            ]);
    }
}
