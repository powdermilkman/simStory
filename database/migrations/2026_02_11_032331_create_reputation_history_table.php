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
        Schema::create('reputation_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained()->onDelete('cascade');
            $table->foreignId('reputation_type_id')->constrained()->onDelete('cascade');
            $table->integer('previous_value');
            $table->integer('new_value');
            $table->integer('change_amount');
            $table->string('reason');
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reputation_history');
    }
};
