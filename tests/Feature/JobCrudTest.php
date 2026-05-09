<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Job;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_perform_job_crud_via_web_routes(): void
    {
        $actingUser = User::factory()->create([
            'password' => 'password',
        ]);

        $client = Client::create([
            'name' => 'Job Client',
            'address' => 'Default Address',
        ]);

        $pendingStatus = Status::create(['name' => 'pending']);
        $completedStatus = Status::create(['name' => 'completed']);

        $this->actingAs($actingUser);

        // Store via the index-page creation path (JSON flow)
        $storeResponse = $this->post(route('job.store'), [
            'isJobCreationFromIndexPage' => 1,
            'status_id' => $pendingStatus->id,
            'courrier_id' => 0,
            'billingClientId' => $client->id,
            'common_date' => now()->toDateString(),
        ])->assertOk()->assertJson([
            'success' => true,
        ]);

        $createdJobId = (int) $storeResponse->json('job.id');
        $this->assertGreaterThan(0, $createdJobId);

        $this->assertDatabaseHas('jobs', [
            'id' => $createdJobId,
            'status_id' => $pendingStatus->id,
            'clientToBill_id' => $client->id,
            'manager_id' => $actingUser->id,
        ]);

        $this->post(route('job.update'), [
            'id' => $createdJobId,
            'courierId' => '0',
            'status_id' => $completedStatus->id,
            'clientId' => $client->id,
            'common_date' => now()->addDay()->toDateString(),
            'note' => 'Updated note',
        ])->assertOk()->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $createdJobId,
            'status_id' => $completedStatus->id,
            'clientToBill_id' => $client->id,
        ]);

        $this->post(route('job.delete'), [
            'id' => $createdJobId,
        ])->assertOk()->assertJson([
            'message' => 'Job deleted successfully.',
        ]);

        $this->assertDatabaseMissing('jobs', [
            'id' => $createdJobId,
        ]);
    }

    public function test_guest_is_redirected_from_job_routes(): void
    {
        $this->get(route('job.index'))->assertRedirect(route('login'));
        $this->get(route('job.create'))->assertRedirect(route('login'));
    }
}
