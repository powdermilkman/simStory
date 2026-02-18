<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * The content_triggers table originally had single-condition fields (type, target_type, target_id).
     * Now we use the trigger_conditions table for multiple conditions, so these fields should be nullable.
     */
    public function up(): void
    {
        // Helper function to check if a foreign key exists
        $foreignKeyExists = function($table, $constraint) {
            $result = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$table, $constraint]);
            return count($result) > 0;
        };

        // Drop foreign keys that reference content_triggers (only if they exist)
        if ($foreignKeyExists('threads', 'threads_required_trigger_id_foreign')) {
            DB::statement('ALTER TABLE threads DROP FOREIGN KEY threads_required_trigger_id_foreign');
        }
        if ($foreignKeyExists('posts', 'posts_required_trigger_id_foreign')) {
            DB::statement('ALTER TABLE posts DROP FOREIGN KEY posts_required_trigger_id_foreign');
        }
        if ($foreignKeyExists('private_messages', 'private_messages_required_trigger_id_foreign')) {
            DB::statement('ALTER TABLE private_messages DROP FOREIGN KEY private_messages_required_trigger_id_foreign');
        }
        if ($foreignKeyExists('trigger_conditions', 'trigger_conditions_content_trigger_id_foreign')) {
            DB::statement('ALTER TABLE trigger_conditions DROP FOREIGN KEY trigger_conditions_content_trigger_id_foreign');
        }

        // Drop the temporary table if it exists from a previous failed run
        Schema::dropIfExists('content_triggers_new');

        // Create a temporary table with the correct schema
        Schema::create('content_triggers_new', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier')->unique();
            $table->string('type')->nullable(); // Now nullable
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->foreignId('choice_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Copy data from old table to new table
        $triggers = DB::table('content_triggers')->get();
        foreach ($triggers as $trigger) {
            DB::table('content_triggers_new')->insert([
                'id' => $trigger->id,
                'name' => $trigger->name,
                'identifier' => $trigger->identifier,
                'type' => $trigger->type,
                'target_type' => $trigger->target_type,
                'target_id' => $trigger->target_id,
                'choice_option_id' => $trigger->choice_option_id,
                'description' => $trigger->description,
                'created_at' => $trigger->created_at,
                'updated_at' => $trigger->updated_at,
            ]);
        }

        // Drop old table and rename new table
        Schema::dropIfExists('content_triggers');
        Schema::rename('content_triggers_new', 'content_triggers');

        // Recreate the foreign keys
        Schema::table('threads', function (Blueprint $table) {
            $table->foreign('required_trigger_id')->references('id')->on('content_triggers')->onDelete('set null');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('required_trigger_id')->references('id')->on('content_triggers')->onDelete('set null');
        });

        Schema::table('private_messages', function (Blueprint $table) {
            $table->foreign('required_trigger_id')->references('id')->on('content_triggers')->onDelete('set null');
        });

        Schema::table('trigger_conditions', function (Blueprint $table) {
            $table->foreign('content_trigger_id')->references('id')->on('content_triggers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate with NOT NULL type column
        Schema::create('content_triggers_old', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier')->unique();
            $table->string('type');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->foreignId('choice_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $triggers = DB::table('content_triggers')->get();
        foreach ($triggers as $trigger) {
            DB::table('content_triggers_old')->insert([
                'id' => $trigger->id,
                'name' => $trigger->name,
                'identifier' => $trigger->identifier,
                'type' => $trigger->type ?? 'view_post', // Default for rollback
                'target_type' => $trigger->target_type,
                'target_id' => $trigger->target_id,
                'choice_option_id' => $trigger->choice_option_id,
                'description' => $trigger->description,
                'created_at' => $trigger->created_at,
                'updated_at' => $trigger->updated_at,
            ]);
        }

        Schema::dropIfExists('content_triggers');
        Schema::rename('content_triggers_old', 'content_triggers');
    }
};
