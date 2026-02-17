<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('choice_options', function (Blueprint $table) {
            // JSON column to store vote counts to display when THIS option is chosen
            // Format: {"option_id_1": 150, "option_id_2": 350, ...}
            $table->json('result_votes')->nullable()->after('vote_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('choice_options', function (Blueprint $table) {
            $table->dropColumn('result_votes');
        });
    }
};
