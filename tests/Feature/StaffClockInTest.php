<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffClockInTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_clock_in(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);

        $staff = Staff::create([
            'staff_id' => 'RCC-600',
            'first_name' => 'Samuel',
            'last_name' => 'Test',
            'email' => 'samuel.test@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        $this->actingAs($staff, 'staff')
            ->postJson(route('staff.attendance.clock-in'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'staff_id' => $staff->id,
        ]);
    }

    public function test_attendance_page_includes_clock_in_script(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);

        $staff = Staff::create([
            'staff_id' => 'RCC-601',
            'first_name' => 'Page',
            'last_name' => 'Test',
            'email' => 'page.test@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        $this->actingAs($staff, 'staff')
            ->get(route('staff.attendance.index'))
            ->assertOk()
            ->assertSee('id="btn-clock-in"', false)
            ->assertSee('submitClock', false)
            ->assertDontSee('geolocation', false);
    }
}
