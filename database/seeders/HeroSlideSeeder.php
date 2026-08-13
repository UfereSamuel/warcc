<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeroSlide;
use App\Models\Staff;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super admin staff member
        $admin = Staff::where('is_admin', true)->first();

        if (!$admin) {
            $this->command->error('No admin staff found. Please run StaffSeeder first.');
            return;
        }

        $slides = [
            [
                'title' => 'Western RCC',
                'subtitle' => null,
                'description' => null,
                'image_path' => 'placeholder_hero_1.png',
                'button_text' => null,
                'button_link' => null,
                'order_index' => 1,
                'status' => 'active',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Health security across West Africa',
                'subtitle' => null,
                'description' => null,
                'image_path' => 'placeholder_hero_2.png',
                'button_text' => null,
                'button_link' => null,
                'order_index' => 2,
                'status' => 'active',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Advancing the AHSS Agenda',
                'subtitle' => null,
                'description' => null,
                'image_path' => 'placeholder_hero_3.png',
                'button_text' => null,
                'button_link' => null,
                'order_index' => 3,
                'status' => 'active',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::updateOrCreate(
                ['order_index' => $slide['order_index']],
                $slide
            );
        }

        $this->command->info('Hero slides seeded successfully!');
        $this->command->warn('Placeholder hero images are committed under public/images/hero-slides. Replace via Admin → Content → Hero Slides.');
    }
}
