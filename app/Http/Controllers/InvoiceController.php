<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSendMail;
use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Services\SettingsService;
use App\Services\InvoiceSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $invoices = Invoice::with(['client', 'invoiceItems', 'sentByUser'])->latest()->paginate(10);

      $invoices->getCollection()->transform(function (Invoice $invoice) {
        $invoice->email_subject_prefill = $this->renderTemplate(
          $invoice->client->invoice_email_subject_template ?? null,
          $invoice,
          'Invoice :invoice_number'
        );

        $invoice->email_body_prefill = $this->renderTemplate(
          $invoice->client->invoice_email_body_template ?? null,
          $invoice,
          "Hello :client_name,\n\nPlease find attached invoice :invoice_number.\n\nThank you."
        );

        return $invoice;
      });

      return view('invoice.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
      $invoice->load('snapshots');
      return view('invoice.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
      try{
        if ($invoice->isLockedForUser(auth()->user())) {
          return redirect()->route('invoice.show', $invoice->id)->with('error', 'This invoice is locked and cannot be updated.');
        }

        $validated = $request->validate([
          'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
        ]);
        $invoice->update($validated);
        return redirect()->route('invoice.show', $invoice->id)->with('success', 'Invoice updated successfully.');
      }catch(\Exception $e){
        return redirect()->route('invoice.show', $invoice->id)->with('error', 'Invoice could not be updated. ' . $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
      try{
        if ($invoice->isLockedForUser(auth()->user())) {
          return redirect()->route('invoice.index')->with('error', 'This invoice is locked and cannot be deleted.');
        }

        foreach($invoice->invoiceItems as $item){
          if($item->jobs()->count() > 0) {
              return redirect()->route('invoice.index')->with('error', 'Cannot delete Invoice with associated Jobs.');
          }
          $item->delete();
        }
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice deleted successfully.');
      }catch(\Exception $e){
        return redirect()->route('invoice.index')->with('error', 'Invoices cannot be deleted. ' . $e->getMessage());
      }
    
    }
    public function viewPDF(Request $request, Invoice $invoice, SettingsService $settings)
    {
        // $pdf = \PDF::loadView('invoice.pdf', compact('invoice'));
        // return $pdf->stream('invoice_' . $invoice->invoice_number . '.pdf');
      $vatRate = (float) $settings->get('global.vatRate');
      $snapshotService = app(InvoiceSnapshotService::class);
      $snapshotId = $request->query('snapshot_id');

      if ($snapshotId) {
        $snapshot = $invoice->snapshots()->whereKey($snapshotId)->firstOrFail();
      } else {
        $snapshot = $snapshotService->createSnapshot($invoice, $vatRate, auth()->id());
      }

      $snapshotData = $snapshot->data;
      $snapshotData['version'] = $snapshot->version;
      $snapshotData['generated_at'] = $snapshot->generated_at;

      $viewData = [
        'invoice' => $invoice,
        'snapshot' => $snapshot,
        'snapshotData' => $snapshotData,
      ];

      $fileName = 'invoice_' . ($snapshotData['invoice']['invoice_number'] ?? $invoice->id) . '_v' . $snapshot->version . '.pdf';
      $pdf = Pdf::loadView('invoice.pdf', $viewData);

      if ($request->boolean('download')) {
        return $pdf->download($fileName);
      }

      return $pdf->stream($fileName);
    }

    public function generateSnapshot(Request $request, Invoice $invoice, SettingsService $settings)
    {
      $vatRate = (float) $settings->get('global.vatRate');
      $snapshotService = app(InvoiceSnapshotService::class);
      $snapshot = $snapshotService->createSnapshot($invoice, $vatRate, auth()->id());

      return redirect()->route('invoice.viewPDF', [
        'invoice' => $invoice->id,
        'snapshot_id' => $snapshot->id,
      ])->with('success', 'Invoice snapshot generated.');
    }

    public function sendEmail(Request $request, Invoice $invoice, SettingsService $settings)
    {
      $invoice->loadMissing('client');

      if (!$invoice->canBeSent()) {
        return redirect()->route('invoice.index')->with('error', 'Client email does not exist for this invoice.');
      }

      $validated = $request->validate([
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'save_template' => 'nullable|boolean',
        'snapshot_id' => 'nullable|integer',
      ]);

      $vatRate = (float) $settings->get('global.vatRate');
      $snapshotService = app(InvoiceSnapshotService::class);
      $snapshotId = $validated['snapshot_id'] ?? null;

      if ($snapshotId) {
        $snapshot = $invoice->snapshots()->whereKey($snapshotId)->first();
      } else {
        $snapshot = $snapshotService->createSnapshot($invoice, $vatRate, auth()->id());
      }

      if (!$snapshot) {
        return redirect()->route('invoice.index')->with('error', 'Invalid snapshot selected for invoice email.');
      }

      $snapshotData = $snapshot->data;
      $snapshotData['version'] = $snapshot->version;
      $snapshotData['generated_at'] = $snapshot->generated_at;

      $viewData = [
        'invoice' => $invoice,
        'snapshot' => $snapshot,
        'snapshotData' => $snapshotData,
      ];

      $pdfContent = Pdf::loadView('invoice.pdf', $viewData)->output();
      $pdfFileName = 'invoice_' . ($snapshotData['invoice']['invoice_number'] ?? $invoice->id) . '_v' . $snapshot->version . '.pdf';

      $clientEmail = trim((string) $invoice->getInvoiceEmail());
      $clientEmail = "invoice.nekohomedelivery@gmail.com";
      /*
      // Accept comma/semicolon separated CC emails from settings and keep only valid addresses.
      $rawCc = (string) ($settings->get('global.cc_email') ?? '');
      $ccEmails = preg_split('/[;,]+/', $rawCc) ?: [];
      $ccEmails = array_values(array_filter(array_map('trim', $ccEmails), function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
      }));
      //$clientEmail = 'jamieholloway477@gmail.com';
      
      
      $ccEmails = [
        //'romanas.jancak@gmail.com',
        'jamieholloway477@gmail.com'];
      if (!empty($ccEmails)) {
        $mailMessage->cc($ccEmails);
      }
      */
      $mailMessage = Mail::to($clientEmail);
      $mailMessage->send(
        new InvoiceSendMail(
          $invoice,
          $validated['subject'],
          $validated['body'],
          $pdfContent,
          $pdfFileName
        )
      );

      if ($request->boolean('save_template')) {
        $invoice->client->update([
          'invoice_email_subject_template' => $validated['subject'],
          'invoice_email_body_template' => $validated['body'],
        ]);
      }

      $invoice->status = 'sent';
      $invoice->sent_at = now();
      $invoice->sent_by = auth()->id();
      if (Schema::hasColumn('invoices', 'status_id')) {
        $invoice->status_id = Invoice::STATUS_SENT_ID;
      }
      $invoice->save();

      return redirect()->route('invoice.index')->with('success', 'Invoice email sent successfully.');
    }

    private function renderTemplate(?string $template, Invoice $invoice, string $fallback): string
    {
      $rawTemplate = $template ?: $fallback;
      $clientName = $invoice->client->name ?? 'Client';
      $invoiceDate = $invoice->invoice_date ?: $invoice->created_at?->toDateString();

      return strtr($rawTemplate, [
        ':invoice_number' => (string) ($invoice->invoice_number ?? $invoice->id),
        ':invoice_id' => (string) $invoice->id,
        ':client_name' => (string) $clientName,
        ':invoice_date' => (string) ($invoiceDate ?: ''),
        ':invoice_total' => number_format((float) $invoice->total, 2),
      ]);
    }
}
