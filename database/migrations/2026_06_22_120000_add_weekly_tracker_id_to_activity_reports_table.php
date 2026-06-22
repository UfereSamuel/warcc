<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_reports', function (Blueprint $table) {
            $table->foreignId('weekly_tracker_id')
                ->nullable()
                ->after('activity_calendar_id')
                ->constrained('weekly_trackers')
                ->nullOnDelete();

            $table->unique('weekly_tracker_id');
        });
    }

    public function down(): void
    {
        Schema::table('activity_reports', function (Blueprint $table) {
            $table->dropForeign(['weekly_tracker_id']);
            $table->dropUnique(['weekly_tracker_id']);
            $table->dropColumn('weekly_tracker_id');
        });
    }
};
