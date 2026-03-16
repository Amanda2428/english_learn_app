<?php

namespace Database\Factories;

use App\Models\ChatbotSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotSessionFactory extends Factory
{
    protected $model = ChatbotSession::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $startedAt = fake()->dateTimeBetween('-30 days', 'now');
        $lastMsgAt = fake()->optional(0.8)->dateTimeBetween($startedAt, 'now');

        return [
            'user_id' => $user->id,
            'started_at' => $startedAt,
            'last_msg_at' => $lastMsgAt,
            'created_at' => $startedAt,
            'updated_at' => $lastMsgAt ?? $startedAt,
        ];
    }

    public function forUser(int $userId): static
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }
}