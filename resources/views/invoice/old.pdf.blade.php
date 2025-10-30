<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Invoice {{$invoice->invoice_number}}</title>
  <style>
    /* Simple printable invoice style */
    :root{
      --accent:#1f6feb;
      --muted:#666;
      --border:#e6e6e6;
      --paper:#fff;
      font-family: Arial, Helvetica, sans-serif;
      color:#111;
    }
    body{
      background:#f4f6f8;
      padding:20px;
      margin:0;
    }
    .invoice {
      max-width:900px;
      margin:0 auto;
      background:var(--paper);
      padding:28px;
      border-radius:8px;
      box-shadow:0 6px 20px rgba(20,20,30,0.06);
      border:1px solid var(--border);
    }
    header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:16px;
      margin-bottom:20px;
    }
    .from {
      font-weight:700;
      font-size:18px;
    }
    .small {
      font-size:13px;
      color:var(--muted);
      line-height:1.45;
    }
    .invoice-meta {
      text-align:right;
      font-size:14px;
    }
    .invoice-meta .title{
      font-weight:700;
      font-size:20px;
      color:var(--accent);
    }
    .addresses{
      display:flex;
      gap:40px;
      margin:18px 0;
      flex-wrap:wrap;
    }
    .address {
      min-width:240px;
    }
    table.items{
      width:100%;
      border-collapse:collapse;
      margin-top:12px;
    }
    table.items th,
    table.items td{
      border-bottom:1px solid var(--border);
      padding:10px 8px;
      text-align:left;
      vertical-align:top;
      font-size:14px;
    }
    table.items th {
      background:#fafafa;
      font-weight:700;
      color:#222;
    }
    table.items td.numeric { text-align:right; font-family:monospace; }
    .totals {
      margin-top:12px;
      width:320px;
      float:right;
      border:1px solid var(--border);
      border-radius:6px;
      overflow:hidden;
    }
    .totals table {
      width:100%;
      border-collapse:collapse;
    }
    .totals td{
      padding:8px 12px;
      background:#fff;
      font-size:14px;
    }
    .totals tr:nth-child(even) td{ background:#fbfbfb; }
    .totals .grand { font-weight:700; font-size:16px; }
    .bank {
      clear:both;
      margin-top:26px;
      padding-top:12px;
      border-top:1px dashed var(--border);
      font-size:14px;
    }
    footer {
      margin-top:18px;
      font-size:13px;
      color:var(--muted);
    }

    /* Responsive */
    @media (max-width:600px){
      header{flex-direction:column; align-items:flex-start;}
      .invoice-meta { text-align:left; margin-top:8px; }
      .totals { width:100%; float:none; }
    }
  </style>
  <style>
    @page {
        margin: 25px 30px;
    }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        /* color: #ffffffff; */
    }

    .invoice-item {
        /* border: 1px solid #ccc; */
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 4px;
        page-break-before: auto;
        page-break-after: auto;
        page-break-inside: auto;
    }

    .invoice-header {
        margin-bottom: 10px;
    }

    .invoice-header h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
    }

    .invoice-header .total {
        font-weight: bold;
        font-size: 14px;
        text-align: right;
    }

    .description {
        /* color: #666; */
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 11px;
        page-break-inside: auto;
    }

    thead {
        /* background-color: #f5f5f5; */
    }

    th, td {
        /* border: 1px solid #ccc; */
        padding: 6px 8px;
        vertical-align: top;
    }

    th {
        font-weight: bold;
        text-align: left;
        color : #000000ff;
    }

    td.text-right {
        text-align: right;
    }

    .section-total {
        text-align: right;
        font-weight: bold;
        border-top: 2px solid #000;
        padding-top: 5px;
        margin-top: 10px;
        font-size: 12px;
        page-break-inside: avoid;
    }

    .small-text {
        font-size: 10px;
        /* color: #fffcfcff; */
    }

    /* --- Fix table breaking issues --- */
    table, tr, td, th {
        page-break-inside: avoid !important;
    }

    /* --- Allow invoice blocks to flow correctly --- */
    .invoice-item {
        page-break-inside: avoid;
        display: block;
    }

    /* --- Optional: add clear page breaks between invoice items --- */
    .page-break {
        page-break-before: always;
    }
</style>
</head>
<body>
  <article class="invoice" role="document" aria-label="Invoice {{$invoice->invoice_number}}">
    <header>
      <div>
        <div class="from">Neko Home Delivery LLP</div>
        <div class="small" style="margin-top:6px;">
          410 1951 42<br>
          <a href="http://www.nekohomedelivery.com" target="_blank" rel="noopener">www.nekohomedelivery.com</a><br>
          nekohomedelivery@gmail.com<br>
          07429381472
        </div>
      </div>

      <div class="invoice-meta" aria-hidden="false">
        <div class="title">INVOICE</div>
        <div style="margin-top:10px;">
          <div><strong>Invoice Number</strong> {{$invoice->invoice_number}}</div>
          <div><strong>Invoice Date</strong> {{$invoice->invoice_date}}</div>
          <div><strong>Due Date</strong> {{$invoice->due_date}}</div>
          <div style="margin-top:8px; font-size:16px;"><strong>Invoice Total</strong> £{{ number_format($invoice->total, 2) }}</div>
          <div style="color:var(--muted)"><strong>Balance Due</strong> £{{ number_format($invoice->total - $invoice->amount_paid, 2) }}</div>
        </div>
      </div>
    </header>

    <section class="addresses" aria-label="Addresses">
      <div class="address">
        <strong>Bill To</strong>
        <div style="height:8px;"></div>
        <div class="small">
          {{$invoice->client->name}}<br>
          {{$invoice->client->address_line}}<br>
          {{$invoice->client->city}} {{$invoice->client->postcode}}<br>
          {{$invoice->client->country}}<br>
          <a href="mailto:{{$invoice->client->email}}">{{$invoice->client->email}}</a>
        </div>
      </div>

      <div class="address">
        <strong>From / Supplier</strong>
        <div style="height:8px;"></div>
        <div class="small">
          Bakersfield, Crayford Road<br>
          Flat 22<br>
          London N7 0LT<br>
          United Kingdom
        </div>
      </div>
    </section>

    <section aria-label="Line items">
      <table class="items" role="table" aria-label="Invoice items">
        <thead>
          <tr>
            <th style="width:55%;">Item</th>
            <!-- <th style="width:12%;">Unit Cost</th> -->
            <th style="width:10%;">Quantity</th>
            <th style="width:23%; text-align:right;">Line Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoice->invoiceItems as $item)
          <tr>
            <td>
              {{ $item->description }}<br>
            </td>
            <td class="numeric">{{ $item->jobs->count() }}</td>
            <td class="numeric">£{{ number_format($item->price, 2) }}</td>
          </tr>
          <table>
            <thead>
              <tr>
                <th style="width:55%;"><small>Jobs</small></th>
                <th style="width:12%;"><small>Status</small></th>
                <th style="width:23%; text-align:right;"><small>Date</small></th>
                <th style="width:23%; text-align:right;"><small>Pickup</small></th>
                <th style="width:23%; text-align:right;"><small>Package</small></th>
                <th style="width:23%; text-align:right;"><small>Dropoff</small></th>
                <th style="width:23%; text-align:right;"><small>Return</small></th>
                <th style="width:23%; text-align:right;"><small>Job Total</small></th>
              </tr>
            </thead>
            <tbody>
              @foreach($item->jobs as $job)
        @php
            $pickup = $job->getPickupTask();
            $dropOffs = $job->getDropOffs();
            $dropCount = count($dropOffs);
            $returnTask = $job->hasReturn() ? $job->getReturnTask()->return : null;
        @endphp

        @foreach($dropOffs as $index => $dropOff)
            <tr>
                {{-- Only show these columns once per job --}}
                @if($index === 0)
                    <td rowspan="{{ $dropCount }}">#{{ $job->id }}</td>
                    <td rowspan="{{ $dropCount }}">{{ ucfirst($job->status->name ?? 'Unknown') }}</td>
                    <td rowspan="{{ $dropCount }}">{{ $job->date ?? 'N/A' }}</td>

                    <td rowspan="{{ $dropCount }}">
                        {{ $pickup->addressShort() ?? 'N/A' }}<br>
                        <span class="small-text">
                            {{ $pickup->timeWindowBeginFormatted() ?? 'N/A' }} – {{ $pickup->timeWindowEndFormatted() ?? 'N/A' }}
                        </span>
                    </td>
                @endif

                {{-- Dropoff details --}}
                <td>
                    <span class="small-text">
                        {{ $dropOff->packageType->name }} × {{ $dropOff->quantity }}
                    </span>
                </td>

                <td>
                    {{ $dropOff->addressShort() ?? 'N/A' }}<br>
                    <span class="small-text">
                        {{ $dropOff->timeWindowBeginFormatted() ?? 'N/A' }} – {{ $dropOff->timeWindowEndFormatted() ?? 'N/A' }}
                    </span>
                </td>

                {{-- Only show these columns once per job --}}
                @if($index === 0)
                    <td rowspan="{{ $dropCount }}">
                        @if($returnTask)
                            {{ $returnTask->addressShort() ?? 'N/A' }}<br>
                            <span class="small-text">
                                {{ $returnTask->timeWindowBeginFormatted() ?? 'N/A' }} – {{ $returnTask->timeWindowEndFormatted() ?? 'N/A' }}
                            </span>
                        @else
                            —
                        @endif
                    </td>

                    <td rowspan="{{ $dropCount }}" class="text-right">
                        {{ number_format(($job->price()['totalPrice'] ?? 0) / 100, 2) }}
                    </td>

                @endif
            </tr>
        @endforeach
    @endforeach
</tbody>
          </table>
        </td>
      </tr>
      @endforeach
        </tbody>
      </table>
    </section>

    <aside class="totals" aria-label="Totals">
      <table role="table" aria-hidden="false">
          <td>Subtotal</td>
          <td style="text-align:right">{{ number_format($invoice->total - $invoice->vat, 2) }}</td>
        </tr>
        <tr>
          <td>VAT 20%</td>
          <td style="text-align:right">{{ number_format($invoice->total * 0.2, 2) }}</td>
        </tr>
        <tr class="grand">
          <td>Total</td>
          <td style="text-align:right">£{{ number_format($invoice->total*1.2, 2) }}</td>
        </tr>
        <tr>
          <td>Paid to Date</td>
          <td style="text-align:right">£0.00</td>
        </tr>
        <tr>
          <td><strong>Balance Due</strong></td>
          <td style="text-align:right"><strong>£{{ number_format($invoice->total*1.2, 2) }}</strong></td>
        </tr>
      </table>
    </aside>

    <section class="bank" aria-label="Payment details">
      <strong>Bank Transfer</strong>
      <div style="height:8px;"></div>
      <div class="small">
        Starling Bank<br>
        Neko Home Delivery LLP<br>
        Account Number: 09592067<br>
        Sort Code: 608371
      </div>
    </section>

    <footer>
      <div class="small" style="margin-top:8px;">
        Invoice generated by Neko Home Delivery LLP.
      </div>
    </footer>
  </article>
</body>
</html>
