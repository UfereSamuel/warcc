<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class AdminReportsAccessTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_reports(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('auth.login'));
    }

    public function test_super_admin_can_load_reports_dashboard_view(): void
    {
        $this->actingAsSuperAdmin();

        $view = app(ReportsController::class)->index(Request::create('/admin/reports', 'GET'));

        $this->assertSame('admin.reports.index', $view->name());
        $this->assertArrayHasKey('totalStaff', $view->getData());

        $this->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Reports & Analytics');
    }

    public function test_super_admin_can_export_overview_pdf(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.reports.export-pdf', ['type' => 'overview']))
            ->assertOk();
    }
}
