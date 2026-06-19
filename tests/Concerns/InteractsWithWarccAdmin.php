<?php

namespace Tests\Concerns;

use App\Models\Staff;
use Database\Seeders\LeaveTypeSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SuperAdminSeeder;

trait InteractsWithWarccAdmin
{
    protected function seedWarccAdmin(): Staff
    {
        $this->seed([
            LeaveTypeSeeder::class,
            SuperAdminSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return Staff::where('staff_id', 'RCC-001')->firstOrFail();
    }

    protected function actingAsSuperAdmin(): static
    {
        $admin = $this->seedWarccAdmin();
        $this->actingAs($admin, 'staff');

        return $this;
    }
}
