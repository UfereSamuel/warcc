<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Position;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class ExportPhase1Test extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_daily_attendance_export_uses_selected_date_only(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $todayStaff = $this->makeStaff('RCC-701', 'Today', 'Only', $position->id);
        $yesterdayStaff = $this->makeStaff('RCC-702', 'Yesterday', 'Only', $position->id);

        Attendance::create([
            'staff_id' => $todayStaff->id,
            'date' => $today,
            'clock_in_time' => now(),
            'status' => 'present',
        ]);

        Attendance::create([
            'staff_id' => $yesterdayStaff->id,
            'date' => $yesterday,
            'clock_in_time' => now()->subDay(),
            'status' => 'present',
        ]);

        $response = $this->actingAs($admin, 'staff')
            ->get(route('admin.export.attendance', [
                'date' => $today,
                'format' => 'csv',
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('RCC-701', $content);
        $this->assertStringContainsString("attendance_report_{$today}_to_{$today}.csv", strtolower($response->headers->get('content-disposition')));
        $this->assertStringNotContainsString('RCC-702', $content);
    }

    public function test_attendance_export_honors_position_filter(): void
    {
        $admin = $this->seedWarccAdmin();
        $officer = Position::create(['title' => 'Officer', 'is_active' => true]);
        $analyst = Position::create(['title' => 'Analyst', 'is_active' => true]);
        $date = now()->toDateString();

        $officerStaff = $this->makeStaff('RCC-703', 'Officer', 'Staff', $officer->id);
        $analystStaff = $this->makeStaff('RCC-704', 'Analyst', 'Staff', $analyst->id);

        foreach ([$officerStaff, $analystStaff] as $staff) {
            Attendance::create([
                'staff_id' => $staff->id,
                'date' => $date,
                'clock_in_time' => now(),
                'status' => 'present',
            ]);
        }

        $response = $this->actingAs($admin, 'staff')
            ->get(route('admin.export.attendance', [
                'date' => $date,
                'position_id' => $officer->id,
                'format' => 'csv',
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('RCC-703', $content);
        $this->assertStringNotContainsString('RCC-704', $content);
    }

    public function test_weekly_tracker_export_honors_weekly_status_filter(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $missionStaff = $this->makeStaff('RCC-705', 'Mission', 'Staff', $position->id);
        $officeStaff = $this->makeStaff('RCC-706', 'Office', 'Staff', $position->id);

        $this->makeTracker($missionStaff, $weekStart, 'on_mission');
        $this->makeTracker($officeStaff, $weekStart, 'at_duty_station');

        $response = $this->actingAs($admin, 'staff')
            ->get(route('admin.export.weekly-trackers', [
                'start_date' => $weekStart->toDateString(),
                'end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
                'status' => 'on_mission',
                'format' => 'csv',
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('RCC-705', $content);
        $this->assertStringNotContainsString('RCC-706', $content);
    }

    public function test_weekly_tracker_export_honors_submission_status_filter(): void
    {
        $admin = $this->seedWarccAdmin();
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $submittedStaff = $this->makeStaff('RCC-707', 'Submitted', 'Staff', $position->id);
        $draftStaff = $this->makeStaff('RCC-708', 'Draft', 'Staff', $position->id);

        WeeklyTracker::create([
            'staff_id' => $submittedStaff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $draftStaff->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'draft',
        ]);

        $response = $this->actingAs($admin, 'staff')
            ->get(route('admin.export.weekly-trackers', [
                'start_date' => $weekStart->toDateString(),
                'end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
                'status' => 'submitted',
                'format' => 'csv',
            ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('RCC-707', $content);
        $this->assertStringNotContainsString('RCC-708', $content);
    }

    public function test_reports_attendance_page_includes_csv_export_and_presets(): void
    {
        $admin = $this->seedWarccAdmin();

        $this->actingAs($admin, 'staff')
            ->get(route('admin.reports.attendance'))
            ->assertOk()
            ->assertSee('Export CSV', false)
            ->assertSee('This week', false)
            ->assertSee('This month', false)
            ->assertSee('Last month', false);
    }

    public function test_reports_weekly_trackers_page_includes_csv_export_and_presets(): void
    {
        $admin = $this->seedWarccAdmin();

        $this->actingAs($admin, 'staff')
            ->get(route('admin.reports.weekly-trackers'))
            ->assertOk()
            ->assertSee('Export CSV', false)
            ->assertSee('This week', false)
            ->assertSee('Last month', false);
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
            'mission_title' => $status === 'on_mission' ? 'Export test mission' : null,
            'mission_type' => $status === 'on_mission' ? 'regional' : null,
            'mission_start_date' => $status === 'on_mission' ? $weekStart->toDateString() : null,
            'mission_end_date' => $status === 'on_mission' ? $weekStart->copy()->addDay()->toDateString() : null,
            'mission_purpose' => $status === 'on_mission' ? 'Testing export filters.' : null,
            'mission_documents' => $status === 'on_mission' ? [['original_name' => 'a.pdf', 'path' => 'x', 'size' => 100]] : null,
        ]);
    }
}
