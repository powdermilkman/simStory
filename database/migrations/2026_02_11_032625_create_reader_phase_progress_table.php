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
        Schema::create('reader_phase_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_id')->constrained()->onDelete('cascade');
            $table->foreignId('phase_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('not_started'); // not_started, in_progress, completed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['reader_id', 'phase_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_phase_progress');
    }
};
