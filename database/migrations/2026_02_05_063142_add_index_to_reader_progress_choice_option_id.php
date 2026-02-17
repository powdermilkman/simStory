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
        Schema::table('reader_progress', function (Blueprint $table) {
            $table->index('choice_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reader_progress', function (Blueprint $table) {
            $table->dropIndex(['choice_option_id']);
        });
    }
};
