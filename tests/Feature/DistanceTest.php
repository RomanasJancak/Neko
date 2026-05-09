<?php

namespace Tests\Feature;

use App\Models\Distance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DistanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Distance::clearRequestCache();
    }

    public function test_guest_is_redirected_from_distance_route(): void
    {
        $this->get(route('distance.getDistance', [
            'origin' => 'A Street',
            'destination' => 'B Street',
        ]))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_gets_distance_from_cached_database_value(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        Distance::create([
            'origin_address' => 'Origin Address',
            'destination_address' => 'Destination Address',
            'origin_address_id' => 'place_origin',
            'destination_address_id' => 'place_dest',
            'origin_lat' => 54.6872,
            'origin_lng' => 25.2797,
            'destination_lat' => 54.6892,
            'destination_lng' => 25.2817,
            'mode_of_travel' => 'walking',
            'distance' => 321,
        ]);

        Http::fake();

        $response = $this->actingAs($user)->get(route('distance.getDistance', [
            'origin' => 'Origin Address',
            'destination' => 'Destination Address',
        ]));

        $response->assertOk();
        $this->assertSame(321, $response->json());
        Http::assertNothingSent();
    }

    public function test_authenticated_user_fetches_and_stores_distance_when_not_cached(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        Http::fake([
            'https://maps.googleapis.com/maps/api/geocode/json*' => Http::sequence()
                ->push([
                    'status' => 'OK',
                    'results' => [[
                        'place_id' => 'place_origin_1',
                        'geometry' => ['location' => ['lat' => 54.6872, 'lng' => 25.2797]],
                    ]],
                ], 200)
                ->push([
                    'status' => 'OK',
                    'results' => [[
                        'place_id' => 'place_dest_1',
                        'geometry' => ['location' => ['lat' => 54.7000, 'lng' => 25.3000]],
                    ]],
                ], 200),
            'https://maps.googleapis.com/maps/api/distancematrix/json*' => Http::response([
                'status' => 'OK',
                'rows' => [[
                    'elements' => [[
                        'status' => 'OK',
                        'distance' => ['value' => 987],
                    ]],
                ]],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get(route('distance.getDistance', [
            'origin' => 'Origin Missing',
            'destination' => 'Destination Missing',
        ]));

        $response->assertOk();
        $this->assertSame(987, $response->json());

        Http::assertSentCount(3);
        Http::assertSent(function (HttpRequest $request) {
            return str_contains($request->url(), '/maps/api/geocode/json')
                && (($request->data()['address'] ?? null) === 'Origin Missing');
        });
        Http::assertSent(function (HttpRequest $request) {
            return str_contains($request->url(), '/maps/api/geocode/json')
                && (($request->data()['address'] ?? null) === 'Destination Missing');
        });
        Http::assertSent(function (HttpRequest $request) {
            return str_contains($request->url(), '/maps/api/distancematrix/json')
                && (($request->data()['origins'] ?? null) === 'place_id:place_origin_1')
                && (($request->data()['destinations'] ?? null) === 'place_id:place_dest_1');
        });

        $this->assertDatabaseHas('distances', [
            'origin_address' => 'Origin Missing',
            'destination_address' => 'Destination Missing',
            'origin_address_id' => 'place_origin_1',
            'destination_address_id' => 'place_dest_1',
            'mode_of_travel' => 'walking',
            'distance' => 987,
        ]);
    }
}
