<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\ChatbotMessage;
use App\Models\ChatbotRule;
use App\Models\ChatbotSession;
use App\Models\Level;
use App\Models\Question;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('chatbot_messages')->truncate();
            DB::table('chatbot_sessions')->truncate();
            DB::table('chatbot_rules')->truncate();
            DB::table('answers')->truncate();
            DB::table('questions')->truncate();
            DB::table('level_skill')->truncate();
            DB::table('user_progress')->truncate();
            DB::table('videos')->truncate();
            DB::table('users')->truncate();
            DB::table('skills')->truncate();
            DB::table('levels')->truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            echo "Creating levels...\n";
            $levels = Level::factory()->count(6)->create();

            echo "Creating skills...\n";
            $skills = Skill::factory()->count(6)->create();

            echo "Attaching skills to levels...\n";
            foreach ($levels as $level) {
                $randomSkills = $skills->random(rand(2, 4))->pluck('skill_id')->toArray();
                $level->skills()->syncWithoutDetaching($randomSkills);
            }

            echo "Creating videos and associating with skills...\n";
            foreach (range(1, 10) as $i) {
                $video = Video::factory()->create([
                    'skill_id' => $skills->random()->skill_id
                ]);
            }

            echo "Creating chatbot rules...\n";
            ChatbotRule::factory()->count(20)->create();

            echo "Creating users...\n";
            $users = User::factory()->count(10)->create();

            echo "Creating questions...\n";
            $questions = Question::factory()->count(20)->create();

            echo "Creating answers...\n";
            foreach ($questions as $question) {
                Answer::factory()->count(4)->forQuestion($question->question_id)->create();

                $correctAnswer = $question->answers()->inRandomOrder()->first();
                if ($correctAnswer) {
                    $correctAnswer->update(['is_correct' => true]);
                }
            }

            echo "Creating chatbot sessions...\n";
            $sessions = ChatbotSession::factory()->count(30)->create();

            echo "Creating chatbot messages...\n";
            ChatbotMessage::factory()->count(100)->create();

            echo "Creating user progress...\n";
            $progressCount = 0;
            $maxProgressRecords = 50;
            $maxAttempts = 500;

            for ($i = 0; $i < $maxProgressRecords; $i++) {
                try {
                    UserProgress::factory()->create();
                    $progressCount++;
                } catch (\Exception $e) {

                    continue;
                }
            }

            echo "Seeding completed successfully!\n";
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            throw $e;
        }
    }
}
