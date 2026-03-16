<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id('video_id');
            $table->unsignedBigInteger('skill_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_file');
            $table->time('duration');
            $table->timestamps();

            $table->foreign('skill_id')
                ->references('skill_id')
                ->on('skills')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};