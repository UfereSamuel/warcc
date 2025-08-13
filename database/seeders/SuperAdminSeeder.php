<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@africacdc.org',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        // Create super admin staff record
        $superAdmin = Staff::create([
            'staff_id' => 'RCC-001',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@africacdc.org',
            'gender' => 'male',
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
        ]);
    }
}
