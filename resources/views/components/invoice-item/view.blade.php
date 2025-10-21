<style>
    @page {
        margin: 25px 30px;
    }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        color: #333;
    }

    .invoice-item {
        border: 1px solid #ccc;
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
        color: #666;
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
        background-color: #f5f5f5;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 6px 8px;
        vertical-align: top;
    }

    th {
        font-weight: bold;
        text-align: left;
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
        color: #555;
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

<div class="invoice-item">
    <div class="invoice-header">
        <table style="width:100%; border:0; border-collapse:collapse;">
            <tr>
                <td><h4>Invoice Item #{{ $item->id }}</h4></td>
                <td class="total">£{{ number_format($item->price, 2) }}</td>
            </tr>
        </table>
    </div>

    <p class="description">{{ $item->description }}</p>

    @if($item->jobs->count())
        <table>
            <thead>
                <tr>
                    <th>Job #</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Pickup</th>
                    <th>Deliveries</th>
                    <th>Returns</th>
                    <th class="text-right">Amount (£)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->jobs as $job)
                    @php
                        $pickup = $job->getPickupTask();
                        $dropOffs = $job->getDropOffs();
                        $returnTask = $job->hasReturn() ? $job->getReturnTask()->return : null;
                    @endphp
                    <tr>
                        <td>#{{ $job->id }}</td>
                        <td>{{ ucfirst($job->status->name ?? 'Unknown') }}</td>
                        <td>{{ $job->date ?? 'N/A' }}</td>

                        <td>
                            {{ $pickup->addressShort() ?? 'N/A' }}<br>
                            <span class="small-text">
                                {{ $pickup->timeWindowBeginFormatted() ?? 'N/A' }} – {{ $pickup->timeWindowEndFormatted() ?? 'N/A' }}
                            </span>
                        </td>

                        <td>
                            @foreach($dropOffs as $dropOff)
                                {{ $dropOff->addressShort() ?? 'N/A' }}<br>
                                <span class="small-text">
                                    {{ $dropOff->timeWindowBeginFormatted() ?? 'N/A' }} – {{ $dropOff->timeWindowEndFormatted() ?? 'N/A' }}
                                </span><br>
                                <span class="small-text">
                                    {{ $dropOff->packageType->name }} × {{ $dropOff->quantity }}
                                </span>
                                @if(!$loop->last)
                                    <hr style="border:0;border-top:1px dotted #ccc;margin:3px 0;">
                                @endif
                            @endforeach
                        </td>

                        <td>
                            @if($returnTask)
                                {{ $returnTask->addressShort() ?? 'N/A' }}<br>
                                <span class="small-text">
                                    {{ $returnTask->timeWindowBeginFormatted() ?? 'N/A' }} – {{ $returnTask->timeWindowEndFormatted() ?? 'N/A' }}
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td class="text-right">
                            {{ number_format(($job->price()['totalPrice'] ?? 0) / 100, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-total">
            Item Total: £{{ number_format($item->price, 2) }}
        </div>
    @else
        <p class="description">No jobs linked to this invoice item.</p>
    @endif
</div>
