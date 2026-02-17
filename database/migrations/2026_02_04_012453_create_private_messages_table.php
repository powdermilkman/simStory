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
        Schema::create('private_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('characters')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('characters')->onDelete('cascade');
            $table->string('subject');
            $table->text('content');
            $table->timestamp('fake_sent_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('required_choice_option_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_messages');
    }
};
