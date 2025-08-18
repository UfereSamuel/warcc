<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin details from environment or use defaults
        $adminName = env('INSTALLER_ADMIN_NAME', 'System Administrator');
        $adminEmail = env('INSTALLER_ADMIN_EMAIL', 'admin@africacdc.org');
        $adminPassword = env('INSTALLER_ADMIN_PASSWORD', 'admin123');

        // Extract first and last name
        $nameParts = explode(' ', $adminName, 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : 'Admin';

        // Create super admin user (if using standard authentication)
        $superAdminUser = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'email_verified_at' => now(),
                'password' => Hash::make($adminPassword),
            ]
        );

        // Create super admin staff record
        $superAdmin = Staff::firstOrCreate(
            ['email' => $adminEmail],
            [
                'staff_id' => 'WARCC-001',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $adminEmail,
                'gender' => 'other',
                'phone' => '+234-800-000-0000',
                'position' => 'System Administrator',
                'department' => 'IT Department',
                'microsoft_id' => null,
                'profile_picture' => null,
                'status' => 'active',
                'is_admin' => true,
                'annual_leave_balance' => 28,
                'hire_date' => now()->subYear(),
                'permissions' => json_encode([
                    'manage_staff' => true,
                    'manage_system' => true,
                    'view_reports' => true,
                    'manage_activities' => true,
                    'manage_leave_types' => true,
                ]),
                'last_login' => null,
            ]
        );

        $this->command->info('Custom admin account created successfully:');
        $this->command->info("Name: {$adminName}");
        $this->command->info("Email: {$adminEmail}");
        $this->command->info("Staff ID: WARCC-001");
        $this->command->info('Position: System Administrator');
    }
}
