<?php

namespace Database\Factories;

use App\Models\ChatbotMessage;
use App\Models\ChatbotSession;
use App\Models\ChatbotRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotMessageFactory extends Factory
{
    protected $model = ChatbotMessage::class;

    public function definition(): array
    {
        $session = ChatbotSession::inRandomOrder()->first() ?? ChatbotSession::factory()->create();
        $rule = ChatbotRule::inRandomOrder()->first();

        $createdAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'session_id' => $session->session_id,
            'user_message' => fake()->sentence(),
            'bot_response' => fake()->paragraph(),
            'link_url' => fake()->optional(0.3)->url(),
            'link_title' => fake()->optional(0.3)->words(3, true),
            'rule_id' => $rule?->rule_id,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function forSession(int $sessionId): static
    {
        return $this->state(fn () => [
            'session_id' => $sessionId,
        ]);
    }
}