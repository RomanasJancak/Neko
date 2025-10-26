@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Invoice Details</h1>
  <div class="card">
    <div class="card-header">
      Invoice #{{ $invoice->id }}
    </div>
    <div class="card-body">
      <h5 class="card-title">Customer: {{ $invoice->customer_name }}</h5>
      <p class="card-text">Date: {{ $invoice->date }}</p>
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
</div>
@endsection