<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Regional Director',
            'Regional Program Lead',
            'Finance Officer',
            'Admin',
            'Technical Officer Digital System',
            'National Coordinator',
            'Senior Country Representative',
            'Program Officer',
            'Monitoring & Evaluation Officer',
            'Communications Officer',
            'Human Resources Officer',
            'IT Officer',
            'Logistics Officer',
            'Research Officer',
            'Training Coordinator',
            'Data Analyst',
            'Project Manager',
            'Field Officer',
            'Accountant',
            'Secretary',
            'Driver',
            'Security Officer',
            'Cleaner',
            'Intern',
            'Volunteer'
        ];

        foreach ($positions as $title) {
            Position::firstOrCreate(
                ['title' => $title],
                [
                    'title' => $title,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Default positions seeded successfully!');
    }
}
