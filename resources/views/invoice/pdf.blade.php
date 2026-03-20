<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Invoice {{$invoice->invoice_number}}</title>

  <style>
    :root {
      --accent: #1f6feb;
      --muted: #555;
      --border: #ddd;
      --paper: #fff;
      font-family: "DejaVu Sans", Arial, sans-serif;
      color: #111;
    }

    body {
      background: #f6f8fa;
      padding: 25px;
      margin: 0;
    }

    .invoice {
      max-width: 960px;
      margin: 0 auto;
      background: var(--paper);
      padding: 32px;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
      border: 1px solid var(--border);
    }

    header {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 25px;
      border-bottom: 2px solid var(--border);
      padding-bottom: 15px;
    }

    .from {
      font-weight: 700;
      font-size: 20px;
    }

    .small {
      font-size: 13px;
      color: var(--muted);
      line-height: 1.5;
    }

    .invoice-meta {
      text-align: right;
      font-size: 14px;
    }

    .invoice-meta .title {
      font-weight: 700;
      font-size: 22px;
      color: var(--accent);
    }

    .addresses {
      display: flex;
      gap: 50px;
      margin: 25px 0;
      flex-wrap: wrap;
    }

    .address {
      min-width: 240px;
    }

    table.items {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table.items th,
    table.items td {
      border-bottom: 1px solid var(--border);
      padding: 10px 8px;
      vertical-align: top;
      font-size: 14px;
    }

    table.items th {
      background: #f8f8f8;
      font-weight: 700;
      color: #222;
    }

    table.items td.numeric {
      text-align: right;
      font-family: monospace;
    }

    /* Nested job table */
    .job-table {
      width: 100%;
      border-collapse: collapse;
      margin: 8px 0 20px;
      font-size: 12px;
      background: #fafafa;
    }

    .job-table th,
    .job-table td {
      border: 1px solid var(--border);
      padding: 6px 8px;
      vertical-align: top;
    }

    .job-table th {
      background: #f2f2f2;
      text-align: left;
      font-weight: 600;
    }

    .small-text {
      font-size: 11px;
      color: var(--muted);
    }

    .totals {
      margin-top: 30px;
      width: 320px;
      float: right;
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
    }

    .totals table {
      width: 100%;
      border-collapse: collapse;
    }

    .totals td {
      padding: 8px 12px;
      font-size: 14px;
      background: #fff;
    }

    .totals tr:nth-child(even) td {
      background: #f9f9f9;
    }

    .totals .grand td {
      font-weight: 700;
      font-size: 16px;
      border-top: 2px solid var(--border);
    }

    .bank {
      clear: both;
      margin-top: 40px;
      padding-top: 15px;
      border-top: 1px dashed var(--border);
      font-size: 14px;
    }

    footer {
      margin-top: 20px;
      font-size: 12px;
      color: var(--muted);
      text-align: center;
    }

    /* Print & page layout */
    @page {
      margin: 25px 30px;
    }

    @media print {
      body {
        background: #fff;
      }
      .invoice {
        box-shadow: none;
        border: none;
        padding: 0;
      }
    }
  </style>
</head>

<body>
  @php
    $invoiceData = $snapshotData['invoice'] ?? [];
    $clientData = $snapshotData['client'] ?? [];
    $items = $snapshotData['items'] ?? [];
    $totals = $snapshotData['totals'] ?? [];
    $version = $snapshotData['version'] ?? null;
    $generatedAt = $snapshotData['generated_at'] ?? null;
  @endphp

  <article class="invoice" role="document" aria-label="Invoice {{$invoiceData['invoice_number'] ?? ''}}">
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

      <div class="invoice-meta">
        <div class="title">Itemized invoice part of Invoice : {{$invoiceData['invoice_number'] ?? ''}}</div>
        <div style="margin-top:10px;">
          <div><strong>Invoice Number:</strong> {{$invoiceData['invoice_number'] ?? ''}}</div>
          <div><strong>Invoice Date:</strong> {{$invoiceData['invoice_date'] ?? ''}}</div>
          <div><strong>Due Date:</strong> {{$invoiceData['due_date'] ?? ''}}</div>
          @if($version)
          <div><strong>Version:</strong> {{$version}}</div>
          @endif
          @if($generatedAt)
          <div><strong>Generated:</strong> {{$generatedAt}}</div>
          @endif
          <div style="margin-top:8px; font-size:16px;">
            <strong>Invoice Total:</strong> £{{ number_format($totals['grand_total'] ?? 0, 2) }}
          </div>
          <div style="color:var(--muted)">
            <strong>Balance Due:</strong> £{{ number_format($totals['balance_due'] ?? 0, 2) }}
          </div>
        </div>
      </div>
    </header>

    <section class="addresses">
      <div class="address">
        <strong>Bill To</strong>
        <div class="small" style="margin-top:8px;">
          {{$clientData['name'] ?? ''}}<br>
          {{$clientData['address_line'] ?? ''}}<br>
          {{$clientData['city'] ?? ''}} {{$clientData['postcode'] ?? ''}}<br>
          {{$clientData['country'] ?? ''}}<br>
          <a href="mailto:{{$clientData['email'] ?? ''}}">{{$clientData['email'] ?? ''}}</a>
        </div>
      </div>

      <div class="address">
        <strong>From</strong>
        <div class="small" style="margin-top:8px;">
          Bakersfield, Crayford Road<br>
          Flat 22<br>
          London N7 0LT<br>
          United Kingdom
        </div>
      </div>
    </section>

    <section aria-label="Line items">
      <table class="items">
        <thead>
          <tr>
            <th style="width:55%;">Item</th>
            <th style="width:10%;">Quantity</th>
            <th style="width:23%; text-align:right;">Line Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
          <tr>
            <td>
              <strong>{{ $item['description'] ?? '' }}</strong>
            </td>
            <td class="numeric">{{ $item['jobs_count'] ?? 0 }}</td>
            <td class="numeric">£{{ number_format($item['price'] ?? 0, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3" style="padding:0;">
              <table class="job-table">
                <thead>
                  <tr>
                    <th>Job ID</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Pickup</th>
                    <th>Package</th>
                    <th>Dropoff</th>
                    <th>Return</th>
                    <th class="text-right">Job Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($item['jobs'] ?? [] as $job)
                    @php
                      $dropOffs = $job['dropoffs'] ?? [];
                      $dropCount = max(count($dropOffs), 1);
                    @endphp

                    @foreach($dropOffs as $index => $dropOff)
                    <tr>
                      @if($index === 0)
                        <td rowspan="{{ $dropCount }}">#{{ $job['id'] ?? '' }}</td>
                        <td rowspan="{{ $dropCount }}">{{ ucfirst($job['status'] ?? 'Unknown') }}</td>
                        <td rowspan="{{ $dropCount }}">{{ $job['date'] ?? 'N/A' }}</td>
                        <td rowspan="{{ $dropCount }}">
                          {{ $job['pickup']['address'] ?? 'N/A' }}<br>
                          <span class="small-text">{{ $job['pickup']['time_window_begin'] ?? 'N/A' }} – {{ $job['pickup']['time_window_end'] ?? 'N/A' }}</span>
                        </td>
                      @endif

                      <td><span class="small-text">{{ $dropOff['package_type'] ?? '' }} × {{ $dropOff['quantity'] ?? '' }}</span></td>
                      <td>
                        {{ $dropOff['address'] ?? 'N/A' }}<br>
                        <span class="small-text">{{ $dropOff['time_window_begin'] ?? 'N/A' }} – {{ $dropOff['time_window_end'] ?? 'N/A' }}</span>
                      </td>

                      @if($index === 0)
                        <td rowspan="{{ $dropCount }}">
                          @if(!empty($job['return']))
                            {{ $job['return']['address'] ?? 'N/A' }}<br>
                            <span class="small-text">{{ $job['return']['time_window_begin'] ?? 'N/A' }} – {{ $job['return']['time_window_end'] ?? 'N/A' }}</span>
                          @else
                            —
                          @endif
                        </td>
                        <td rowspan="{{ $dropCount }}" class="text-right">
                          £{{ number_format($job['total'] ?? 0, 2) }}
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

    <aside class="totals">
      <table>
        <tr>
          <td>Subtotal</td>
          <td style="text-align:right">£{{ number_format($totals['subtotal'] ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td>VAT {{ number_format(($totals['vat_rate'] ?? 0) * 100, 2) }}%</td>
          <td style="text-align:right">£{{ number_format($totals['vat_amount'] ?? 0, 2) }}</td>
        </tr>
        <tr class="grand">
          <td>Total</td>
          <td style="text-align:right">£{{ number_format($totals['grand_total'] ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td>Paid to Date</td>
          <td style="text-align:right">£{{ number_format($totals['amount_paid'] ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td><strong>Balance Due</strong></td>
          <td style="text-align:right"><strong>£{{ number_format($totals['balance_due'] ?? 0, 2) }}</strong></td>
        </tr>
      </table>
    </aside>

    <section class="bank">
      <strong>Bank Transfer</strong>
      <div class="small" style="margin-top:8px;">
        Starling Bank<br>
        Neko Home Delivery LLP<br>
        Account Number: 09592067<br>
        Sort Code: 60-83-71
      </div>
    </section>

    <footer>
      Invoice generated by Neko Home Delivery LLP
    </footer>
  </article>
</body>
</html>
