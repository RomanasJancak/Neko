@extends('layouts.app')

@section('content')
<div class="container">
  <h1 class="mb-4">Invoices</h1>
  <div class="mb-3">
    <a href="{{ route('invoice.create') }}" class="btn btn-primary">Create New Invoice</a>
  </div>
  @if($invoices->isEmpty())
    <p>No invoices found.</p>
  @else
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>ID</th>
          <th>Invoice Number</th>
          <th>Customer</th>
          <th>Lines</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoices as $invoice)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $invoice->id }}</td>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->client->name }}</td>
            <td>
            @foreach($invoice->invoiceItems as $item)
              {{ $item->description }}<br>
            @endforeach
            </td>
            <td>${{ number_format($invoice->total, 2) }}</td>
            <td>{{ $invoice->invoice_date}}</td>
            <td>
              <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-info btn-sm">View</a>
              <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-warning btn-sm">Edit</a>
              <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    {{ $invoices->links() }}
  @endif
</div>
@endsection