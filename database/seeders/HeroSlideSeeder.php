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
                'title' => 'Africa CDC Western RCC',
                'subtitle' => 'Strengthening Health Security',
                'description' => 'Leading regional collaboration in disease surveillance and health emergency preparedness across West Africa.',
                'image_path' => 'hero_default_1.jpg', // Placeholder - admin will upload real images
                'button_text' => 'Learn More',
                'button_link' => route('public.about'),
                'order_index' => 1,
                'status' => 'active',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Building Resilient Health Systems',
                'subtitle' => 'Capacity Building Excellence',
                'description' => 'Empowering health professionals and institutions across 15 West African countries through innovative training programs.',
                'image_path' => 'hero_default_2.jpg', // Placeholder
                'button_text' => 'Contact Us',
                'button_link' => route('public.contact'),
                'order_index' => 2,
                'status' => 'active',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Regional Health Collaboration',
                'subtitle' => 'United for Better Health',
                'description' => 'Fostering partnerships and coordination among West African nations for improved health outcomes and emergency response.',
                'image_path' => 'hero_default_3.jpg', // Placeholder
                'button_text' => 'Our Mission',
                'button_link' => route('public.about'),
                'order_index' => 3,
                'status' => 'active',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }

        $this->command->info('Hero slides seeded successfully!');
        $this->command->warn('Note: Placeholder images are used. Admin should upload real images through the admin panel.');
    }
}
