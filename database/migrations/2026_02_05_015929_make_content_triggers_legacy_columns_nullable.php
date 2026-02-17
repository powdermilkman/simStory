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
        // SQLite doesn't support modifying columns directly, so we need to recreate the table
        Schema::table('content_triggers', function (Blueprint $table) {
            // For SQLite, we'll create a new table with the correct schema
        });

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
