<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preference_games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->boolean('is_eliminatory_phase')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preference_games');
    }
};
