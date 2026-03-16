<?php

namespace Database\Factories;

use App\Models\ChatbotRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotRuleFactory extends Factory
{
    protected $model = ChatbotRule::class;

    public function definition(): array
    {
        $keywords = ['hello', 'help', 'course', 'price', 'schedule', 'teacher', 'level', 'test'];
        $hasLink = fake()->boolean(30);

        return [
            'keyword' => fake()->randomElement($keywords),
            'response_text' => fake()->paragraph(),
            'link_url' => $hasLink ? fake()->url() : null,
            'link_title' => $hasLink ? fake()->words(3, true) : null,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function withLink(): static
    {
        return $this->state(fn () => [
            'link_url' => fake()->url(),
            'link_title' => fake()->words(3, true),
        ]);
    }

    public function withKeyword(string $keyword): static
    {
        return $this->state(fn () => [
            'keyword' => $keyword,
        ]);
    }
}