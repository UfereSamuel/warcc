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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->morphs('documentable'); // For polymorphic relationships (missions, leave_requests, etc.)
            $table->enum('document_type', ['mission_memo', 'note_verbale', 'travel_approval', 'leave_approval', 'other']);
            $table->foreignId('uploaded_by')->constrained('staff')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
