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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('official_name');
            $table->string('flag_code', 2); // ISO 3166-1 alpha-2 code
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default AU Western Africa countries (15 member states)
        $defaultCountries = [
            ['name' => 'Benin', 'official_name' => 'The Republic of Benin', 'flag_code' => 'bj', 'sort_order' => 1],
            ['name' => 'Burkina Faso', 'official_name' => 'Burkina Faso', 'flag_code' => 'bf', 'sort_order' => 2],
            ['name' => 'Cabo Verde', 'official_name' => 'The Republic of Cabo Verde', 'flag_code' => 'cv', 'sort_order' => 3],
            ['name' => 'Côte d\'Ivoire', 'official_name' => 'The Republic of Côte d\'Ivoire', 'flag_code' => 'ci', 'sort_order' => 4],
            ['name' => 'Gambia', 'official_name' => 'The Republic of The Gambia', 'flag_code' => 'gm', 'sort_order' => 5],
            ['name' => 'Ghana', 'official_name' => 'The Republic of Ghana', 'flag_code' => 'gh', 'sort_order' => 6],
            ['name' => 'Guinea', 'official_name' => 'The Republic of Guinea', 'flag_code' => 'gn', 'sort_order' => 7],
            ['name' => 'Guinea-Bissau', 'official_name' => 'The Republic of Guinea-Bissau', 'flag_code' => 'gw', 'sort_order' => 8],
            ['name' => 'Liberia', 'official_name' => 'The Republic of Liberia', 'flag_code' => 'lr', 'sort_order' => 9],
            ['name' => 'Mali', 'official_name' => 'The Republic of Mali', 'flag_code' => 'ml', 'sort_order' => 10],
            ['name' => 'Niger', 'official_name' => 'The Republic of Niger', 'flag_code' => 'ne', 'sort_order' => 11],
            ['name' => 'Nigeria', 'official_name' => 'The Federal Republic of Nigeria', 'flag_code' => 'ng', 'sort_order' => 12],
            ['name' => 'Senegal', 'official_name' => 'The Republic of Senegal', 'flag_code' => 'sn', 'sort_order' => 13],
            ['name' => 'Sierra Leone', 'official_name' => 'The Republic of Sierra Leone', 'flag_code' => 'sl', 'sort_order' => 14],
            ['name' => 'Togo', 'official_name' => 'The Togolese Republic', 'flag_code' => 'tg', 'sort_order' => 15],
        ];

        foreach ($defaultCountries as $country) {
            DB::table('countries')->insert([
                'name' => $country['name'],
                'official_name' => $country['official_name'],
                'flag_code' => $country['flag_code'],
                'is_active' => true,
                'sort_order' => $country['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
