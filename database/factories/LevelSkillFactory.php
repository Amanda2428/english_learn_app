<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Skill;
use App\Models\LevelSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelSkillFactory extends Factory
{
    protected $model = LevelSkill::class;

    public function definition(): array
    {
        return [
            'level_id' => Level::query()->inRandomOrder()->value('level_id') ?? Level::factory(),
            'skill_id' => Skill::query()->inRandomOrder()->value('skill_id') ?? Skill::factory(),
        ];
    }
}