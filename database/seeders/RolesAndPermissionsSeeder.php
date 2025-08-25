<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Staff;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache to avoid issues
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Staff Management
            'manage_staff',
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'promote_staff',
            'demote_staff',
            
            // System Management
            'manage_system',
            'view_system_logs',
            'manage_settings',
            
            // Content Management
            'manage_content',
            'manage_hero_slides',
            'manage_public_events',
            'manage_activity_calendar',
            
            // Reports & Analytics
            'view_reports',
            'export_reports',
            'view_analytics',
            
            // Attendance Management
            'manage_attendance',
            'view_attendance',
            'export_attendance',
            
            // Activity & Mission Management
            'manage_activities',
            'approve_missions',
            'approve_leaves',
            'manage_leave_types',
            
            // Weekly Trackers
            'manage_weekly_trackers',
            'review_weekly_trackers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'staff'
            ]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'staff'
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'Administrator',
            'guard_name' => 'staff'
        ]);

        $staffRole = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => 'staff'
        ]);

        // Assign all permissions to Super Admin
        $superAdminRole->givePermissionTo(Permission::where('guard_name', 'staff')->get());

        // Assign limited permissions to Administrator
        $adminRole->givePermissionTo([
            'view_staff',
            'create_staff',
            'edit_staff',
            'promote_staff', // Can promote staff to admin but not to super admin
            'manage_content',
            'manage_hero_slides',
            'manage_public_events',
            'manage_activity_calendar',
            'view_reports',
            'export_reports',
            'manage_attendance',
            'view_attendance',
            'export_attendance',
            'manage_activities',
            'approve_missions',
            'approve_leaves',
            'manage_weekly_trackers',
            'review_weekly_trackers',
        ]);

        // Basic permissions for Staff
        $staffRole->givePermissionTo([
            'view_staff', // Can view their own profile
        ]);

        // Assign roles to existing staff
        $this->assignRolesToExistingStaff($superAdminRole, $adminRole, $staffRole);

        $this->command->info('Roles and permissions created successfully!');
    }

    private function assignRolesToExistingStaff($superAdminRole, $adminRole, $staffRole)
    {
        // Assign Super Admin role to admin@africacdc.org
        $superAdmin = Staff::where('email', 'admin@africacdc.org')->first();
        if ($superAdmin) {
            $superAdmin->assignRole($superAdminRole);
            $this->command->info("Assigned Super Admin role to: {$superAdmin->email}");
        }

        // Assign Admin role to other admin staff
        $otherAdmins = Staff::where('is_admin', true)
                          ->where('email', '!=', 'admin@africacdc.org')
                          ->get();
        
        foreach ($otherAdmins as $admin) {
            $admin->assignRole($adminRole);
            $this->command->info("Assigned Administrator role to: {$admin->email}");
        }

        // Assign Staff role to non-admin staff
        $regularStaff = Staff::where('is_admin', false)->get();
        foreach ($regularStaff as $staff) {
            $staff->assignRole($staffRole);
            $this->command->info("Assigned Staff role to: {$staff->email}");
        }
    }
}
