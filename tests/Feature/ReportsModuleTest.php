<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class ReportsModuleTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_staff_performance_report_route_is_not_registered(): void
    {
        $this->actingAsSuperAdmin()
            ->get('/admin/reports/staff-performance')
            ->assertNotFound();
    }

    public function test_reports_index_does_not_reference_staff_appraisal(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertDontSee('Staff Performance')
            ->assertDontSee('performance scoring', false)
            ->assertDontSee('Performance Report')
            ->assertSee('Weekly Tracker Analysis')
            ->assertSee('Attendance Analytics');
    }

    public function test_super_admin_can_load_weekly_trackers_report(): void
    {
        $this->actingAsSuperAdmin();

        $view = app(ReportsController::class)->weeklyTrackers(Request::create('/admin/reports/weekly-trackers', 'GET'));

        $this->assertSame('admin.reports.weekly-trackers', $view->name());
        $this->assertArrayHasKey('trackers', $view->getData());

        $this->get(route('admin.reports.weekly-trackers'))
            ->assertOk()
            ->assertSee('Weekly Tracker Submissions');
    }

    public function test_super_admin_can_load_attendance_report(): void
    {
        $this->actingAsSuperAdmin();

        $view = app(ReportsController::class)->attendance(Request::create('/admin/reports/attendance', 'GET'));

        $this->assertSame('admin.reports.attendance', $view->name());
        $this->assertArrayHasKey('staffRankings', $view->getData());

        $this->get(route('admin.reports.attendance'))
            ->assertOk()
            ->assertSee('Staff Attendance Summary')
            ->assertDontSee('Top 10 Attendance Leaders');
    }

    public function test_super_admin_can_export_operational_pdf_reports(): void
    {
        $this->actingAsSuperAdmin();

        foreach (['overview', 'weekly-trackers', 'attendance'] as $type) {
            $this->get(route('admin.reports.export-pdf', ['type' => $type]))
                ->assertOk();
        }
    }

    public function test_legacy_staff_performance_pdf_type_falls_back_to_overview(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.reports.export-pdf', ['type' => 'staff-performance']))
            ->assertOk();
    }
}
