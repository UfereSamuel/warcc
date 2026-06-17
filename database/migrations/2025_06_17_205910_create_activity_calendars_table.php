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
        Schema::create('activity_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location');
            $table->enum('status', ['done', 'ongoing', 'not_yet_started'])->default('not_yet_started');
            $table->foreignId('created_by')->constrained('staff')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_calendars');
    }
};
