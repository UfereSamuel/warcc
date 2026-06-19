<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevLoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dev_routes_are_hidden_when_disabled(): void
    {
        config(['warcc.dev_login.enabled' => false]);

        $this->get('/test-accounts')->assertNotFound();
        $this->get('/test-login/RCC-002')->assertNotFound();
    }

    public function test_dev_login_works_when_explicitly_enabled(): void
    {
        config(['warcc.dev_login.enabled' => true]);

        $position = Position::firstOrCreate(
            ['title' => 'Test Program Officer'],
            ['is_active' => true]
        );
        Staff::create([
            'staff_id' => 'RCC-002',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'gender' => 'male',
            'phone' => '+234-800-000-0001',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'annual_leave_balance' => 21,
            'hire_date' => now()->subYear(),
        ]);

        $this->get('/test-login/RCC-002')
            ->assertRedirect(route('staff.dashboard'));
    }
}
