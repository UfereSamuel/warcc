<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityRequest;
use App\Models\Staff;
use Carbon\Carbon;

class ActivityRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some staff members (non-admin)
        $staffMembers = Staff::where('is_admin', false)->take(3)->get();

        if ($staffMembers->isEmpty()) {
            $this->command->info('No non-admin staff found. Creating requests with first available staff.');
            $staffMembers = Staff::take(3)->get();
        }

        $requests = [
            [
                'title' => 'Team Building Workshop',
                'description' => 'A comprehensive team building workshop to improve collaboration and communication among staff members. This will include outdoor activities, group exercises, and professional facilitation.',
                'start_date' => Carbon::now()->addDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'location' => 'Conference Room A',
                'type' => 'training',
                'status' => 'pending',
                'justification' => 'Our team has been working remotely for extended periods, and this workshop will help rebuild team cohesion and improve workplace relationships. It aligns with our organizational goal of maintaining a positive work environment.',
                'expected_participants' => 25,
                'estimated_budget' => 3500.00,
                'requested_by' => $staffMembers->first()?->id,
            ],
            [
                'title' => 'Monthly Department Review Meeting',
                'description' => 'Monthly review meeting to discuss departmental performance, upcoming projects, and resource allocation.',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(7),
                'location' => 'Main Board Room',
                'type' => 'meeting',
                'status' => 'pending',
                'justification' => 'Regular departmental meetings are essential for maintaining clear communication, tracking progress on key initiatives, and ensuring all team members are aligned with organizational objectives.',
                'expected_participants' => 12,
                'estimated_budget' => 150.00,
                'requested_by' => $staffMembers->skip(1)->first()?->id,
            ],
            [
                'title' => 'RCC Research Symposium',
                'description' => 'Annual research symposium showcasing ongoing research projects and findings from regional collaborating centers across West Africa.',
                'start_date' => Carbon::now()->addDays(45),
                'end_date' => Carbon::now()->addDays(47),
                'location' => 'University of Ghana Conference Center',
                'type' => 'event',
                'status' => 'pending',
                'justification' => 'This symposium is crucial for knowledge sharing, networking with other RCCs, and presenting our research contributions to the broader scientific community. It directly supports our mandate for research collaboration.',
                'expected_participants' => 150,
                'estimated_budget' => 15000.00,
                'requested_by' => $staffMembers->last()?->id,
            ],
            [
                'title' => 'WHO Guidelines Training Session',
                'description' => 'Training session on the latest WHO guidelines for disease surveillance and reporting protocols.',
                'start_date' => Carbon::now()->addDays(20),
                'end_date' => Carbon::now()->addDays(22),
                'location' => 'Training Center B',
                'type' => 'training',
                'status' => 'approved',
                'justification' => 'Keeping our staff updated with the latest WHO guidelines is mandatory for maintaining our accreditation and ensuring compliance with international standards.',
                'expected_participants' => 30,
                'estimated_budget' => 2800.00,
                'requested_by' => $staffMembers->first()?->id,
                'reviewed_by' => Staff::where('is_admin', true)->first()?->id,
                'reviewed_at' => Carbon::now()->subDays(2),
                'admin_notes' => 'Approved for immediate implementation. Essential for regulatory compliance.',
            ],
            [
                'title' => 'Annual Budget Planning Retreat',
                'description' => 'Strategic planning retreat for developing next year\'s budget and setting organizational priorities.',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(32),
                'location' => 'Mountain View Resort',
                'type' => 'meeting',
                'status' => 'rejected',
                'justification' => 'Annual budget planning requires focused attention away from daily distractions. A retreat setting would provide the necessary environment for strategic thinking.',
                'expected_participants' => 15,
                'estimated_budget' => 8500.00,
                'requested_by' => $staffMembers->skip(1)->first()?->id,
                'reviewed_by' => Staff::where('is_admin', true)->first()?->id,
                'reviewed_at' => Carbon::now()->subDays(1),
                'rejection_reason' => 'Budget for off-site venues exceeds current allocation. Please revise with on-site venue option.',
                'admin_notes' => 'The concept is good but the venue cost is too high. Consider using our conference facilities instead.',
            ],
        ];

        foreach ($requests as $request) {
            ActivityRequest::create($request);
            $this->command->info("Created activity request: {$request['title']}");
        }

        $this->command->info('Sample activity requests created successfully!');
    }
}
