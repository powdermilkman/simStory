<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the new conditions table
        Schema::create('trigger_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_trigger_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'view_post', 'view_thread', 'react_post', 'choice'
            $table->string('target_type')->nullable(); // 'post', 'thread'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->foreignId('choice_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing trigger conditions to the new table
        $triggers = DB::table('content_triggers')->whereNotNull('type')->get();
        foreach ($triggers as $trigger) {
            DB::table('trigger_conditions')->insert([
                'content_trigger_id' => $trigger->id,
                'type' => $trigger->type,
                'target_type' => $trigger->target_type,
                'target_id' => $trigger->target_id,
                'choice_option_id' => $trigger->choice_option_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove the old columns from content_triggers (optional - keeping for now as backup)
        // We'll keep type, target_type, target_id, choice_option_id as legacy fields
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trigger_conditions');
    }
};
