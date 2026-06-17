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
        Schema::create('public_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('summary')->nullable(); // Short summary for listings
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('venue_address')->nullable(); // Full address for maps
            $table->enum('category', ['conference', 'workshop', 'training', 'seminar', 'meeting', 'announcement', 'celebration'])->default('announcement');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('featured_image')->nullable(); // Event banner/poster
            $table->string('registration_link')->nullable(); // External registration URL
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_registrations')->default(0);
            $table->boolean('is_featured')->default(false); // Highlight on homepage
            $table->boolean('registration_required')->default(false);
            $table->date('registration_deadline')->nullable();
            $table->decimal('fee', 8, 2)->nullable(); // Event fee if applicable
            $table->json('tags')->nullable(); // Event tags for filtering
            $table->text('additional_info')->nullable(); // Extra details
            $table->foreignId('created_by')->constrained('staff')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['status', 'is_featured']);
            $table->index(['start_date', 'status']);
            $table->index(['category', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_events');
    }
};
