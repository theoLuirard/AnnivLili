<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preference_games', function (Blueprint $table) {
            $table->boolean('show_podium')->default(false)->after('is_eliminatory_phase');
        });
    }

    public function down(): void
    {
        Schema::table('preference_games', function (Blueprint $table) {
            $table->dropColumn('show_podium');
        });
    }
};
