<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPage>
 */
class UserPageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => fake()->unique()->slug(2),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
        ];
    }
}
