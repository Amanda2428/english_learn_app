<?php
// database/factories/QuestionFactory.php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Skill;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        $skill = Skill::inRandomOrder()->first() ?? Skill::factory()->create();

        // Randomly decide if this question is associated with a video (50% chance)
        $videoId = null;
        if ($this->faker->boolean(50)) {
            $video = Video::where('skill_id', $skill->skill_id)->inRandomOrder()->first();
            $videoId = $video ? $video->video_id : null;
        }

        return [
            'question_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'skill_id' => $skill->skill_id,
            'video_id' => $videoId,
            'question_text' => $this->faker->sentence() . '?',
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'points' => $this->faker->numberBetween(5, 50),
            'question_type' => $this->faker->randomElement(['multiple_choice', 'true_false', 'fill_blank']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State for easy questions
     */
    public function easy(): static
    {
        return $this->state(fn(array $attributes) => [
            'difficulty' => 'easy',
            'points' => $this->faker->numberBetween(5, 10),
        ]);
    }

    /**
     * State for medium questions
     */
    public function medium(): static
    {
        return $this->state(fn(array $attributes) => [
            'difficulty' => 'medium',
            'points' => $this->faker->numberBetween(11, 25),
        ]);
    }

    /**
     * State for hard questions
     */
    public function hard(): static
    {
        return $this->state(fn(array $attributes) => [
            'difficulty' => 'hard',
            'points' => $this->faker->numberBetween(26, 50),
        ]);
    }

    /**
     * State for multiple choice questions
     */
    public function multipleChoice(): static
    {
        return $this->state(fn(array $attributes) => [
            'question_type' => 'multiple_choice',
        ]);
    }

    /**
     * State for true/false questions
     */
    public function trueFalse(): static
    {
        return $this->state(fn(array $attributes) => [
            'question_type' => 'true_false',
        ]);
    }

    /**
     * State for fill in the blank questions
     */
    public function chooseCorrectOne(): static
    {
        return $this->state(fn(array $attributes) => [
            'question_type' => 'choose_correct_one',
        ]);
    }

    /**
     * State for video-specific questions
     */
    public function withVideo(): static
    {
        return $this->state(function (array $attributes) {
            $skillId = $attributes['skill_id'] ?? Skill::inRandomOrder()->first()->skill_id;
            $video = Video::where('skill_id', $skillId)->inRandomOrder()->first();

            return [
                'video_id' => $video ? $video->video_id : null,
            ];
        });
    }

    /**
     * State for questions without video
     */
    public function withoutVideo(): static
    {
        return $this->state(fn(array $attributes) => [
            'video_id' => null,
        ]);
    }

    /**
     * For a specific skill
     */
    public function forSkill(int $skillId): static
    {
        return $this->state(fn(array $attributes) => [
            'skill_id' => $skillId,
        ]);
    }

    /**
     * For a specific video
     */
    public function forVideo(int $videoId): static
    {
        return $this->state(fn(array $attributes) => [
            'video_id' => $videoId,
        ]);
    }
}
