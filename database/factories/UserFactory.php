<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $level = Level::inRandomOrder()->first() ?? Level::factory()->create();

        $profilePictures = [
            'avatar1.jpg',
            'avatar2.jpg',
            'avatar3.jpg',
            'avatar4.jpg',
            'avatar5.jpg',
            'profile1.png',
            'profile2.png',
            'profile3.png',
            'user1.jpg',
            'user2.jpg',
        ];

        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'name' => fake()->name(),
            'profile' => fake()->randomElement($profilePictures),
            'bio' => fake()->sentence(),
            'role' => fake()->randomElement([0, 1]),
            'level_id' => $level->level_id,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 1,
        ]);
    }

    public function regular(): static
    {
        return $this->state(fn () => [
            'role' => 0,
        ]);
    }
}