<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id('progress_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('skill_id')->nullable();

            $table->json('completed_questions')->nullable();

            $table->integer('points_earned')->default(0);
            $table->integer('completion_percentage')->default(0);

            $table->integer('videos_watched')->default(0);
            $table->integer('total_videos_in_skill')->default(0);

            $table->integer('questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('total_questions_in_skill')->default(0);

            $table->integer('time_spent_minutes')->default(0);

            $table->enum('status', ['not_started', 'in_progress', 'completed'])
                ->default('not_started');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('level_id')->references('level_id')->on('levels')->cascadeOnDelete();
            $table->foreign('skill_id')->references('skill_id')->on('skills')->cascadeOnDelete();

            $table->unique(['user_id', 'level_id', 'skill_id'], 'unique_user_level_skill');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};