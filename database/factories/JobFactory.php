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
        return [
            'client_id' => $this->faker->randomElement($clientIds),
            'courrier_id' => $this->faker->randomElement($courierIds),
            'creation_time' => $this->faker->dateTimeThisYear,
            'completion_time' => $this->faker->dateTimeThisYear,
            'status_id' => $this->faker->randomElement($statusIds),
            'collection_details' => $this->faker->text,
            'pickup_address' => $this->faker->address,
            'delivery_address' => $this->faker->address,
            'senderContacts' => $this->faker->name,
            'manager_id' => $this->faker->randomElement($managerIds),
            'receiverContacts' => $this->faker->name,
            'group_id' => rand(1, 3), // Replace 1 and 3 with your group IDs
            'notes' => $this->faker->text,
            'invoice_id' => rand(1, 10), // Replace 1 and 10 with your invoice IDs
        ];
    }
}
