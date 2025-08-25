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
        DB::statement("UPDATE weekly_trackers SET submission_status = 'draft' WHERE submission_status = 'pending'");
        
        // Then modify the enum column to include 'draft' and keep other values
        DB::statement("ALTER TABLE weekly_trackers MODIFY COLUMN submission_status ENUM('draft', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data back to original enum values
        DB::statement("UPDATE weekly_trackers SET submission_status = 'pending' WHERE submission_status = 'draft'");
        
        // Revert the enum column to original values
        DB::statement("ALTER TABLE weekly_trackers MODIFY COLUMN submission_status ENUM('pending', 'submitted', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};