<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_trackers', function (Blueprint $table) {
            $table->foreignId('activity_calendar_id')
                ->nullable()
                ->after('status')
                ->constrained('activity_calendars')
                ->nullOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE activity_calendars MODIFY COLUMN type ENUM('meeting', 'training', 'event', 'mission', 'workshop', 'holiday', 'deadline') NOT NULL DEFAULT 'event'");
        }
    }

    public function down(): void
    {
        Schema::table('weekly_trackers', function (Blueprint $table) {
            $table->dropForeign(['activity_calendar_id']);
            $table->dropColumn('activity_calendar_id');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE activity_calendars MODIFY COLUMN type ENUM('meeting', 'training', 'event', 'holiday', 'deadline') NOT NULL DEFAULT 'event'");
        }
    }
};
