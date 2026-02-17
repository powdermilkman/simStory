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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('character_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'like', 'insightful', 'amusing', 'agree'
            $table->timestamp('fake_created_at')->nullable();
            $table->timestamps();
            
            $table->unique(['post_id', 'character_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
