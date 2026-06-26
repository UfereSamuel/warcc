<?php

namespace Tests\Feature;

use App\Models\ActivityReport;
use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class AdminAnalyticsIntegrationTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_all_admin_analytics_pages_render_successfully(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $missionStaff = Staff::create([
            'staff_id' => 'RCC-950',
            'first_name' => 'Mission',
            'last_name' => 'User',
            'email' => 'mission.user@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        $tracker = WeeklyTracker::create([
            'staff_id' => $missionStaff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'on_mission',
            'mission_title' => 'Integration test mission',
            'mission_type' => 'regional',
            'mission_start_date' => $weekStart->toDateString(),
            'mission_end_date' => $weekStart->copy()->addDays(2)->toDateString(),
            'mission_purpose' => 'End-to-end analytics test.',
            'mission_documents' => [['original_name' => 'brief.pdf', 'path' => 'x', 'size' => 512]],
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $admin->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $report = ActivityReport::create([
            'staff_id' => $missionStaff->id,
            'weekly_tracker_id' => $tracker->id,
            'title' => $tracker->mission_title,
            'report_date' => $tracker->mission_end_date,
            'summary' => 'Integration mission report.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'staff');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Staff Status — Decision View', false)
            ->assertSee('Mission Report Compliance', false)
            ->assertSee('Integration test mission', false);

        $this->get(route('admin.staff-roster.index', ['week' => $weekStart->toDateString()]))
            ->assertOk()
            ->assertSee('Mission User', false)
            ->assertSee('Report Submitted', false);

        $this->get(route('admin.staff-roster.index', [
            'week' => $weekStart->toDateString(),
            'status' => 'on_mission',
        ]))
            ->assertOk()
            ->assertSee('Mission User', false)
            ->assertDontSee($admin->full_name, false);

        $this->get(route('admin.weekly-trackers.index', ['week' => $weekStart->toDateString()]))
            ->assertOk()
            ->assertSee('Mission Report', false)
            ->assertSee('Mission Report Compliance', false);

        $this->get(route('admin.weekly-trackers.show', $tracker))
            ->assertOk()
            ->assertSee('Mission Activity Report', false)
            ->assertSee('Report Submitted', false);

        $this->get(route('admin.activity-reports.index', ['mission' => 'yes']))
            ->assertOk()
            ->assertSee('Mission Reports', false)
            ->assertSee('Integration test mission', false);

        $this->get(route('admin.activity-reports.show', $report))
            ->assertOk()
            ->assertSee('Linked Mission', false)
            ->assertSee('Integration test mission', false);
    }
}
