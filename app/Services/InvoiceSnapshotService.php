<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceSnapshot;
use Carbon\Carbon;

class InvoiceSnapshotService
{
    public function buildSnapshotData(Invoice $invoice, float $vatRate): array
    {
        $invoice->loadMissing(['client', 'invoiceItems.jobs', 'invoiceItems.jobs.status']);

        $subtotal = $invoice->total;
        $vatAmount = round($subtotal * $vatRate, 2);
        $grandTotal = $subtotal + $vatAmount;
        $amountPaid = $invoice->amount_paid ?? 0;
        $balanceDue = $grandTotal - $amountPaid;

        $items = $invoice->invoiceItems->map(function ($item) {
            $jobs = $item->jobs->map(function ($job) {
                $pickup = $job->getPickupTask();
                $dropOffs = $job->getDropOffs();
                $returnTask = $job->hasReturn() ? $job->getReturnTask()->return : null;

                $dropOffData = collect($dropOffs)->map(function ($dropOff) {
                    return [
                        'package_type' => $dropOff->packageType->name ?? null,
                        'quantity' => $dropOff->quantity ?? null,
                        'address' => $dropOff->addressShort() ?? null,
                        'time_window_begin' => $dropOff->timeWindowBeginFormatted() ?? null,
                        'time_window_end' => $dropOff->timeWindowEndFormatted() ?? null,
                    ];
                })->values();

                return [
                    'id' => $job->id,
                    'status' => $job->status->name ?? null,
                    'date' => $job->date,
                    'pickup' => [
                        'address' => $pickup?->addressShort(),
                        'time_window_begin' => $pickup?->timeWindowBeginFormatted(),
                        'time_window_end' => $pickup?->timeWindowEndFormatted(),
                    ],
                    'dropoffs' => $dropOffData,
                    'return' => $returnTask ? [
                        'address' => $returnTask->addressShort() ?? null,
                        'time_window_begin' => $returnTask->timeWindowBeginFormatted() ?? null,
                        'time_window_end' => $returnTask->timeWindowEndFormatted() ?? null,
                    ] : null,
                    'total' => ($job->price()['totalPrice'] ?? 0) / 100,
                ];
            })->values();

            return [
                'id' => $item->id,
                'description' => $item->description,
                'price' => $item->price,
                'jobs_count' => $item->jobs->count(),
                'jobs' => $jobs,
            ];
        })->values();

        return [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
            ],
            'client' => [
                'name' => $invoice->client->name ?? null,
                'address_line' => $invoice->client->address_line ?? null,
                'city' => $invoice->client->city ?? null,
                'postcode' => $invoice->client->postcode ?? null,
                'country' => $invoice->client->country ?? null,
                'email' => $invoice->client->email ?? null,
            ],
            'items' => $items,
            'totals' => [
                'subtotal' => $subtotal,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'balance_due' => $balanceDue,
            ],
            'generated_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    public function createSnapshot(Invoice $invoice, float $vatRate, ?int $createdBy = null): InvoiceSnapshot
    {
        $latestVersion = (int) $invoice->snapshots()->max('version');
        $version = $latestVersion + 1;

        $data = $this->buildSnapshotData($invoice, $vatRate);

        return $invoice->snapshots()->create([
            'version' => $version,
            'generated_at' => $data['generated_at'],
            'data' => $data,
            'created_by' => $createdBy,
        ]);
    }
}
