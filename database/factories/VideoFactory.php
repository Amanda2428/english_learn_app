<?php
// database/factories/VideoFactory.php

namespace Database\Factories;

use App\Models\Video;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition(): array
    {
        return [
            'video_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'video_file' => '/storage/videos/' . $this->faker->slug() . '.mp4',
            'duration' => $this->faker->time('H:i:s'), // Format: 00:05:11
            'skill_id' => Skill::inRandomOrder()->first()->skill_id ?? Skill::factory()->create()->skill_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forSkill(int $skillId): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_id' => $skillId,
        ]);
    }
}