<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client; // Import the Client model
use App\Models\Status;
use App\Models\User; 
use App\Models\Role;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientIds = Client::pluck('id')->toArray();
        $statusIds = Status::pluck('id')->toArray();
        $courierIds =   Role::where('name', 'courier')->first()->users->pluck('id')->toArray();
        $managerIds =   Role::where('name', 'manager')->first()->users->pluck('id')->toArray();
        $pickup_time_begin = $this->faker->dateTimeBetween(
            '07:00',
             '21:00'
            );
        $pickup_time_end = $this->faker->dateTimeBetween(
            $pickup_time_begin->modify('+15 minutes'),
             '21:30'
            );
        $dropoff_time_begin = $this->faker->dateTimeBetween(
            $pickup_time_end->modify('+30 minutes'),
             '22:15'
            );
        $dropoff_time_end = $this->faker->dateTimeBetween(
            $dropoff_time_begin->modify('+15 minutes'),
             '23:00'
            );
        return [
            'sender_id' => $this->faker->randomElement($clientIds),
            'receiver_id' => $this->faker->randomElement($clientIds),
            'courrier_id' => $this->faker->randomElement($courierIds),
            'pickup_time_begin' =>  $pickup_time_begin->format('Y-m-d H:i:s'),
            'pickup_time_end'   =>  $pickup_time_end->format('Y-m-d H:i:s'),
            'dropoff_time_begin'=>  $dropoff_time_begin->format('Y-m-d H:i:s'),
            'dropoff_time_end'  =>  $dropoff_time_end->format('Y-m-d H:i:s'),


            'status_id' => $this->faker->randomElement($statusIds),
            'collection_details' => $this->faker->text,
            'dropoff_details'   =>  $this->faker->text,
            'pickup_address' => $this->faker->address,
            'delivery_address' => $this->faker->address,
            'senderContacts' => $this->faker->name,
            'manager_id' => $this->faker->randomElement($managerIds),
            'receiverContacts' => $this->faker->name,
            'notes' => $this->faker->text,
        ];
    }
}
