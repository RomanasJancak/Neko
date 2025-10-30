<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::latest()->paginate(10);
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
        foreach($invoice->invoiceItems as $item){
          if($item->jobs()->count() > 0) {
              return redirect()->route('invoice.index')->with('error', 'Cannot delete Invoice with associated Jobs.');
          }
          $item->delete();
        }
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice deleted successfully.');
      }catch(\Exception $e){
        return redirect()->route('invoice.index')->with('error', 'Invoices cannot be deleted.');
      }
    }
    public function viewPDF(Invoice $invoice)
    {
        // $pdf = \PDF::loadView('invoice.pdf', compact('invoice'));
        // return $pdf->stream('invoice_' . $invoice->invoice_number . '.pdf');
        return view('invoice.pdf', compact('invoice'));
    }
}
