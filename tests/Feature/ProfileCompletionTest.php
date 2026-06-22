<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_completion_succeeds_without_phone(): void
    {
        $position = Position::create(['title' => 'Public Health Officer', 'is_active' => true]);

        $staff = Staff::create([
            'staff_id' => 'RCC-500',
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'new.user@africacdc.org',
            'microsoft_id' => 'ms-new-user',
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        $this->actingAs($staff, 'staff')
            ->post(route('staff.profile.complete.post'), [
                'position_id' => $position->id,
                'gender' => 'female',
            ])
            ->assertRedirect(route('staff.dashboard'));

        $staff->refresh();

        $this->assertFalse($staff->needsProfileCompletion());
        $this->assertSame($position->id, $staff->position_id);
        $this->assertNull($staff->phone);
    }

    public function test_staff_can_update_profile_via_put(): void
    {
        $position = Position::create(['title' => 'Epidemiologist', 'is_active' => true]);

        $staff = Staff::create([
            'staff_id' => 'RCC-501',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@africacdc.org',
            'gender' => 'female',
            'phone' => '+233 20 000 0000',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        $this->actingAs($staff, 'staff')
            ->put(route('staff.profile.update'), [
                'first_name' => 'Janet',
                'last_name' => 'Doe',
                'phone' => '+233 24 111 2222',
            ])
            ->assertRedirect(route('staff.profile'));

        $staff->refresh();

        $this->assertSame('Janet', $staff->first_name);
        $this->assertSame('+233 24 111 2222', $staff->phone);
    }
}
