<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //$faker = Faker\Factory::create();
        return [
            'name'      =>  $this->faker->name(),
            'email'     =>  fake()->unique()->safeEmail(),
            'vat'       =>  'GB'.fake()->numberBetween(100000,9999999),
            'address'   =>  fake()->address(),
            'note'      =>  $this->faker->catchPhrase(),
            'senderContacts'    => $this->faker->name." ".$this->faker->numerify('+## ###-#######'),
            'receiverContacts'  => $this->faker->name." ".$this->faker->numerify('+## ###-#######'),
            'collection_details' => $this->faker->text,
            'dropoff_details'   =>  $this->faker->text,
        ];
    }
}
