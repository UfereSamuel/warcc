<?php

namespace Database\Seeders;

use App\Models\ActivityCalendar;
use App\Models\ActivityReport;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleActivityReportSeeder extends Seeder
{
    /**
     * Seed sample activity reports for local testing (AI summarize/merge, admin review).
     */
    public function run(): void
    {
        if (ActivityReport::exists()) {
            $this->command->info('Activity reports already exist. Skipping SampleActivityReportSeeder.');

            return;
        }

        $john = Staff::where('staff_id', 'RCC-002')->first();
        $jane = Staff::where('staff_id', 'RCC-003')->first();
        $reviewer = Staff::where('staff_id', 'RCC-004')->first()
            ?? Staff::where('is_admin', true)->first();

        if (!$john || !$jane) {
            $this->command->warn('Run TestStaffSeeder first (RCC-002, RCC-003 required).');

            return;
        }

        $training = ActivityCalendar::where('title', 'like', '%Training%')->first();
        $research = ActivityCalendar::where('title', 'like', '%Research%')->first();
        $conference = ActivityCalendar::where('title', 'like', '%Conference%')->first();

        $reports = [
            [
                'staff_id' => $john->id,
                'activity_calendar_id' => $training?->id,
                'title' => 'Public Health Training Workshop — Field Report',
                'report_date' => Carbon::now()->subDays(3),
                'summary' => 'Participated in a three-day public health training workshop covering emergency response, disease surveillance, and cross-border coordination protocols for West African member states.',
                'outcomes' => 'Completed all modules with practical simulations. Updated the regional SOP checklist based on workshop guidance. Established contact with three surveillance focal points from neighboring countries.',
                'challenges' => 'Limited time for hands-on lab sessions. Some materials were only available in French, requiring translation support for Anglophone participants.',
                'recommendations' => 'Schedule follow-up refresher sessions quarterly. Provide bilingual training packs for future workshops.',
                'status' => 'submitted',
                'submitted_at' => Carbon::now()->subDays(2),
            ],
            [
                'staff_id' => $jane->id,
                'activity_calendar_id' => $research?->id,
                'title' => 'Disease Surveillance Research — Mid-Activity Update',
                'report_date' => Carbon::now()->subDays(4),
                'summary' => 'Continued data collection for the ongoing disease surveillance research project. Reviewed laboratory samples and validated reporting templates with partner institutions.',
                'outcomes' => 'Collected 48 validated surveillance records. Harmonized reporting forms with two partner labs. Drafted interim analysis for internal review.',
                'challenges' => 'Delayed sample transport from two remote sites due to logistics constraints.',
                'recommendations' => 'Engage a dedicated courier contract for remote sites. Extend data collection window by two weeks if delays persist.',
                'status' => 'submitted',
                'submitted_at' => Carbon::now()->subDays(1),
            ],
            [
                'staff_id' => $john->id,
                'activity_calendar_id' => $conference?->id,
                'title' => 'RCC Annual Conference — Participation Summary',
                'report_date' => Carbon::now()->subDays(10),
                'summary' => 'Represented WARCC at the regional annual conference, presenting updates on coordinating centre activities and participating in breakout sessions on outbreak preparedness.',
                'outcomes' => 'Delivered a 15-minute plenary update. Signed a memorandum of understanding for data sharing with two RCCs. Collected best-practice notes from five sessions.',
                'challenges' => 'Overlapping sessions limited attendance at some priority tracks.',
                'recommendations' => 'Share conference proceedings internally and schedule a staff brown-bag session within 30 days.',
                'status' => 'submitted',
                'submitted_at' => Carbon::now()->subHours(6),
            ],
            [
                'staff_id' => $jane->id,
                'activity_calendar_id' => null,
                'title' => 'Community Outreach — Malaria Awareness Campaign',
                'report_date' => Carbon::now()->subDays(14),
                'summary' => 'Led a standalone community outreach activity in two local districts to raise awareness on malaria prevention and testing services.',
                'outcomes' => 'Reached approximately 320 community members. Distributed 200 informational leaflets. Referral linkage established with nearest testing centres.',
                'challenges' => 'Rain on the second day reduced turnout at the outdoor venue.',
                'recommendations' => 'Reserve indoor backup venues for future outreach during rainy season.',
                'status' => 'reviewed',
                'submitted_at' => Carbon::now()->subDays(12),
                'admin_notes' => 'Strong community engagement. Consider scaling to a third district next quarter.',
                'reviewed_by' => $reviewer?->id,
                'reviewed_at' => Carbon::now()->subDays(11),
            ],
            [
                'staff_id' => $john->id,
                'activity_calendar_id' => null,
                'title' => 'Weekly Coordination Call — Draft Notes',
                'report_date' => Carbon::now(),
                'summary' => 'Draft notes from this week\'s internal coordination call. Not yet finalized for submission.',
                'outcomes' => null,
                'challenges' => null,
                'recommendations' => null,
                'status' => 'draft',
                'submitted_at' => null,
            ],
        ];

        foreach ($reports as $report) {
            ActivityReport::create($report);
            $this->command->info("Created activity report: {$report['title']} ({$report['status']})");
        }

        $this->command->info('Sample activity reports created successfully!');
    }
}
