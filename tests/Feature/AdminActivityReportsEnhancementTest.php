<?php

namespace Tests\Feature;

use App\Models\ActivityReport;
use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\ActivityReportAiService;
use App\Services\ActivityReportAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class AdminActivityReportsEnhancementTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_report_type_helpers_identify_mission_reports(): void
    {
        $staff = $this->makeStaff();
        $tracker = $this->makeSubmittedMissionTracker($staff);

        $missionReport = ActivityReport::create([
            'staff_id' => $staff->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $standaloneReport = ActivityReport::create([
            'staff_id' => $staff->id,
            'title' => 'Office work report',
            'report_date' => now()->toDateString(),
            'summary' => 'Office summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->assertTrue($missionReport->isMissionReport());
        $this->assertSame('Mission Report', $missionReport->report_type_label);
        $this->assertFalse($standaloneReport->isMissionReport());
        $this->assertSame('Standalone', $standaloneReport->report_type_label);
    }

    public function test_analytics_service_tracks_mission_report_counts(): void
    {
        $staff = $this->makeStaff();
        $tracker = $this->makeSubmittedMissionTracker($staff);

        ActivityReport::create([
            'staff_id' => $staff->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        ActivityReport::create([
            'staff_id' => $staff->id,
            'title' => 'Office work report',
            'report_date' => now()->toDateString(),
            'summary' => 'Office summary.',
            'status' => 'reviewed',
            'submitted_at' => now(),
            'reviewed_at' => now(),
        ]);

        $stats = app(ActivityReportAnalyticsService::class)->getIndexStats();

        $this->assertSame(2, $stats['total']);
        $this->assertSame(1, $stats['mission']);
        $this->assertSame(1, $stats['mission_submitted']);
        $this->assertSame(1, $stats['standalone']);
    }

    public function test_admin_index_shows_mission_report_type_and_stats(): void
    {
        $admin = $this->seedWarccAdmin();
        $tracker = $this->makeSubmittedMissionTracker($admin);

        ActivityReport::create([
            'staff_id' => $admin->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.activity-reports.index'))
            ->assertOk()
            ->assertSee('Mission Reports', false)
            ->assertSee('Mission Pending', false)
            ->assertSee('Mission Report', false)
            ->assertSee($tracker->mission_title, false);
    }

    public function test_admin_can_search_reports_by_mission_title(): void
    {
        $admin = $this->seedWarccAdmin();
        $tracker = $this->makeSubmittedMissionTracker($admin);

        ActivityReport::create([
            'staff_id' => $admin->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        ActivityReport::create([
            'staff_id' => $admin->id,
            'title' => 'Unrelated office report',
            'report_date' => now()->toDateString(),
            'summary' => 'Office summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.activity-reports.index', ['search' => 'Compliance test mission']))
            ->assertOk()
            ->assertSee($tracker->mission_title, false)
            ->assertDontSee('Unrelated office report', false);
    }

    public function test_admin_show_displays_linked_mission_context(): void
    {
        $admin = $this->seedWarccAdmin();
        $tracker = $this->makeSubmittedMissionTracker($admin);

        $report = ActivityReport::create([
            'staff_id' => $admin->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.activity-reports.show', $report))
            ->assertOk()
            ->assertSee('Linked Mission', false)
            ->assertSee($tracker->mission_purpose, false)
            ->assertSee('Mission Report', false);
    }

    public function test_dashboard_shows_mission_report_pending_link(): void
    {
        $admin = $this->seedWarccAdmin();
        $tracker = $this->makeSubmittedMissionTracker($admin);

        ActivityReport::create([
            'staff_id' => $admin->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('mission report pending', false)
            ->assertSee($tracker->mission_title, false);
    }

    public function test_ai_format_report_includes_weekly_tracker_mission_context(): void
    {
        $staff = $this->makeStaff();
        $tracker = $this->makeSubmittedMissionTracker($staff);

        $report = ActivityReport::create([
            'staff_id' => $staff->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Mission summary.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $method = new ReflectionMethod(ActivityReportAiService::class, 'formatReport');
        $formatted = $method->invoke(app(ActivityReportAiService::class), $report->load('weeklyTracker', 'staff'));

        $this->assertStringContainsString('Weekly Tracker Mission: Compliance test mission', $formatted);
        $this->assertStringContainsString('Mission Purpose: Test compliance tracking.', $formatted);
        $this->assertStringContainsString('Report Type: Mission Report', $formatted);
    }

    private function makeStaff(): Staff
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);

        return Staff::create([
            'staff_id' => 'RCC-910',
            'first_name' => 'Report',
            'last_name' => 'Admin',
            'email' => 'report.admin@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);
    }

    private function makeSubmittedMissionTracker(Staff $staff): WeeklyTracker
    {
        $weekStart = now()->startOfWeek();

        return WeeklyTracker::create([
            'staff_id' => $staff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'on_mission',
            'mission_title' => 'Compliance test mission',
            'mission_type' => 'regional',
            'mission_start_date' => $weekStart->toDateString(),
            'mission_end_date' => $weekStart->copy()->addDays(2)->toDateString(),
            'mission_purpose' => 'Test compliance tracking.',
            'mission_documents' => [['original_name' => 'brief.pdf', 'path' => 'weekly-tracker/mission-documents/brief.pdf', 'size' => 1024]],
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }
}
