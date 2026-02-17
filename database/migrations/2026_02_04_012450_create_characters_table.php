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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('display_name');
            $table->string('avatar_path')->nullable();
            $table->text('signature')->nullable();
            $table->timestamp('fake_join_date')->nullable();
            $table->string('role_title')->nullable(); // e.g., "Simulation Enthusiast", "Mind State Archivist"
            $table->integer('post_count')->default(0);
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
