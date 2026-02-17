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
        Schema::create('phase_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained()->onDelete('cascade');
            $table->string('type'); // modify_reputation, unlock_content, modify_character, send_message, trigger_phase
            $table->string('target_type')->nullable(); // character, thread, post, message, phase
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('action_data')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_actions');
    }
};
