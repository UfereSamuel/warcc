<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\MissionComplianceService;
use App\Services\StaffStatusAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class StaffRosterWidgetTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_roster_entries_include_position_and_today_attendance(): void
    {
        $position = Position::create(['title' => 'Epidemiologist', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $staff = Staff::create([
            'staff_id' => 'RCC-920',
            'first_name' => 'Roster',
            'last_name' => 'Member',
            'email' => 'roster.member@africacdc.org',
            'gender' => 'female',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $staff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        Attendance::create([
            'staff_id' => $staff->id,
            'date' => today()->toDateString(),
            'clock_in_time' => now(),
        ]);

        $roster = app(StaffStatusAnalyticsService::class)->getStaffRosterWidgetData($weekStart);
        $entry = $roster['groups']['at_duty_station']->first();

        $this->assertSame('Epidemiologist', $entry['position_title']);
        $this->assertTrue($entry['clocked_in_today']);
    }

    public function test_on_mission_roster_entry_includes_mission_report_status(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $staff = Staff::create([
            'staff_id' => 'RCC-921',
            'first_name' => 'Mission',
            'last_name' => 'Traveler',
            'email' => 'mission.traveler@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $staff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'on_mission',
            'mission_title' => 'Field assessment',
            'mission_type' => 'regional',
            'mission_start_date' => $weekStart->toDateString(),
            'mission_end_date' => $weekStart->copy()->addDay()->toDateString(),
            'mission_purpose' => 'Assess readiness.',
            'mission_documents' => [['original_name' => 'a.pdf', 'path' => 'x', 'size' => 100]],
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $entry = app(StaffStatusAnalyticsService::class)
            ->getStaffRosterWidgetData($weekStart)['groups']['on_mission']
            ->first();

        $this->assertSame('Field assessment', $entry['mission_title']);
        $this->assertSame(
            MissionComplianceService::STATUS_MISSING,
            $entry['mission_report_status']
        );
    }

    public function test_admin_staff_roster_page_loads_with_filters(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();

        WeeklyTracker::create([
            'staff_id' => $admin->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.staff-roster.index'))
            ->assertOk()
            ->assertSee('Staff Roster', false)
            ->assertSee($admin->full_name, false)
            ->assertSee('At Duty Station', false);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.staff-roster.index', [
                'week' => $weekStart->toDateString(),
                'status' => 'on_mission',
            ]))
            ->assertOk()
            ->assertSee('No staff match the current filters.', false);
    }

    public function test_dashboard_shows_staff_status_decision_panel(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();

        WeeklyTracker::create([
            'staff_id' => $admin->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Staff Status — Decision View', false)
            ->assertSee('Full roster', false)
            ->assertSee($admin->staff_id, false);
    }

    public function test_dashboard_week_picker_shows_previous_week_data(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $previousWeek = now()->startOfWeek()->subWeek();

        $staff = Staff::create([
            'staff_id' => 'RCC-922',
            'first_name' => 'Past',
            'last_name' => 'Week',
            'email' => 'past.week@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $staff->id,
            'week_start_date' => $previousWeek->toDateString(),
            'week_end_date' => $previousWeek->copy()->endOfWeek()->toDateString(),
            'status' => 'on_mission',
            'mission_title' => 'Last week mission',
            'mission_type' => 'regional',
            'mission_start_date' => $previousWeek->toDateString(),
            'mission_end_date' => $previousWeek->copy()->addDay()->toDateString(),
            'mission_purpose' => 'Prior week work.',
            'mission_documents' => [['original_name' => 'a.pdf', 'path' => 'x', 'size' => 100]],
            'submission_status' => 'submitted',
            'submitted_at' => $previousWeek->copy()->addDays(2),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.dashboard', ['week' => $previousWeek->toDateString()]))
            ->assertOk()
            ->assertSee('Last week mission', false)
            ->assertSee('Past Week', false);
    }

    public function test_dashboard_status_query_opens_drilldown_panel(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();

        WeeklyTracker::create([
            'staff_id' => $admin->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff')
            ->get(route('admin.dashboard', [
                'week' => $weekStart->toDateString(),
                'status' => 'at_duty_station',
            ]))
            ->assertOk()
            ->assertSee('drilldown-at_duty_station', false)
            ->assertSee('showStaffStatusDrilldown', false)
            ->assertSee($admin->full_name, false);
    }
}
