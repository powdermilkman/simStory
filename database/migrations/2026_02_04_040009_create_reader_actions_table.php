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
        Schema::create('reader_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_id')->constrained()->onDelete('cascade');
            $table->string('action_type'); // view_post, view_thread, react_post
            $table->string('target_type'); // post, thread
            $table->unsignedBigInteger('target_id');
            $table->timestamp('performed_at');
            $table->timestamps();
            
            // Prevent duplicate actions
            $table->unique(['reader_id', 'action_type', 'target_type', 'target_id'], 'reader_action_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_actions');
    }
};
