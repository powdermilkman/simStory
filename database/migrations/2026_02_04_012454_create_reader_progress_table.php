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
        Schema::create('reader_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_id')->constrained()->onDelete('cascade');
            $table->foreignId('choice_option_id')->constrained()->onDelete('cascade');
            $table->timestamp('chosen_at');
            $table->timestamps();
            
            $table->unique(['reader_id', 'choice_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_progress');
    }
};
