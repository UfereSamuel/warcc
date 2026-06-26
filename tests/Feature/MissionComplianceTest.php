<?php

namespace Tests\Feature;

use App\Models\ActivityReport;
use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\MissionComplianceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class MissionComplianceTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_mission_compliance_counts_missing_and_filed_reports(): void
    {
        $weekStart = now()->startOfWeek();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);

        $missingStaff = $this->makeStaff('RCC-901', $position->id);
        $submittedStaff = $this->makeStaff('RCC-902', $position->id);
        $reviewedStaff = $this->makeStaff('RCC-903', $position->id);

        $missingTracker = $this->makeSubmittedMissionTracker($missingStaff, $weekStart);
        $submittedTracker = $this->makeSubmittedMissionTracker($submittedStaff, $weekStart);
        $reviewedTracker = $this->makeSubmittedMissionTracker($reviewedStaff, $weekStart);

        ActivityReport::create([
            'staff_id' => $submittedStaff->id,
            'weekly_tracker_id' => $submittedTracker->id,
            'title' => $submittedTracker->mission_title,
            'report_date' => $submittedTracker->mission_end_date,
            'summary' => 'Submitted report.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        ActivityReport::create([
            'staff_id' => $reviewedStaff->id,
            'weekly_tracker_id' => $reviewedTracker->id,
            'title' => $reviewedTracker->mission_title,
            'report_date' => $reviewedTracker->mission_end_date,
            'summary' => 'Reviewed report.',
            'status' => 'reviewed',
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now(),
        ]);

        $compliance = app(MissionComplianceService::class)->getMissionComplianceForWeek($weekStart);

        $this->assertSame(3, $compliance['summary']['total']);
        $this->assertSame(1, $compliance['summary']['missing']);
        $this->assertSame(1, $compliance['summary']['submitted']);
        $this->assertSame(1, $compliance['summary']['reviewed']);
        $this->assertSame(
            MissionComplianceService::STATUS_MISSING,
            $compliance['items']->firstWhere('tracker.id', $missingTracker->id)['report_status']
        );
    }

    public function test_admin_dashboard_shows_mission_compliance_panel(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();
        $tracker = $this->makeSubmittedMissionTracker($admin, $weekStart);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Mission Report Compliance', false)
            ->assertSee('Report Missing', false)
            ->assertSee($tracker->mission_title, false);
    }

    public function test_weekly_trackers_index_shows_mission_report_column(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();
        $this->makeSubmittedMissionTracker($admin, $weekStart);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.weekly-trackers.index', ['week' => $weekStart->toDateString()]))
            ->assertOk()
            ->assertSee('Mission Report', false)
            ->assertSee('Mission Report Compliance', false);
    }

    public function test_weekly_tracker_show_displays_mission_report_status(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();
        $tracker = $this->makeSubmittedMissionTracker($admin, $weekStart);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.weekly-trackers.show', $tracker))
            ->assertOk()
            ->assertSee('Mission Activity Report', false)
            ->assertSee('Report Missing', false);
    }

    public function test_admin_activity_reports_can_filter_mission_reports(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();
        $tracker = $this->makeSubmittedMissionTracker($admin, $weekStart);

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
            'title' => 'Standalone office report',
            'report_date' => now()->toDateString(),
            'summary' => 'Office work.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.activity-reports.index', ['mission' => 'yes']))
            ->assertOk()
            ->assertSee($tracker->mission_title, false)
            ->assertDontSee('Standalone office report', false);
    }

    private function makeStaff(string $staffId, int $positionId): Staff
    {
        return Staff::create([
            'staff_id' => $staffId,
            'first_name' => 'Test',
            'last_name' => $staffId,
            'email' => strtolower($staffId).'@africacdc.org',
            'gender' => 'male',
            'position_id' => $positionId,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);
    }

    private function makeSubmittedMissionTracker(Staff $staff, $weekStart): WeeklyTracker
    {
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
