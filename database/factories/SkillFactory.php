<?php
// database/factories/SkillFactory.php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'skill_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'skill_name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}