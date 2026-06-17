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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique();
            $table->string('category');
            $table->text('description');
            $table->text('suggested_solution')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->string('complainant_name')->nullable();
            $table->string('complainant_email')->nullable();
            $table->string('complainant_phone')->nullable();
            $table->string('evidence_path')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
