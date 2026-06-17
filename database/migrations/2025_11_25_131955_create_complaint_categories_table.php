<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaint_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default categories
        $defaultCategories = [
            ['name' => 'Harassment', 'slug' => 'harassment', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Workplace Safety', 'slug' => 'workplace_safety', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Discrimination', 'slug' => 'discrimination', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Misconduct', 'slug' => 'misconduct', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Facility Issues', 'slug' => 'facility_issues', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Management Issues', 'slug' => 'management_issues', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Other', 'slug' => 'other', 'is_active' => true, 'sort_order' => 7],
        ];

        foreach ($defaultCategories as $category) {
            DB::table('complaint_categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'is_active' => $category['is_active'],
                'sort_order' => $category['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update complaints table to change category from enum to string
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('category', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_categories');
    }
};
