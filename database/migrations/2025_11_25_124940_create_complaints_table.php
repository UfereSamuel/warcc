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
        if (Schema::hasTable('complaints')) {
            return;
        }

        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique();
            $table->enum('category', [
                'harassment',
                'workplace_safety',
                'discrimination',
                'misconduct',
                'facility_issues',
                'management_issues',
                'other'
            ]);
            $table->text('description');
            $table->text('suggested_solution')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->string('complainant_name')->nullable();
            $table->string('complainant_email')->nullable();
            $table->string('complainant_phone')->nullable();
            $table->string('evidence_path')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->text('admin_notes')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->timestamps();
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
