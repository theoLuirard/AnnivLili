<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preference_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->foreign('game_id')->references('id')->on('preference_games')->onDelete('cascade');
            $table->text('question_text');
            $table->string('option_a');
            $table->string('option_b');
            $table->enum('correct_answer', ['a', 'b'])->nullable();
            $table->boolean('is_eliminatory')->default(false);
            $table->enum('status', ['pending', 'active', 'revealing', 'closed'])->default('pending');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preference_questions');
    }
};
