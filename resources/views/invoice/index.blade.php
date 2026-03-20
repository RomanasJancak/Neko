@extends('layouts.app')

@section('content')
<div class="container">
  <h1 class="mb-4">Invoices</h1>
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <!-- <div class="mb-3">
    <a href="{{ route('invoice.create') }}" class="btn btn-primary">Create New Invoice</a>
  </div>  -->
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
              <a href="{{ route('invoice.viewPDF', $invoice->id) }}" class="btn btn-secondary btn-sm" target="_blank">View PDF</a>
              <a href="{{ route('invoice.viewPDF', ['invoice' => $invoice->id, 'download' => 1]) }}" class="btn btn-success btn-sm">Download Invoice</a>
              @if(!empty(optional($invoice->client)->email))
                <button
                  type="button"
                  class="btn btn-warning btn-sm js-open-send-email-modal"
                  data-send-url="{{ route('invoice.sendEmail', $invoice->id) }}"
                  data-client-email="{{ $invoice->client->email }}"
                  data-invoice-number="{{ $invoice->invoice_number }}"
                  data-subject="{{ base64_encode($invoice->email_subject_prefill) }}"
                  data-body="{{ base64_encode($invoice->email_body_prefill) }}"
                >
                  Send
                </button>
              @else
                <button type="button" class="btn btn-warning btn-sm" disabled title="Client email is missing">Send</button>
              @endif
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

<div class="modal fade" id="sendInvoiceEmailModal" tabindex="-1" aria-labelledby="sendInvoiceEmailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="sendInvoiceEmailForm" method="POST" action="">
        @csrf
        <input type="hidden" name="snapshot_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="sendInvoiceEmailLabel">Send Invoice Email</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="invoiceEmailTo" class="form-label">To</label>
            <input type="email" class="form-control" id="invoiceEmailTo" readonly>
          </div>

          <div class="mb-3">
            <label for="invoiceEmailSubject" class="form-label">Title</label>
            <input type="text" class="form-control" id="invoiceEmailSubject" name="subject" required maxlength="255">
          </div>

          <div class="mb-3">
            <label for="invoiceEmailBody" class="form-label">Body</label>
            <textarea class="form-control" id="invoiceEmailBody" name="body" rows="10" required></textarea>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" value="1" id="saveTemplateCheckbox" name="save_template" checked>
            <label class="form-check-label" for="saveTemplateCheckbox">
              Save this title and body as this client's template
            </label>
          </div>

          <small class="text-muted">PDF attachment is generated from Invoice "View PDF" and attached automatically.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Send Email</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('sendInvoiceEmailModal');
    const sendButtons = document.querySelectorAll('.js-open-send-email-modal');
    const form = document.getElementById('sendInvoiceEmailForm');
    const toField = document.getElementById('invoiceEmailTo');
    const subjectField = document.getElementById('invoiceEmailSubject');
    const bodyField = document.getElementById('invoiceEmailBody');
    const modalTitle = document.getElementById('sendInvoiceEmailLabel');

    if (!modalElement || !form || !toField || !subjectField || !bodyField || !modalTitle) {
      return;
    }

    const modal = new bootstrap.Modal(modalElement);

    sendButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        const sendUrl = button.getAttribute('data-send-url') || '';
        const email = button.getAttribute('data-client-email') || '';
        const invoiceNumber = button.getAttribute('data-invoice-number') || '';
        const subjectBase64 = button.getAttribute('data-subject') || '';
        const bodyBase64 = button.getAttribute('data-body') || '';
        const subject = subjectBase64 ? atob(subjectBase64) : ('Invoice ' + invoiceNumber);
        const body = bodyBase64 ? atob(bodyBase64) : '';

        form.setAttribute('action', sendUrl);
        toField.value = email;
        subjectField.value = subject;
        bodyField.value = body;
        modalTitle.textContent = 'Send Invoice Email - ' + invoiceNumber;

        modal.show();
      });
    });
  });
</script>
@endpush
@endsection