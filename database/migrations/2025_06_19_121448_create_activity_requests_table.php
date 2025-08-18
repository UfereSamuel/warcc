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
        Schema::create('activity_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->enum('type', ['meeting', 'training', 'event', 'holiday', 'deadline'])->default('event');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('justification')->nullable(); // Why this activity is needed
            $table->integer('expected_participants')->nullable();
            $table->decimal('estimated_budget', 10, 2)->nullable();
            $table->text('admin_notes')->nullable(); // Admin feedback/notes
            $table->text('rejection_reason')->nullable(); // Reason for rejection
            $table->foreignId('requested_by')->constrained('staff')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_activity_id')->nullable()->constrained('activity_calendars')->onDelete('set null'); // Links to created activity if approved
            $table->timestamps();

            // Indexes for better performance
            $table->index(['status', 'requested_by']);
            $table->index(['reviewed_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_requests');
    }
};
