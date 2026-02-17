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
        Schema::create('reader_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_id')->constrained()->onDelete('cascade');
            $table->string('visitable_type'); // category, thread
            $table->unsignedBigInteger('visitable_id');
            $table->timestamp('last_visited_at');
            $table->timestamps();
            
            $table->unique(['reader_id', 'visitable_type', 'visitable_id'], 'reader_visit_unique');
            $table->index(['visitable_type', 'visitable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reader_visits');
    }
};
