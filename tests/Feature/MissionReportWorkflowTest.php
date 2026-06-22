<?php

namespace Tests\Feature;

use App\Models\ActivityReport;
use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\ActivityWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unreported_mission_trackers_appear_on_create_report_page(): void
    {
        $staff = $this->makeStaff();
        $tracker = $this->makeSubmittedMissionTracker($staff);

        $this->actingAs($staff, 'staff')
            ->get(route('staff.activity-reports.create'))
            ->assertOk()
            ->assertSee('weekly_tracker_id', false)
            ->assertSee($tracker->mission_title, false);
    }

    public function test_staff_can_create_report_linked_to_weekly_tracker_mission(): void
    {
        $staff = $this->makeStaff();
        $tracker = $this->makeSubmittedMissionTracker($staff);

        $this->actingAs($staff, 'staff')
            ->post(route('staff.activity-reports.store'), [
                'weekly_tracker_id' => $tracker->id,
                'title' => $tracker->mission_title,
                'report_date' => $tracker->mission_end_date->toDateString(),
                'summary' => 'Mission completed successfully.',
                'action' => 'submit',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activity_reports', [
            'staff_id' => $staff->id,
            'weekly_tracker_id' => $tracker->id,
            'status' => 'submitted',
        ]);
    }

    public function test_reported_mission_is_removed_from_selectable_list(): void
    {
        $staff = $this->makeStaff();
        $tracker = $this->makeSubmittedMissionTracker($staff);

        ActivityReport::create([
            'staff_id' => $staff->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Done.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $workflow = app(ActivityWorkflowService::class);

        $this->assertTrue($workflow->getSelectableMissionTrackersForReport($staff)->isEmpty());
    }

    public function test_weekly_tracker_page_shows_submit_mission_report_action(): void
    {
        $staff = $this->makeStaff();
        $this->makeSubmittedMissionTracker($staff);

        $this->actingAs($staff, 'staff')
            ->get(route('staff.tracker.index'))
            ->assertOk()
            ->assertSee('Submit Mission Report', false);
    }

    private function makeStaff(): Staff
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);

        return Staff::create([
            'staff_id' => 'RCC-700',
            'first_name' => 'Mission',
            'last_name' => 'Reporter',
            'email' => 'mission.reporter@africacdc.org',
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
            'mission_title' => 'Regional coordination mission',
            'mission_type' => 'regional',
            'mission_start_date' => $weekStart->toDateString(),
            'mission_end_date' => $weekStart->copy()->addDays(2)->toDateString(),
            'mission_purpose' => 'Support member states on outbreak response.',
            'mission_documents' => [['original_name' => 'brief.pdf', 'path' => 'weekly-tracker/mission-documents/brief.pdf']],
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }
}
