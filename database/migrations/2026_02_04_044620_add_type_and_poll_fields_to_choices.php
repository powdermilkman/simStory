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
        Schema::table('choices', function (Blueprint $table) {
            $table->string('type')->default('choice')->after('prompt_text'); // 'choice' or 'poll'
            $table->integer('total_votes')->default(0)->after('type'); // Fake total votes for polls
        });

        Schema::table('choice_options', function (Blueprint $table) {
            // For polls - the fake percentage to display
            $table->integer('vote_percentage')->default(0)->after('description');
            
            // For choices - a post that gets spawned/revealed when this option is chosen
            $table->foreignId('spawned_post_id')->nullable()->after('vote_percentage')
                ->constrained('posts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('choice_options', function (Blueprint $table) {
            $table->dropForeign(['spawned_post_id']);
            $table->dropColumn(['vote_percentage', 'spawned_post_id']);
        });

        Schema::table('choices', function (Blueprint $table) {
            $table->dropColumn(['type', 'total_votes']);
        });
    }
};
