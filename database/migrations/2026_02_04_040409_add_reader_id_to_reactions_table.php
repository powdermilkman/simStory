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
        // Drop all foreign keys first
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropForeign(['character_id']);
        });

        // Then drop the unique constraint that uses character_id
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropUnique(['post_id', 'character_id', 'type']);
        });

        Schema::table('reactions', function (Blueprint $table) {
            // Make character_id nullable for reader reactions
            $table->foreignId('character_id')->nullable()->change();

            // Add reader_id for reader reactions
            $table->foreignId('reader_id')->nullable()->after('character_id')
                ->constrained()->onDelete('cascade');
        });

        // Recreate the foreign keys
        Schema::table('reactions', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('character_id')->references('id')->on('characters')->onDelete('cascade');
        });

        // Add new unique constraints
        Schema::table('reactions', function (Blueprint $table) {
            // Unique constraint for character reactions
            $table->unique(['post_id', 'character_id', 'type'], 'reactions_character_unique');
            // Unique constraint for reader reactions
            $table->unique(['post_id', 'reader_id', 'type'], 'reactions_reader_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropUnique('reactions_character_unique');
            $table->dropUnique('reactions_reader_unique');
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->dropForeign(['reader_id']);
            $table->dropColumn('reader_id');
            $table->foreignId('character_id')->nullable(false)->change();
            $table->unique(['post_id', 'character_id', 'type']);
        });
    }
};
