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
            // Mission fields
            $table->string('mission_title')->nullable()->after('remarks');
            $table->enum('mission_type', ['regional', 'continental', 'incountry'])->nullable()->after('mission_title');
            $table->date('mission_start_date')->nullable()->after('mission_type');
            $table->date('mission_end_date')->nullable()->after('mission_start_date');
            $table->text('mission_purpose')->nullable()->after('mission_end_date');
            $table->json('mission_documents')->nullable()->after('mission_purpose');

            // Leave fields
            $table->unsignedBigInteger('leave_type_id')->nullable()->after('mission_documents');
            $table->date('leave_start_date')->nullable()->after('leave_type_id');
            $table->date('leave_end_date')->nullable()->after('leave_start_date');
            $table->json('leave_approval_document')->nullable()->after('leave_end_date');

            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_trackers', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropColumn([
                'mission_title',
                'mission_type',
                'mission_start_date',
                'mission_end_date',
                'mission_purpose',
                'mission_documents',
                'leave_type_id',
                'leave_start_date',
                'leave_end_date',
                'leave_approval_document'
            ]);
        });
    }
};
