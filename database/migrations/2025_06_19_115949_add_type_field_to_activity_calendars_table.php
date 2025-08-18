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
        Schema::table('activity_calendars', function (Blueprint $table) {
            $table->enum('type', ['meeting', 'training', 'event', 'holiday', 'deadline'])->default('event')->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_calendars', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
