<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('activity_calendar_id')->nullable()->constrained('activity_calendars')->nullOnDelete();
            $table->string('reminder_type', 64);
            $table->string('recipient_email');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['staff_id', 'reminder_type', 'sent_at']);
            $table->index(['activity_calendar_id', 'reminder_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_reminder_logs');
    }
};
