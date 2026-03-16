<?php
// database/factories/UserProgressFactory.php

namespace Database\Factories;

use App\Models\UserProgress;
use App\Models\User;
use App\Models\Level;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class UserProgressFactory extends Factory
{
    protected $model = UserProgress::class;

    public function definition(): array
    {
        // Get all existing combinations to avoid duplicates
        $existingCombinations = UserProgress::select('user_id', 'level_id', 'skill_id')
            ->get()
            ->map(fn($item) => $item->user_id . '-' . $item->level_id . '-' . $item->skill_id)
            ->toArray();
        
        $maxAttempts = 50;
        $attempt = 0;
        
        do {
            // Get a random user
            $user = User::inRandomOrder()->first() ?? User::factory()->create();
            
            // Get a random skill
            $skill = Skill::inRandomOrder()->first();
            if (!$skill) {
                $skill = Skill::factory()->create();
            }
            
            // Get level_id from pivot table
            $levelId = DB::table('level_skill')
                ->where('skill_id', $skill->skill_id)
                ->inRandomOrder()
                ->value('level_id');
            
            // If no level found, create one and attach
            if (!$levelId) {
                $level = Level::factory()->create();
                DB::table('level_skill')->insert([
                    'level_id' => $level->level_id,
                    'skill_id' => $skill->skill_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $levelId = $level->level_id;
            }
            
            $combination = $user->id . '-' . $levelId . '-' . $skill->skill_id;
            $attempt++;
            
            // If we've tried too many times, create a new user
            if ($attempt >= $maxAttempts) {
                $user = User::factory()->create();
                $combination = $user->id . '-' . $levelId . '-' . $skill->skill_id;
                break;
            }
            
        } while (in_array($combination, $existingCombinations));
        
        // Get video and question counts
        $totalVideos = \App\Models\Video::where('skill_id', $skill->skill_id)->count() ?: fake()->numberBetween(5, 20);
        $totalQuestions = \App\Models\Question::where('skill_id', $skill->skill_id)->count() ?: fake()->numberBetween(10, 30);
        
        $status = fake()->randomElement(['not_started', 'in_progress', 'completed']);
        $startedAt = $status !== 'not_started' ? fake()->dateTimeBetween('-30 days', 'now') : null;
        $completedAt = $status === 'completed' ? fake()->dateTimeBetween($startedAt ?? '-20 days', 'now') : null;
        
        $videosWatched = $status === 'not_started' ? 0 : 
                        ($status === 'completed' ? $totalVideos : fake()->numberBetween(1, $totalVideos - 1));
        
        $questionsAnswered = $status === 'not_started' ? 0 : 
                            ($status === 'completed' ? $totalQuestions : fake()->numberBetween(1, $totalQuestions - 1));
        
        $correctAnswers = $status === 'not_started' ? 0 : 
                         ($status === 'completed' ? $totalQuestions : fake()->numberBetween(0, $questionsAnswered));
        
        $pointsEarned = ($videosWatched * 10) + ($correctAnswers * 5);
        
        $videoPercentage = $totalVideos > 0 ? ($videosWatched / $totalVideos) * 100 : 0;
        $questionPercentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $completionPercentage = round(($videoPercentage + $questionPercentage) / 2);
        
        $timeSpentMinutes = $status === 'not_started' ? 0 :
                           ($status === 'completed' ? fake()->numberBetween(60, 300) : fake()->numberBetween(10, 120));
        
        return [
            'user_id' => $user->id,
            'level_id' => $levelId,
            'skill_id' => $skill->skill_id,
            
            'points_earned' => $pointsEarned,
            'completion_percentage' => $completionPercentage,
            
            'videos_watched' => $videosWatched,
            'total_videos_in_skill' => $totalVideos,
            
            'questions_answered' => $questionsAnswered,
            'correct_answers' => $correctAnswers,
            'total_questions_in_skill' => $totalQuestions,
            
            'time_spent_minutes' => $timeSpentMinutes,
            
            'status' => $status,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            
            'created_at' => $startedAt ?? fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create multiple unique progress records
     */
    public function configure()
    {
        return $this->afterCreating(function (UserProgress $progress) {
            // No action needed after creation
        });
    }

    /**
     * State for completed progress
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $totalVideos = $attributes['total_videos_in_skill'] ?? 20;
            $totalQuestions = $attributes['total_questions_in_skill'] ?? 30;
            
            return [
                'status' => 'completed',
                'completion_percentage' => 100,
                'videos_watched' => $totalVideos,
                'questions_answered' => $totalQuestions,
                'correct_answers' => $totalQuestions,
                'points_earned' => ($totalVideos * 10) + ($totalQuestions * 5),
                'time_spent_minutes' => fake()->numberBetween(120, 300),
                'started_at' => fake()->dateTimeBetween('-30 days', '-10 days'),
                'completed_at' => fake()->dateTimeBetween('-9 days', 'now'),
            ];
        });
    }

    /**
     * State for in-progress progress
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $totalVideos = $attributes['total_videos_in_skill'] ?? 20;
            $totalQuestions = $attributes['total_questions_in_skill'] ?? 30;
            
            $videosWatched = fake()->numberBetween(1, $totalVideos - 1);
            $questionsAnswered = fake()->numberBetween(1, $totalQuestions - 1);
            $correctAnswers = fake()->numberBetween(0, $questionsAnswered);
            
            $videoPercentage = ($videosWatched / $totalVideos) * 100;
            $questionPercentage = ($correctAnswers / $totalQuestions) * 100;
            $completionPercentage = round(($videoPercentage + $questionPercentage) / 2);
            
            return [
                'status' => 'in_progress',
                'completion_percentage' => $completionPercentage,
                'videos_watched' => $videosWatched,
                'questions_answered' => $questionsAnswered,
                'correct_answers' => $correctAnswers,
                'points_earned' => ($videosWatched * 10) + ($correctAnswers * 5),
                'time_spent_minutes' => fake()->numberBetween(15, 90),
                'started_at' => fake()->dateTimeBetween('-15 days', 'now'),
                'completed_at' => null,
            ];
        });
    }

    /**
     * State for not started progress
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'not_started',
            'completion_percentage' => 0,
            'videos_watched' => 0,
            'questions_answered' => 0,
            'correct_answers' => 0,
            'points_earned' => 0,
            'time_spent_minutes' => 0,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * State for specific user
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * State for specific level
     */
    public function forLevel(int $levelId): static
    {
        return $this->state(fn (array $attributes) => [
            'level_id' => $levelId,
        ]);
    }

    /**
     * State for specific skill
     */
    public function forSkill(int $skillId): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_id' => $skillId,
        ]);
    }
}