<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Job;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class JobInvoiceLockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // This project identifies admin/superadmin by role IDs 1 and 2.
        Role::create([
            'name' => 'superadmin',
            'display_name' => 'superadmin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'admin',
            'display_name' => 'admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'manager',
            'display_name' => 'manager',
            'guard_name' => 'web',
        ]);
    }

    public function test_job_is_locked_for_non_admin_when_completed_and_invoice_is_old(): void
    {
        $completed = Status::create(['name' => 'completed']);

        $client = Client::create([
            'name' => 'Lock Client',
            'address' => 'Default Address',
        ]);

        $invoice = new Invoice();
        $invoice->invoice_number = 'INV-LOCK-001';
        $invoice->customer_id = $client->id;
        $invoice->invoice_date = now()->subDays(10)->toDateString();
        $invoice->due_date = now()->addDays(10)->toDateString();
        $invoice->status = 'draft';
        $invoice->total = 0;
        $invoice->save();

        $invoiceItem = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Job item',
            'price' => 50,
        ]);

        $job = Job::create([
            'eilesNumeris' => 1,
            'status_id' => $completed->id,
            'clientToBill_id' => $client->id,
            'manager_id' => 1,
            'date' => now()->toDateString(),
            'invoice_item_id' => $invoiceItem->id,
            'price_adjustment_number' => 0,
        ]);

        $nonAdmin = User::factory()->create(['password' => 'password']);
        $nonAdmin->assignRole('manager');

        $admin = User::factory()->create(['password' => 'password']);
        $admin->assignRole('admin');

        $job->refresh();

        $this->assertTrue($job->isCompletedAndPastInvoiceLockDate());
        $this->assertTrue($job->isLockedForUser($nonAdmin));
        $this->assertFalse($job->isLockedForUser($admin));
    }

    public function test_update_status_is_blocked_for_locked_job_for_non_admin(): void
    {
        $completed = Status::create(['name' => 'completed']);
        $pending = Status::create(['name' => 'pending']);

        $client = Client::create([
            'name' => 'Lock Client',
            'address' => 'Default Address',
        ]);

        $invoice = new Invoice();
        $invoice->invoice_number = 'INV-LOCK-002';
        $invoice->customer_id = $client->id;
        $invoice->invoice_date = now()->subDays(10)->toDateString();
        $invoice->due_date = now()->addDays(10)->toDateString();
        $invoice->status = 'draft';
        $invoice->total = 0;
        $invoice->save();

        $invoiceItem = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Job item',
            'price' => 60,
        ]);

        $job = Job::create([
            'eilesNumeris' => 1,
            'status_id' => $completed->id,
            'clientToBill_id' => $client->id,
            'manager_id' => 1,
            'date' => now()->toDateString(),
            'invoice_item_id' => $invoiceItem->id,
            'price_adjustment_number' => 0,
        ]);

        $nonAdmin = User::factory()->create(['password' => 'password']);
        $nonAdmin->assignRole('manager');

        $response = $this->actingAs($nonAdmin)
            ->from(route('job.show', ['id' => $job->id]))
            ->post(route('job.updateStatus', $job), [
                'status_id' => $pending->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'This job is locked and cannot be updated.');

        $this->assertSame($completed->id, $job->fresh()->status_id);
    }
}
