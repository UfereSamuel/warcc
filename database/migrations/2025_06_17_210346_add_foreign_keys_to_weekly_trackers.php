<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('weekly_trackers', function (Blueprint $table) {
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('set null');
            $table->foreign('leave_request_id')->references('id')->on('leave_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_trackers', function (Blueprint $table) {
            $table->dropForeign(['mission_id']);
            $table->dropForeign(['leave_request_id']);
        });
    }
};
