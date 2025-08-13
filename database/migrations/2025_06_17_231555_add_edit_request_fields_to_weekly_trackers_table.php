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
            $table->enum('edit_request_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('submitted_at');
            $table->timestamp('edit_requested_at')->nullable()->after('edit_request_status');
            $table->timestamp('edit_approved_at')->nullable()->after('edit_requested_at');
            $table->unsignedBigInteger('edit_approved_by')->nullable()->after('edit_approved_at');
            $table->text('edit_rejection_reason')->nullable()->after('edit_approved_by');

            $table->foreign('edit_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_trackers', function (Blueprint $table) {
            $table->dropForeign(['edit_approved_by']);
            $table->dropColumn([
                'edit_request_status',
                'edit_requested_at',
                'edit_approved_at',
                'edit_approved_by',
                'edit_rejection_reason'
            ]);
        });
    }
};
