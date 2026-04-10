<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Invoice {{$invoiceData['invoice_number'] ?? ''}}</title>

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

    #invoice {
      max-width: 960px;
      margin: 0 auto;
      background: var(--paper);
      padding: 32px;
      border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
      border: 1px solid var(--border);
    }

    #invoice-title{
      font-size:28px;
      margin-bottom:10px;
    }

    #header {
      display: flex;
      justify-content: end;
      gap: 75px;
      margin-bottom: 30px;
    }

    #invoice-header {
      display: flex;
      gap: 100px;
    }

    #totals {
      display: flex;
      justify-content: end;
    }

    #footer-title{
      margin-bottom: 10px;
    }

    #footer-text{
      text-align: center;
      font-size: 12px;
    }

    #client-info{
      display: flex;
      flex-direction: row;
      gap:20px
    }

    .span-info {
      display: flex;
      gap: 20px;
    }

    .title {
      color: #1f6feb;
    }

    .info {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .break {
      display: block;
      height: 2px;
      background-color: lightgray;
      margin: 16px 0;
      width: 100%;
      border: none;
    }

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

    .time{
      color: var(--muted);
      font-size:11px;
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

<article id="invoice">

  <!-- HEADER -->
  <div id="invoice-title"><b>Itemised Invoice of {{ $invoiceData['invoice_number'] }}</b></div>

  <section id="header">
    <div class="info">
      <div class="title"><b>Neko Home Delivery LLP</b></div>
      <div>410 1951 42</div>
      <div>www.nekohomedelivery.com</div>
      <div>nekohomedelivery@gmail.com</div>
      <div>07429381472</div>
    </div>

    <div class="info">
      <div>Bakersfield, Crayford Road</div>
      <div>Flat 22</div>
      <div>London N7 0LT</div>
      <div>United Kingdom</div>
    </div>
  </section>

  <!-- INVOICE INFO -->
  <section>
    <div class="title"><b>INVOICE</b></div>
    <div class="break"></div>

    <div id="invoice-header">
      <div class="span-info">
        <div class="info">
          <div>Invoice Number</div>
          <div>Invoice Date</div>
          <div>Due Date</div>
          <div>Invoice Total</div>
        </div>

        <div class="info">
          <div>{{ $invoiceData['invoice_number'] }}</div>
          <div>{{ $invoiceData['invoice_date'] }}</div>
          <div>{{ $invoiceData['due_date'] }}</div>
          <div>£{{ number_format($totals['grand_total'], 2) }}</div>
        </div>
      </div>

      <div id="client-info">
        <b>BILL TO:</b>
        <div class="info">
          <b>{{ $clientData['name'] }}</b>
          <div>{{ $clientData['address_line'] }}</div>
          <div>{{ $clientData['city'] }} {{ $clientData['postcode'] }}</div>
          <div>{{ $clientData['country'] }}</div>
          <div>{{ $clientData['email'] }}</div>
        </div>
      </div>
    </div>

    <div class="break"></div>
  </section>

  <!-- ITEMS -->
  @foreach ($items as $item)

  <table class="job-table">
    <thead>
      <tr>
        <th>Item</th>
        <th>Jobs Count</th>
        <th>Price</th>
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
        <th>Job ID</th>
        <th>Status</th>
        <th>Date</th>
        <th>Pickup</th>
        <th>Package</th>
        <th>Dropoff</th>
        <th>Return</th>
        <th>Job Total</th>
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
            <td rowspan="{{ $dropCount }}">{{ $job['date'] }}</td>
            <td rowspan="{{ $dropCount }}">
              {{ $job['pickup']['address'] }}<br>
              <span class="time">{{ $job['pickup']['time_window_begin'] }} – {{ $job['pickup']['time_window_end'] }}</span>
            </td>
          @endif

          <td>{{ $drop['package_type'] }} × {{ $drop['quantity'] }}</td>

          <td>
            {{ $drop['address'] }}<br>
            <span class="time">{{ $drop['time_window_begin'] }} – {{ $drop['time_window_end'] }}</span>
          </td>

          @if ($index === 0)
            <td rowspan="{{ $dropCount }}">
              {{ $job['return']['address'] ?? '—' }}
            </td>
            <td rowspan="{{ $dropCount }}">
              £{{ number_format($job['total'], 2) }}
            </td>
          @endif
        </tr>
        @endforeach
      @endforeach
    </tbody>
  </table>

  <div class="break"></div>

  @endforeach

  <!-- TOTALS -->
  <section id="totals">
    <div class="span-info">
      <div class="info">
        <div>Subtotal</div>
        <div>VAT {{ number_format($totals['vat_rate'] * 100, 2) }}%</div>
        <div>Total</div>
      </div>

      <div class="info">
        <div>£{{ number_format($totals['subtotal'], 2) }}</div>
        <div>£{{ number_format($totals['vat_amount'], 2) }}</div>
        <div>£{{ number_format($totals['grand_total'], 2) }}</div>
      </div>
    </div>
  </section>

  <div class="break"></div>

  <!-- FOOTER -->
  <section id="footer">
    <div id="footer-title"><b>Bank Transfer</b></div>
    <div class="info">
      <div>Starling Bank</div>
      <div>Neko Home Delivery LLP</div>
      <div>Account Number: 09592067</div>
      <div>Sort Code: 60-83-71</div>
    </div>
    <div id="footer-text">Invoice generated by Neko Home Delivery LLP</div>
  </section>

</article>
</body>
</html>