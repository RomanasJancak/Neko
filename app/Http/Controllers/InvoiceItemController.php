<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Http\Requests\StoreInvoiceItemRequest;
use App\Http\Requests\UpdateInvoiceItemRequest;

class InvoiceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreInvoiceItemRequest $request)
    {
      if($request->has('invoice_id')){
        $invoiceItem = new InvoiceItem();
        $invoiceItem->invoice_id = $request->input('invoice_id');
        $invoiceItem->price = $request->input('price', 0);
        $invoiceItem->description = $request->input('description', '');
        $invoiceItem->job_id = $request->input('job_id', null);
        $invoiceItem->save(); 
        return redirect()->route('invoiceItems.show', $invoiceItem);
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceItem $invoiceItem)
    {
        //dd($invoiceItem);
        $invoiceItem->load('jobs'); // eager load jobs relation
        return view('invoiceItem.show', compact('invoiceItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceItem $invoiceItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceItemRequest $request, InvoiceItem $invoiceItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceItem $invoiceItem)
    {
      try{
        if($invoiceItem->jobs()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete Invoice Item with associated Jobs.');
        }
        $invoiceItem->delete();
        return redirect()->back()->with('success', 'Invoice deleted successfully.');
      }catch(\Exception $e){
          return redirect()->back()->with('error', 'Invoices cannot be deleted.');
      }
    }
}
