<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\StaffRosterExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class ExportPhase2Test extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_staff_roster_page_shows_unified_export_panel(): void
    {
        $admin = $this->seedWarccAdmin();

        $this->actingAs($admin, 'staff')
            ->get(route('admin.staff-roster.index'))
            ->assertOk()
            ->assertSee('Export roster CSV', false)
            ->assertSee('Export summary CSV', false)
            ->assertSee('Weekly status summary', false);
    }

    public function test_staff_roster_detail_export_honors_filters(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $missionStaff = $this->makeStaff('RCC-801', 'Mission', 'Staff', $position->id);
        $officeStaff = $this->makeStaff('RCC-802', 'Office', 'Staff', $position->id);

        $this->makeTracker($missionStaff, $weekStart, 'on_mission');
        $this->makeTracker($officeStaff, $weekStart, 'at_duty_station');

        $response = $this->actingAs($admin, 'staff')
            ->get(route('admin.export.staff-roster', [
                'week' => $weekStart->toDateString(),
                'status' => 'on_mission',
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('RCC-801', $content);
        $this->assertStringContainsString('On Mission', $content);
        $this->assertStringNotContainsString('RCC-802', $content);
    }

    public function test_staff_roster_summary_export_lists_counts_by_week(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $currentWeek = now()->startOfWeek();
        $previousWeek = $currentWeek->copy()->subWeek();

        $currentStaff = $this->makeStaff('RCC-803', 'Current', 'Week', $position->id);
        $previousStaff = $this->makeStaff('RCC-804', 'Previous', 'Week', $position->id);

        $this->makeTracker($currentStaff, $currentWeek, 'on_mission');
        $this->makeTracker($previousStaff, $previousWeek, 'at_duty_station');

        $response = $this->actingAs($admin, 'staff')
            ->get(route('admin.export.staff-roster-summary', [
                'week' => $currentWeek->toDateString(),
                'weeks' => 2,
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('Week Start', $content);
        $this->assertStringContainsString('At Duty Station', $content);
        $this->assertStringContainsString('On Mission', $content);
        $this->assertStringContainsString('Not Submitted', $content);
        $this->assertStringContainsString($previousWeek->toDateString(), $content);
        $this->assertStringContainsString($currentWeek->toDateString(), $content);
        $this->assertStringContainsString(
            'staff_status_summary_',
            strtolower($response->headers->get('content-disposition'))
        );
    }

    public function test_weekly_summary_service_returns_oldest_week_first(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $currentWeek = now()->startOfWeek();
        $previousWeek = $currentWeek->copy()->subWeek();

        $this->makeTracker(
            $this->makeStaff('RCC-805', 'One', 'Staff', $position->id),
            $previousWeek,
            'on_leave'
        );
        $this->makeTracker(
            $this->makeStaff('RCC-806', 'Two', 'Staff', $position->id),
            $currentWeek,
            'on_mission'
        );

        $summaries = app(StaffRosterExportService::class)
            ->getWeeklyStatusSummaries($currentWeek, 2);

        $this->assertCount(2, $summaries);
        $this->assertSame($previousWeek->toDateString(), $summaries->first()['week_start']);
        $this->assertSame($currentWeek->toDateString(), $summaries->last()['week_start']);
        $this->assertSame(1, $summaries->first()['on_leave']);
        $this->assertSame(1, $summaries->last()['on_mission']);
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

    private function makeTracker(Staff $staff, $weekStart, string $status): WeeklyTracker
    {
        return WeeklyTracker::create([
            'staff_id' => $staff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => $status,
            'submission_status' => 'submitted',
            'submitted_at' => now(),
            'mission_title' => $status === 'on_mission' ? 'Summary test mission' : null,
            'mission_type' => $status === 'on_mission' ? 'regional' : null,
            'mission_start_date' => $status === 'on_mission' ? $weekStart->toDateString() : null,
            'mission_end_date' => $status === 'on_mission' ? $weekStart->copy()->addDay()->toDateString() : null,
            'mission_purpose' => $status === 'on_mission' ? 'Testing summary export.' : null,
            'mission_documents' => $status === 'on_mission' ? [['original_name' => 'a.pdf', 'path' => 'x', 'size' => 100]] : null,
        ]);
    }
}
