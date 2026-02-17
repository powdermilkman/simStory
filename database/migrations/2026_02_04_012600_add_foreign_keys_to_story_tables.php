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
        Schema::table('threads', function (Blueprint $table) {
            $table->foreign('required_choice_option_id')
                ->references('id')
                ->on('choice_options')
                ->onDelete('set null');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('required_choice_option_id')
                ->references('id')
                ->on('choice_options')
                ->onDelete('set null');
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->foreign('required_choice_option_id')
                ->references('id')
                ->on('choice_options')
                ->onDelete('set null');
        });

        Schema::table('choices', function (Blueprint $table) {
            $table->foreign('trigger_post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['required_choice_option_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['required_choice_option_id']);
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropForeign(['required_choice_option_id']);
        });

        Schema::table('choices', function (Blueprint $table) {
            $table->dropForeign(['trigger_post_id']);
        });
    }
};
