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
        if (Schema::hasColumn('system_settings', 'complaints_system_enabled')) {
            return;
        }

        Schema::table('system_settings', function (Blueprint $table) {
            $table->boolean('complaints_system_enabled')->default(true)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('complaints_system_enabled');
        });
    }
};
