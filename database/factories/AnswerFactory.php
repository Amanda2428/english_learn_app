<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        $questionId = Question::query()->inRandomOrder()->value('question_id') ?? Question::factory();

        return [
            'question_id' => $questionId,
            'answer_text' => fake()->sentence(),
            'is_correct' => false,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function correct(): static
    {
        return $this->state(fn () => [
            'is_correct' => true,
        ]);
    }

    public function forQuestion(int $questionId, bool $isCorrect = false): static
    {
        return $this->state(fn () => [
            'question_id' => $questionId,
            'is_correct' => $isCorrect,
        ]);
    }
}