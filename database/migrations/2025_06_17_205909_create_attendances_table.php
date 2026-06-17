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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in_time')->nullable();
            $table->time('clock_out_time')->nullable();
            $table->decimal('clock_in_latitude', 10, 8)->nullable();
            $table->decimal('clock_in_longitude', 11, 8)->nullable();
            $table->decimal('clock_out_latitude', 10, 8)->nullable();
            $table->decimal('clock_out_longitude', 11, 8)->nullable();
            $table->string('clock_in_address')->nullable();
            $table->string('clock_out_address')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'partial', 'late'])->default('absent');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
