<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_admin_dashboard_uses_operational_position_activity_language(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Position Activity Overview')
            ->assertDontSee('Position Performance Overview')
            ->assertDontSee('Performance Score');
    }
}
