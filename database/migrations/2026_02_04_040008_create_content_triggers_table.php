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
        Schema::create('content_triggers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Human-readable name for admin
            $table->string('identifier')->unique(); // For code reference
            $table->string('type'); // view_post, view_thread, react_post, choice
            
            // The target of the trigger (what needs to be viewed/reacted to)
            $table->string('target_type')->nullable(); // post, thread
            $table->unsignedBigInteger('target_id')->nullable();
            
            // For choice-type triggers, link to choice option
            $table->foreignId('choice_option_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_triggers');
    }
};
