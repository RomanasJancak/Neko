<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Invoice {{ $invoiceData['invoice_number'] ?? '' }}</title>

  <style>
    /* DomPDF works best with DejaVu Sans for special characters like £ */
    body {
      font-family: "DejaVu Sans", Arial, sans-serif;
      font-size: 12px;
      color: #111;
      background: #fff;
      margin: 0;
      padding: 0;
    }

    #invoice {
      padding: 10px;
    }

    #invoice-title {
      font-size: 22px;
      margin-bottom: 20px;
      border-bottom: 2px solid #1f6feb;
      padding-bottom: 10px;
    }

    /* Table layout replacements for Flexbox */
    .layout-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .layout-table td {
      vertical-align: top;
      padding: 0;
    }

    .title {
      color: #1f6feb;
      font-weight: bold;
    }

    .text-right {
      text-align: right;
    }

    .break {
      height: 1px;
      background-color: #ddd;
      margin: 15px 0;
      border: none;
    }

    /* Job Tables */
    .job-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      font-size: 11px;
    }

    .job-table th {
      background: #f0f0f0;
      border: 1px solid #ddd;
      padding: 6px;
      text-align: left;
    }

    .job-table td {
      border: 1px solid #ddd;
      padding: 6px;
      vertical-align: top;
    }

    .time {
      color: #555;
      font-size: 10px;
    }

    #footer {
      margin-top: 30px;
      font-size: 11px;
    }

    .muted {
      color: #555;
    }

    /* Force page breaks if invoice is long */
    .page-break {
      page-break-after: always;
    }
  </style>
</head>

<body>

  @php
    $invoiceData = $snapshotData['invoice'] ?? [];
    $clientData = $snapshotData['client'] ?? [];
    $items = $snapshotData['items'] ?? [];
    $totals = $snapshotData['totals'] ?? [];
  @endphp

<div id="invoice">

  <!-- HEADER: REPLACED FLEX WITH TABLE -->
  <div id="invoice-title">
    <b>Itemised Invoice of {{ $invoiceData['invoice_number'] }}</b>
  </div>

  <table class="layout-table">
    <tr>
      <td style="width: 50%;">
        <div class="title">Neko Home Delivery LLP</div>
        <div>410 1951 42</div>
        <div>www.nekohomedelivery.com</div>
        <div>nekohomedelivery@gmail.com</div>
        <div>07429381472</div>
      </td>
      <td style="width: 50%; text-align: right;">
        <div>Bakersfield, Crayford Road</div>
        <div>Flat 22</div>
        <div>London N7 0LT</div>
        <div>United Kingdom</div>
      </td>
    </tr>
  </table>

  <div class="title" style="margin-bottom: 5px;">INVOICE</div>
  <hr class="break">

  <!-- INVOICE INFO & CLIENT: REPLACED FLEX WITH TABLE -->
  <table class="layout-table">
    <tr>
      <td style="width: 50%;">
        <table style="width: 100%;">
          <tr>
            <td style="padding-right: 20px; line-height: 1.6;">
              <div>Invoice Number:</div>
              <div>Invoice Date:</div>
              <div>Due Date:</div>
              <div class="title">Invoice Total:</div>
            </td>
            <td style="line-height: 1.6;">
              <div>{{ $invoiceData['invoice_number'] }}</div>
              <div>@displayInvoiceDate($invoiceData['invoice_date'] ?? null)</div>
              <div>@displayInvoiceDate($invoiceData['due_date'] ?? null)</div>
              <div class="title">£{{ number_format($totals['grand_total'], 2) }}</div>
            </td>
          </tr>
        </table>
      </td>
      <td style="width: 50%;">
        <div style="margin-left: 40px;">
          <b>BILL TO:</b><br>
          <div style="margin-top: 5px;">
            <b>{{ $clientData['name'] }}</b><br>
            {{ $clientData['address_line'] }}<br>
            {{ $clientData['city'] }} {{ $clientData['postcode'] }}<br>
            {{ $clientData['country'] }}<br>
            <span class="muted">{{ $clientData['email'] }}</span>
          </div>
        </div>
      </td>
    </tr>
  </table>

  <hr class="break">

  <!-- ITEMS -->
  @foreach ($items as $item)

    <table class="job-table">
      <thead>
        <tr>
          <th style="width: 60%;">Item Description</th>
          <th style="width: 20%;">Jobs Count</th>
          <th style="width: 20%;">Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $item['description'] }}</td>
          <td>{{ $item['jobs_count'] }}</td>
          <td>£{{ number_format($item['price'], 2) }}</td>
        </tr>
      </tbody>
    </table>

    <table class="job-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Status</th>
          <th>Date</th>
          <th>Pickup</th>
          <th>Package</th>
          <th>Dropoff</th>
          <th>Return</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($item['jobs'] as $job)
          @php
            $dropOffs = $job['dropoffs'] ?? [];
            $dropCount = max(count($dropOffs), 1);
          @endphp
          @foreach ($dropOffs as $index => $drop)
          <tr>
            @if ($index === 0)
              <td rowspan="{{ $dropCount }}">#{{ $job['id'] }}</td>
              <td rowspan="{{ $dropCount }}">{{ ucfirst($job['status']) }}</td>
              <td rowspan="{{ $dropCount }}">{{ \Carbon\Carbon::parse($job['date'])->format('d-m-Y') }}</td>
              <td rowspan="{{ $dropCount }}">
                {{ $job['pickup']['address'] }}<br>
                <span class="time">{{ $job['pickup']['time_window_begin'] }}–{{ $job['pickup']['time_window_end'] }}</span>
              </td>
            @endif
            <td>{{ $drop['package_type'] }} × {{ $drop['quantity'] }}</td>
            <td>
              {{ $drop['address'] }}<br>
              <span class="time">{{ $drop['time_window_begin'] }}–{{ $drop['time_window_end'] }}</span>
            </td>
            @if ($index === 0)
              <td rowspan="{{ $dropCount }}">{{ $job['return']['address'] ?? '—' }}</td>
              <td rowspan="{{ $dropCount }}">£{{ number_format($job['total'], 2) }}</td>
            @endif
          </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  @endforeach

  <!-- TOTALS: REPLACED FLEX WITH TABLE -->
  <table class="layout-table">
    <tr>
      <td style="width: 60%;"></td>
      <td style="width: 40%;">
        <table style="width: 100%; line-height: 1.8;">
          <tr>
            <td style="text-align: right; padding-right: 15px;">Subtotal:</td>
            <td style="text-align: right;">£{{ number_format($totals['subtotal'], 2) }}</td>
          </tr>
          <tr>
            <td style="text-align: right; padding-right: 15px;">VAT {{ number_format($totals['vat_rate'] * 100, 2) }}%:</td>
            <td style="text-align: right;">£{{ number_format($totals['vat_amount'], 2) }}</td>
          </tr>
          <tr style="font-weight: bold; font-size: 14px;">
            <td style="text-align: right; padding-right: 15px; border-top: 1px solid #000;">Total:</td>
            <td style="text-align: right; border-top: 1px solid #000;">£{{ number_format($totals['grand_total'], 2) }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- FOOTER -->
  <div id="footer">
    <hr class="break">
    <b>Bank Transfer Details</b>
    <div style="margin-top: 5px;">
      Starling Bank | Neko Home Delivery LLP<br>
      Account Number: 09592067 | Sort Code: 60-83-71
    </div>
    <div style="text-align: center; margin-top: 20px; color: #888;">
      Invoice generated by Neko Home Delivery LLP
    </div>
  </div>

</div>
</body>
</html>