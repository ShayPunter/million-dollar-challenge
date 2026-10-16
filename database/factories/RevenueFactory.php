<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Revenue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Revenue>
 */
class RevenueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            // Revenue belongs to the project's owner.
            'user_id' => fn (array $attributes) => Project::find($attributes['project_id'])->user_id,
            'amount' => fake()->randomFloat(2, 1, 5000),
        ];
    }
}
