<?php

namespace Database\Factories;

use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Work>
 */
class WorkFactory extends Factory
{
    protected $model = Work::class;

    public function definition()
    {
        return [
            'date' => fake()->date(),
            'hours' => fake()->randomFloat(2, 0, 24),
            'description' => fake()->paragraph(),
        ];
    }
}
