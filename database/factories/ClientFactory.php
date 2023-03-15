<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->optional()->text(),
            'contact_email' => fake()->optional()->email(),
            'contact_phone' => fake()->optional()->phoneNumber(),
        ];
    }
}
