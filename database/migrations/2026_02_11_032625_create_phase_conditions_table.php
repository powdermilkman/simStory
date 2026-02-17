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
        Schema::create('phase_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained()->onDelete('cascade');
            $table->string('type'); // trigger, choice, view_post, view_thread, react_post, all_posts_in_thread, report_post, phase_complete
            $table->string('target_type')->nullable(); // post, thread, trigger, phase
            $table->unsignedBigInteger('target_id')->nullable();
            $table->foreignId('choice_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_conditions');
    }
};
