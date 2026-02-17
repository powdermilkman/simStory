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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained('characters')->onDelete('cascade');
            $table->text('content');
            $table->timestamp('fake_created_at')->nullable();
            $table->timestamp('fake_edited_at')->nullable();
            $table->unsignedBigInteger('required_choice_option_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
