<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->unsignedBigInteger('session_id');
            $table->text('user_message');
            $table->text('bot_response')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_title')->nullable();
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->timestamps();

            $table->foreign('session_id')->references('session_id')->on('chatbot_sessions')->onDelete('cascade');
            $table->foreign('rule_id')->references('rule_id')->on('chatbot_rules')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
    }
};