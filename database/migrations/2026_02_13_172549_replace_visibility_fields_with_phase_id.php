<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['threads', 'posts', 'private_messages'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBp) {
                // Add phase_id column
                $tableBp->foreignId('phase_id')->nullable()->constrained('phases')->onDelete('set null');

                // Drop old foreign keys and columns
                $tableBp->dropForeign(['required_trigger_id']);
                $tableBp->dropColumn('required_trigger_id');

                $tableBp->dropForeign(['required_choice_option_id']);
                $tableBp->dropColumn('required_choice_option_id');
            });
        }
    }

    public function down(): void
    {
        $tables = ['threads', 'posts', 'private_messages'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBp) {
                $tableBp->unsignedBigInteger('required_choice_option_id')->nullable();
                $tableBp->foreign('required_choice_option_id')->references('id')->on('choice_options')->onDelete('set null');
                $tableBp->unsignedBigInteger('required_trigger_id')->nullable();
                $tableBp->foreign('required_trigger_id')->references('id')->on('content_triggers')->onDelete('set null');
            });
        }

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBp) {
                $tableBp->dropForeign(['phase_id']);
                $tableBp->dropColumn('phase_id');
            });
        }
    }
};
