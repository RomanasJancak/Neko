<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_perform_client_crud_via_web_routes(): void
    {
        // Reserve client ID 1 because ClientController::destroy blocks deleting it.
        Client::create([
            'name' => 'Default Client',
            'address' => 'Default Address',
        ]);

        $actingUser = User::factory()->create([
            'password' => 'password',
        ]);

        $this->actingAs($actingUser);

        $this->get(route('client.index'))->assertOk();
        $this->get(route('client.create'))->assertOk();

        $this->post(route('client.store'), [
            'clientname' => 'CRUD Client',
            'shortenedName' => 'CC',
            'reg-addr-country' => 'Lithuania',
            'reg-addr-city' => 'Vilnius',
            'reg-addr-postal_code' => 'LT-01100',
            'reg-addr-address_line' => 'Gedimino pr. 1',
            'phone' => '+37061234567',
        ])
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Client created successfully',
            ]);

        $createdClient = Client::where('name', 'CRUD Client')->firstOrFail();

        $this->assertDatabaseHas('clients', [
            'id' => $createdClient->id,
            'name' => 'CRUD Client',
            'shortenedName' => 'CC',
            'country' => 'Lithuania',
            'city' => 'Vilnius',
            'postal_code' => 'LT-01100',
            'address_line' => 'Gedimino pr. 1',
            'phone' => '+37061234567',
        ]);

        $this->get(route('client.show', $createdClient))->assertOk();

        $this->post(route('client.update'), [
            'clientid' => $createdClient->id,
            'clientname' => 'CRUD Client Updated',
            'shortenedName' => 'CCU',
            'reg-addr-postal_code' => 'LT-02100',
            'reg-addr-address_line' => 'Konstitucijos pr. 10',
            'phone' => '+37069999999',
        ])
            ->assertOk()
            ->assertJsonPath('after_update_client.name', 'CRUD Client Updated');

        $this->assertDatabaseHas('clients', [
            'id' => $createdClient->id,
            'name' => 'CRUD Client Updated',
            'shortenedName' => 'CCU',
            'postal_code' => 'LT-02100',
            'address_line' => 'Konstitucijos pr. 10',
            'phone' => '+37069999999',
        ]);

        $this->post(route('client.delete'), [
            'clientid' => $createdClient->id,
        ])
            ->assertOk()
            ->assertJson([
                'message' => 'Client deleted successfully.',
            ]);

        $this->assertDatabaseMissing('clients', [
            'id' => $createdClient->id,
        ]);
    }

    public function test_guest_is_redirected_from_client_routes(): void
    {
        $this->get(route('client.index'))->assertRedirect(route('login'));
        $this->get(route('client.create'))->assertRedirect(route('login'));
    }
}
