<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE activity_requests MODIFY COLUMN type ENUM('meeting', 'training', 'event', 'mission', 'workshop', 'holiday', 'deadline') NOT NULL DEFAULT 'event'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE activity_requests MODIFY COLUMN type ENUM('meeting', 'training', 'event', 'holiday', 'deadline') NOT NULL DEFAULT 'event'");
        }
    }
};
