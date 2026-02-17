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
        Schema::create('reader_character_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_id')->constrained()->onDelete('cascade');
            $table->json('overrides')->default('{}');
            $table->timestamps();

            $table->unique(['reader_id', 'character_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_character_states');
    }
};
