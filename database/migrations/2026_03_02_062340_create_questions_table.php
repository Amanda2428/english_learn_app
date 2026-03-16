<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->unsignedBigInteger('skill_id');
            $table->unsignedBigInteger('video_id')->nullable();
            $table->text('question_text');
            $table->string('difficulty')->default('easy');
            $table->integer('points')->default(0);
            $table->string('question_type')->default('multiple_choice');
            $table->timestamps();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')->references('level_id')->on('levels')->nullOnDelete();

            $table->foreign('skill_id')
                ->references('skill_id')
                ->on('skills')
                ->cascadeOnDelete();

            $table->foreign('video_id')
                ->references('video_id')
                ->on('videos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
