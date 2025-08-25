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
        // First, update any existing data to match the new enum values
        DB::statement("UPDATE weekly_trackers SET status = 'at_duty_station' WHERE status = 'duty_station'");
        DB::statement("UPDATE weekly_trackers SET status = 'on_mission' WHERE status = 'mission'");
        DB::statement("UPDATE weekly_trackers SET status = 'on_leave' WHERE status = 'leave'");
        
        // Then modify the enum column to use the correct values
        DB::statement("ALTER TABLE weekly_trackers MODIFY COLUMN status ENUM('at_duty_station', 'on_mission', 'on_leave') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data back to original enum values
        DB::statement("UPDATE weekly_trackers SET status = 'duty_station' WHERE status = 'at_duty_station'");
        DB::statement("UPDATE weekly_trackers SET status = 'mission' WHERE status = 'on_mission'");
        DB::statement("UPDATE weekly_trackers SET status = 'leave' WHERE status = 'on_leave'");
        
        // Revert the enum column to original values
        DB::statement("ALTER TABLE weekly_trackers MODIFY COLUMN status ENUM('duty_station', 'mission', 'leave') NOT NULL");
    }
};
