@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Invoice Details</h1>
  <div class="card">
    <div class="card-header">
      Invoice #{{ $invoice->id }}<br>
      <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
        @csrf
            @method('patch')
        <label for="invoice_number">Invoice Number:</label>
        <input onchange="this.form.submit()" class="form-control" type="text" id="invoice_number" name="invoice_number" value="{{ $invoice->invoice_number }}">
      </form>
      <form action="{{ route('invoice.snapshots.generate', $invoice->id) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm">Generate Snapshot</button>
      </form>
      <a href="#snapshots" class="btn btn-outline-secondary btn-sm mt-2">View Snapshots</a>
    </div>
    <div class="card-body">
      <h5 class="card-title">Customer: {{ $invoice->client->name }}</h5>
      <p class="card-text">Date: {{ $invoice->invoice_date }}</p>
      <p class="card-text">Total Amount: ${{ number_format($invoice->total, 2) }}</p>
    </div>
  </div>

  <h3 class="mt-4">Items</h3>
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Id</th>
        <th>Description</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($invoice->items->take(3) as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->id }}</td>
          <td>{{ $item->description }}</td>
          <td>{{ $item->jobs->count() }}</td>
          <td>${{ number_format($item->price, 2) }}</td>
          <td>
        <a href="{{ route('invoiceItem.show', $item->id) }}" class="btn btn-info btn-sm">View</a>
        @if($invoice->items->count() > 1)
          <form action="{{ route('invoiceItem.destroy', $item->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
          </form>
        @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <a href="{{ route('invoice.index') }}" class="btn btn-primary mt-3">Back to Invoices</a>

  <h3 id="snapshots" class="mt-5">Snapshots</h3>
  @if($invoice->snapshots->isEmpty())
    <p>No snapshots yet.</p>
  @else
    <table class="table">
      <thead>
        <tr>
          <th>Version</th>
          <th>Generated At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($invoice->snapshots->sortByDesc('version') as $snapshot)
          <tr>
            <td>{{ $snapshot->version }}</td>
            <td>{{ $snapshot->generated_at }}</td>
            <td>
              <a href="{{ route('invoice.viewPDF', ['invoice' => $invoice->id, 'snapshot_id' => $snapshot->id]) }}" class="btn btn-secondary btn-sm" target="_blank">View PDF</a>
              <a href="{{ route('invoice.viewPDF', ['invoice' => $invoice->id, 'snapshot_id' => $snapshot->id, 'download' => 1]) }}" class="btn btn-success btn-sm">Download Invoice</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection