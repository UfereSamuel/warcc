<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PublicEvent;
use App\Models\Staff;
use Carbon\Carbon;

class PublicEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin staff member to assign as creator
        $adminStaff = Staff::where('is_admin', true)->first();

        if (!$adminStaff) {
            $this->command->info('No admin staff found. Creating events with first available staff.');
            $adminStaff = Staff::first();
        }

        if (!$adminStaff) {
            $this->command->error('No staff members found. Please run staff seeders first.');
            return;
        }

        $events = [
            [
                'title' => 'West Africa Health Symposium 2024',
                'summary' => 'Annual symposium bringing together health professionals from across West Africa to discuss emerging health challenges and innovative solutions.',
                'description' => 'Join us for the premier health symposium in West Africa, featuring keynote speakers from WHO, CDC, and leading research institutions. This three-day event will cover topics including infectious disease surveillance, health system strengthening, and community health innovations. Participants will engage in workshops, panel discussions, and networking sessions designed to foster collaboration across the region.',
                'start_date' => Carbon::now()->addDays(45),
                'end_date' => Carbon::now()->addDays(47),
                'start_time' => '08:00',
                'end_time' => '17:00',
                'location' => 'Accra International Conference Centre',
                'venue_address' => 'Liberation Road, Ridge, Accra, Ghana',
                'category' => 'conference',
                'status' => 'published',
                'is_featured' => true,
                'registration_required' => true,
                'registration_deadline' => Carbon::now()->addDays(30),
                'max_participants' => 500,
                'current_registrations' => 127,
                'fee' => 250.00,
                'contact_email' => 'symposium@africacdc.org',
                'contact_phone' => '+233 30 123 4567',
                'registration_link' => 'https://events.africacdc.org/west-africa-symposium-2024',
                'tags' => ['health', 'symposium', 'west africa', 'cdc', 'who', 'research'],
                'additional_info' => 'Accommodation arrangements available. CEU credits provided for healthcare professionals. Simultaneous translation in English and French available.',
                'created_by' => $adminStaff->id,
                'published_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Digital Health Innovation Workshop',
                'summary' => 'Hands-on workshop exploring cutting-edge digital health technologies and their applications in African healthcare systems.',
                'description' => 'This intensive two-day workshop will provide participants with practical experience in digital health solutions including telemedicine platforms, health information systems, and mobile health applications. Designed for healthcare administrators, IT professionals, and health policy makers.',
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(22),
                'start_time' => '09:00',
                'end_time' => '16:30',
                'location' => 'University of Ghana School of Public Health',
                'venue_address' => 'Legon, Accra, Ghana',
                'category' => 'workshop',
                'status' => 'published',
                'is_featured' => false,
                'registration_required' => true,
                'registration_deadline' => Carbon::now()->addDays(14),
                'max_participants' => 50,
                'current_registrations' => 23,
                'fee' => 75.00,
                'contact_email' => 'digitalhealth@africacdc.org',
                'contact_phone' => '+233 30 123 4568',
                'registration_link' => 'https://events.africacdc.org/digital-health-workshop',
                'tags' => ['digital health', 'technology', 'innovation', 'telemedicine'],
                'additional_info' => 'Laptops will be provided. Basic programming knowledge helpful but not required.',
                'created_by' => $adminStaff->id,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Epidemiology Training Course',
                'summary' => 'Comprehensive 5-day training course on field epidemiology and outbreak investigation methods.',
                'description' => 'Intensive training program designed for public health professionals, covering outbreak investigation, data analysis, surveillance systems, and epidemiological study design. Participants will engage in case studies, practical exercises, and simulation scenarios based on real-world outbreak investigations.',
                'start_date' => Carbon::now()->addDays(35),
                'end_date' => Carbon::now()->addDays(39),
                'start_time' => '08:30',
                'end_time' => '17:00',
                'location' => 'Africa CDC Training Center',
                'venue_address' => 'Airport Residential Area, Accra, Ghana',
                'category' => 'training',
                'status' => 'published',
                'is_featured' => true,
                'registration_required' => true,
                'registration_deadline' => Carbon::now()->addDays(21),
                'max_participants' => 30,
                'current_registrations' => 18,
                'fee' => 350.00,
                'contact_email' => 'training@africacdc.org',
                'contact_phone' => '+233 30 123 4569',
                'registration_link' => 'https://training.africacdc.org/epidemiology-course',
                'tags' => ['epidemiology', 'training', 'outbreak investigation', 'surveillance'],
                'additional_info' => 'Course materials included. Certificate of completion provided. Prior experience in public health recommended.',
                'created_by' => $adminStaff->id,
                'published_at' => Carbon::now()->subDays(15),
            ],
            [
                'title' => 'Africa CDC Monthly Webinar Series',
                'summary' => 'Monthly webinar featuring updates on continental health security initiatives and emerging disease threats.',
                'description' => 'Join our monthly webinar series where leading experts discuss current health security challenges across Africa, recent outbreak responses, and new initiatives from Africa CDC. This month focuses on antimicrobial resistance surveillance networks.',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(10),
                'start_time' => '14:00',
                'end_time' => '15:30',
                'location' => 'Virtual Event',
                'venue_address' => 'Online via Zoom',
                'category' => 'seminar',
                'status' => 'published',
                'is_featured' => false,
                'registration_required' => true,
                'registration_deadline' => Carbon::now()->addDays(8),
                'max_participants' => null, // Unlimited for virtual event
                'current_registrations' => 234,
                'fee' => null, // Free event
                'contact_email' => 'webinars@africacdc.org',
                'contact_phone' => '+233 30 123 4570',
                'registration_link' => 'https://zoom.us/webinar/register/africa-cdc-monthly',
                'tags' => ['webinar', 'antimicrobial resistance', 'surveillance', 'virtual'],
                'additional_info' => 'Webinar will be recorded. Q&A session included. CEU credits available.',
                'created_by' => $adminStaff->id,
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'World Health Day Celebration',
                'summary' => 'Community celebration of World Health Day with health screenings, educational exhibits, and cultural performances.',
                'description' => 'Join us for a day-long celebration of World Health Day featuring free health screenings, educational booths from local health organizations, children\'s activities, cultural performances, and presentations on this year\'s health theme. Open to the public and families.',
                'start_date' => Carbon::parse('2024-04-07'),
                'end_date' => Carbon::parse('2024-04-07'),
                'start_time' => '09:00',
                'end_time' => '16:00',
                'location' => 'Independence Square',
                'venue_address' => 'Independence Square, Accra, Ghana',
                'category' => 'celebration',
                'status' => 'published',
                'is_featured' => true,
                'registration_required' => false,
                'registration_deadline' => null,
                'max_participants' => null,
                'current_registrations' => 0,
                'fee' => null, // Free event
                'contact_email' => 'events@africacdc.org',
                'contact_phone' => '+233 30 123 4571',
                'registration_link' => null,
                'tags' => ['world health day', 'community', 'celebration', 'health screening'],
                'additional_info' => 'No registration required. Food and refreshments available for purchase. Parking available nearby.',
                'created_by' => $adminStaff->id,
                'published_at' => Carbon::now()->subDays(30),
            ],
            [
                'title' => 'Grant Application Deadline: Health Research Funding 2024',
                'summary' => 'Final deadline for submitting applications for Africa CDC health research grants supporting innovative research projects.',
                'description' => 'This is the final deadline for submitting applications for our annual health research grants. We are particularly interested in research proposals focusing on infectious disease surveillance, health system strengthening, and community health innovations. Grants range from $50,000 to $200,000 over 2-3 years.',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(30),
                'start_time' => '23:59',
                'end_time' => '23:59',
                'location' => 'Online Submission',
                'venue_address' => 'Submit via grants.africacdc.org',
                'category' => 'announcement',
                'status' => 'published',
                'is_featured' => false,
                'registration_required' => false,
                'registration_deadline' => null,
                'max_participants' => null,
                'current_registrations' => 0,
                'fee' => null,
                'contact_email' => 'grants@africacdc.org',
                'contact_phone' => '+233 30 123 4572',
                'registration_link' => 'https://grants.africacdc.org/health-research-2024',
                'tags' => ['grants', 'research funding', 'deadline', 'health research'],
                'additional_info' => 'Applications must be submitted online. Technical support available Monday-Friday 9AM-5PM.',
                'created_by' => $adminStaff->id,
                'published_at' => Carbon::now()->subDays(60),
            ],
            [
                'title' => 'Regional Laboratory Network Meeting',
                'summary' => 'Quarterly meeting of West African laboratory directors to discuss quality assurance and network coordination.',
                'description' => 'This quarterly meeting brings together laboratory directors and senior technical staff from across West Africa to discuss laboratory quality assurance programs, proficiency testing results, equipment maintenance, and coordination of reference laboratory services. The meeting includes technical presentations and working group sessions.',
                'start_date' => Carbon::now()->addDays(60),
                'end_date' => Carbon::now()->addDays(62),
                'start_time' => '08:00',
                'end_time' => '17:00',
                'location' => 'Coconut Grove Hotel',
                'venue_address' => 'Elmina Street, East Legon, Accra, Ghana',
                'category' => 'meeting',
                'status' => 'draft',
                'is_featured' => false,
                'registration_required' => true,
                'registration_deadline' => Carbon::now()->addDays(45),
                'max_participants' => 75,
                'current_registrations' => 0,
                'fee' => 150.00,
                'contact_email' => 'laboratory@africacdc.org',
                'contact_phone' => '+233 30 123 4573',
                'registration_link' => null, // Will be added when published
                'tags' => ['laboratory', 'quality assurance', 'west africa', 'network meeting'],
                'additional_info' => 'Invitation-only event for laboratory network members. Travel support available for selected participants.',
                'created_by' => $adminStaff->id,
                'published_at' => null, // Draft status
            ],
        ];

        foreach ($events as $eventData) {
            PublicEvent::create($eventData);
            $this->command->info("Created public event: {$eventData['title']}");
        }

        $this->command->info('Sample public events created successfully!');
    }
}
