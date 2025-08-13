<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityCalendar;
use App\Models\Staff;
use Carbon\Carbon;

class SampleCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user
        $admin = Staff::where('is_admin', true)->first();

        if (!$admin) {
            $this->command->info('No admin user found. Creating sample activities with null creator.');
        }

        $activities = [
            [
                'title' => 'Weekly Team Meeting',
                'description' => 'Regular weekly team sync to discuss ongoing projects, updates, and upcoming deadlines.',
                'start_date' => Carbon::now()->addDays(2),
                'end_date' => Carbon::now()->addDays(2),
                'location' => 'Conference Room A',
                'type' => 'meeting',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Public Health Training Workshop',
                'description' => 'Comprehensive training on latest public health protocols, emergency response procedures, and disease surveillance methods.',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(9),
                'location' => 'Training Center, Building B',
                'type' => 'training',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'RCC Annual Conference 2025',
                'description' => 'Annual conference bringing together regional collaborating centers to share research findings and best practices.',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(16),
                'location' => 'Accra International Conference Centre',
                'type' => 'event',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Independence Day Holiday',
                'description' => 'National holiday - All offices closed. No work activities scheduled.',
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(21),
                'location' => null,
                'type' => 'holiday',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Quarterly Report Deadline',
                'description' => 'Final submission deadline for Q2 quarterly performance reports. All departments must submit by end of day.',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(30),
                'location' => null,
                'type' => 'deadline',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Ongoing Research Project - Disease Surveillance',
                'description' => 'Multi-week research project focused on improving disease surveillance systems across West Africa.',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(10),
                'location' => 'Research Laboratory, Building C',
                'type' => 'event',
                'status' => 'ongoing',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Monthly Safety Review Meeting',
                'description' => 'Monthly review of safety protocols and incident reports.',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(5),
                'location' => 'Main Board Room',
                'type' => 'meeting',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Staff Performance Review Period',
                'description' => 'Annual staff performance review period. All managers to complete reviews.',
                'start_date' => Carbon::now()->addDays(45),
                'end_date' => Carbon::now()->addDays(60),
                'location' => 'Various Offices',
                'type' => 'event',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Equipment Maintenance Deadline',
                'description' => 'Mandatory equipment maintenance must be completed by this date.',
                'start_date' => Carbon::now()->addDays(35),
                'end_date' => Carbon::now()->addDays(35),
                'location' => 'Equipment Room',
                'type' => 'deadline',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Leadership Training Program',
                'description' => 'Advanced leadership training for department heads and team leaders.',
                'start_date' => Carbon::now()->addDays(25),
                'end_date' => Carbon::now()->addDays(27),
                'location' => 'Executive Training Center',
                'type' => 'training',
                'status' => 'not_yet_started',
                'created_by' => $admin?->id,
            ]
        ];

        foreach ($activities as $activity) {
            ActivityCalendar::create($activity);
            $this->command->info("Created activity: {$activity['title']}");
        }

        $this->command->info('Sample calendar activities created successfully!');
    }
}
