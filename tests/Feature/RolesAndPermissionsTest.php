<?php

namespace Tests\Feature;

use App\Models\Staff;
use Database\Seeders\TestStaffSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_super_admin_has_view_reports_permission(): void
    {
        $admin = $this->seedWarccAdmin();

        $this->assertTrue($admin->can('view_reports'));
        $this->assertTrue($admin->hasRole('Super Admin'));
    }

    public function test_regular_staff_cannot_access_admin_reports(): void
    {
        $this->seedWarccAdmin();
        $this->seed(TestStaffSeeder::class);

        $staff = Staff::where('staff_id', 'RCC-002')->firstOrFail();

        $this->assertFalse($staff->can('view_reports'));

        $this->actingAs($staff, 'staff')
            ->get(route('admin.reports.index'))
            ->assertRedirect(route('staff.dashboard'));
    }

    public function test_regular_staff_cannot_access_about_content_editor(): void
    {
        $this->seedWarccAdmin();
        $this->seed(TestStaffSeeder::class);

        $staff = Staff::where('staff_id', 'RCC-002')->firstOrFail();

        $this->actingAs($staff, 'staff')
            ->get(route('admin.content.about'))
            ->assertRedirect(route('staff.dashboard'));
    }
}
