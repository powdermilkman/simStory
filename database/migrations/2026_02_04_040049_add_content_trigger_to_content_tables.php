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
            $table->foreignId('required_trigger_id')->nullable()->after('required_choice_option_id')
                ->constrained('content_triggers')->onDelete('set null');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('required_trigger_id')->nullable()->after('required_choice_option_id')
                ->constrained('content_triggers')->onDelete('set null');
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->foreignId('required_trigger_id')->nullable()->after('required_choice_option_id')
                ->constrained('content_triggers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['required_trigger_id']);
            $table->dropColumn('required_trigger_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['required_trigger_id']);
            $table->dropColumn('required_trigger_id');
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropForeign(['required_trigger_id']);
            $table->dropColumn('required_trigger_id');
        });
    }
};
