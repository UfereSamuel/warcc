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
        Schema::create('weekly_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('week_start_date');
            $table->date('week_end_date');
            $table->enum('status', ['duty_station', 'mission', 'leave']);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('mission_id')->nullable();
            $table->unsignedBigInteger('leave_request_id')->nullable();
            $table->enum('submission_status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'week_start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_trackers');
    }
};
