<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_reminder_logs', function (Blueprint $table) {
            $table->date('week_start_date')->nullable()->after('activity_calendar_id');
            $table->index(['staff_id', 'reminder_type', 'week_start_date'], 'email_reminder_logs_staff_type_week_idx');
        });
    }

    public function down(): void
    {
        Schema::table('email_reminder_logs', function (Blueprint $table) {
            $table->dropIndex('email_reminder_logs_staff_type_week_idx');
            $table->dropColumn('week_start_date');
        });
    }
};
