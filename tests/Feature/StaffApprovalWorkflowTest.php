<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Staff;
use App\Models\Position;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

class StaffApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_new_microsoft_sso_registration_sets_status_to_pending()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('ms-123456');
        $abstractUser->shouldReceive('getEmail')->andReturn('new.staff@africacdc.org');
        $abstractUser->shouldReceive('getName')->andReturn('John Doe');

        $abstractUser->id = 'ms-123456';
        $abstractUser->email = 'new.staff@africacdc.org';
        $abstractUser->name = 'John Doe';

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('microsoft')->andReturn($provider);

        $response = $this->get(route('auth.microsoft.callback'));

        $staff = Staff::where('email', 'new.staff@africacdc.org')->first();

        $this->assertNotNull($staff);
        $this->assertEquals('pending', $staff->status);
        $this->assertTrue($staff->isPending());
        $response->assertRedirect(route('staff.pending-approval'));
    }

    public function test_pending_staff_is_redirected_to_pending_approval_page()
    {
        $position = Position::create(['title' => 'Epidemiologist', 'status' => 'active']);
        $staff = Staff::create([
            'staff_id' => 'RCC-999',
            'first_name' => 'Pending',
            'last_name' => 'User',
            'email' => 'pending.user@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'pending',
            'hire_date' => now()->toDateString(),
            'is_admin' => false,
        ]);

        $response = $this->actingAs($staff, 'staff')
            ->get(route('staff.dashboard'));

        $response->assertRedirect(route('staff.pending-approval'));
    }

    public function test_pending_approval_page_renders_for_pending_staff()
    {
        $position = Position::create(['title' => 'Epidemiologist', 'status' => 'active']);
        $staff = Staff::create([
            'staff_id' => 'RCC-998',
            'first_name' => 'Pending',
            'last_name' => 'Test',
            'email' => 'pending.test@africacdc.org',
            'gender' => 'female',
            'position_id' => $position->id,
            'status' => 'pending',
            'hire_date' => now()->toDateString(),
            'is_admin' => false,
        ]);

        $response = $this->actingAs($staff, 'staff')
            ->get(route('staff.pending-approval'));

        $response->assertStatus(200);
        $response->assertSee('Account Pending Verification');
        $response->assertSee('pending.test@africacdc.org');
    }

    public function test_admin_can_see_pending_staff_and_approve_account()
    {
        $position = Position::create(['title' => 'System Administrator', 'status' => 'active']);
        $admin = Staff::create([
            'staff_id' => 'RCC-001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'hire_date' => now()->toDateString(),
            'is_admin' => true,
        ]);
        $admin->assignRole('Super Admin');

        $pendingStaff = Staff::create([
            'staff_id' => 'RCC-888',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@africacdc.org',
            'gender' => 'female',
            'position_id' => $position->id,
            'status' => 'pending',
            'hire_date' => now()->toDateString(),
            'is_admin' => false,
        ]);

        // Admin index filter for pending
        $indexResponse = $this->actingAs($admin, 'staff')
            ->get(route('admin.staff.index', ['status' => 'pending']));

        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('jane.smith@africacdc.org');

        // Admin approves staff
        $approveResponse = $this->actingAs($admin, 'staff')
            ->post(route('admin.staff.approve', $pendingStaff));

        $approveResponse->assertRedirect();
        $this->assertEquals('active', $pendingStaff->fresh()->status);
        $this->assertTrue($pendingStaff->fresh()->isActive());
    }

    public function test_admin_can_reject_pending_staff()
    {
        $position = Position::create(['title' => 'System Administrator', 'status' => 'active']);
        $admin = Staff::create([
            'staff_id' => 'RCC-001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'hire_date' => now()->toDateString(),
            'is_admin' => true,
        ]);
        $admin->assignRole('Super Admin');

        $pendingStaff = Staff::create([
            'staff_id' => 'RCC-777',
            'first_name' => 'Rejected',
            'last_name' => 'User',
            'email' => 'rejected.user@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'pending',
            'hire_date' => now()->toDateString(),
            'is_admin' => false,
        ]);

        $rejectResponse = $this->actingAs($admin, 'staff')
            ->post(route('admin.staff.reject', $pendingStaff));

        $rejectResponse->assertRedirect();
        $this->assertEquals('inactive', $pendingStaff->fresh()->status);
    }
}
