<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition(): array
    {
        static $usedOrders = 0;
        $usedOrders++;

        $levelNames = [
            'Beginner',
            'Elementary',
            'Intermediate',
            'Upper Intermediate',
            'Advanced',
            'Expert',
        ];

        return [
            'level_name' => $levelNames[$usedOrders - 1] ?? 'Level ' . $usedOrders,
            'level_order' => $usedOrders,
            'description' => fake()->paragraph(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function beginner(): static
    {
        return $this->state(fn () => [
            'level_name' => 'Beginner',
            'level_order' => 1,
        ]);
    }

    public function advanced(): static
    {
        return $this->state(fn () => [
            'level_name' => 'Advanced',
            'level_order' => 5,
        ]);
    }
}