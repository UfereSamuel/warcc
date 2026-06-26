<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\StaffStatusAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class StaffStatusAnalyticsTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_staff_status_counts_use_submitted_weekly_trackers(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $onMission = $this->makeStaff('RCC-801', 'Mission', 'Staff', $position->id);
        $onLeave = $this->makeStaff('RCC-802', 'Leave', 'Staff', $position->id);
        $atOffice = $this->makeStaff('RCC-803', 'Office', 'Staff', $position->id);
        $this->makeStaff('RCC-804', 'Pending', 'Staff', $position->id);

        $this->makeSubmittedTracker($onMission, $weekStart, 'on_mission');
        $this->makeSubmittedTracker($onLeave, $weekStart, 'on_leave');
        $this->makeSubmittedTracker($atOffice, $weekStart, 'at_duty_station');

        $counts = app(StaffStatusAnalyticsService::class)->getCurrentWeekTrackerStatusCounts($weekStart);

        $this->assertSame(1, $counts['on_mission']);
        $this->assertSame(1, $counts['on_leave']);
        $this->assertSame(1, $counts['at_duty_station']);
        $this->assertSame(1, $counts['not_submitted']);
    }

    public function test_admin_dashboard_shows_tracker_based_staff_roster(): void
    {
        $admin = $this->seedWarccAdmin();
        $weekStart = now()->startOfWeek();

        $this->makeSubmittedTracker($admin, $weekStart, 'at_duty_station');

        $this->actingAs($admin, 'staff')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Staff Status — Decision View', false)
            ->assertSee('From submitted weekly trackers', false)
            ->assertSee('At Duty Station', false)
            ->assertSee($admin->full_name, false);
    }

    private function makeStaff(string $staffId, string $first, string $last, int $positionId): Staff
    {
        return Staff::create([
            'staff_id' => $staffId,
            'first_name' => $first,
            'last_name' => $last,
            'email' => strtolower($staffId).'@africacdc.org',
            'gender' => 'male',
            'position_id' => $positionId,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);
    }

    private function makeSubmittedTracker(Staff $staff, $weekStart, string $status): WeeklyTracker
    {
        return WeeklyTracker::create([
            'staff_id' => $staff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => $status,
            'submission_status' => 'submitted',
            'submitted_at' => now(),
            'mission_title' => $status === 'on_mission' ? 'Test mission' : null,
            'mission_type' => $status === 'on_mission' ? 'regional' : null,
            'mission_start_date' => $status === 'on_mission' ? $weekStart->toDateString() : null,
            'mission_end_date' => $status === 'on_mission' ? $weekStart->copy()->addDay()->toDateString() : null,
            'mission_purpose' => $status === 'on_mission' ? 'Purpose' : null,
            'mission_documents' => $status === 'on_mission' ? [['original_name' => 'a.pdf', 'path' => 'x']] : null,
        ]);
    }
}
