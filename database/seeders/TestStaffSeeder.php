<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class TestStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test staff member (John Doe)
        $testStaff = Staff::create([
            'staff_id' => 'RCC-002',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@africacdc.org',
            'gender' => 'male',
            'phone' => '+233 24 123 4567',
            'position' => 'Public Health Officer',
            'department' => 'Disease Surveillance',
            'annual_leave_balance' => 21,
            'status' => 'active',
            'is_admin' => false,
            'microsoft_id' => 'test-john-doe-123',
            'hire_date' => now()->subMonths(6),
        ]);

        // Create another test staff member (Jane Smith)
        $testStaff2 = Staff::create([
            'staff_id' => 'RCC-003',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@africacdc.org',
            'gender' => 'female',
            'phone' => '+233 24 987 6543',
            'position' => 'Epidemiologist',
            'department' => 'Capacity Building',
            'annual_leave_balance' => 21,
            'status' => 'active',
            'is_admin' => false,
            'microsoft_id' => 'test-jane-smith-456',
            'hire_date' => now()->subMonths(8),
        ]);

        // Create a test admin staff member (Sarah Johnson)
        $testAdmin = Staff::create([
            'staff_id' => 'RCC-004',
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@africacdc.org',
            'gender' => 'female',
            'phone' => '+233 24 555 0123',
            'position' => 'Regional Coordinator',
            'department' => 'Administration',
            'annual_leave_balance' => 25,
            'status' => 'active',
            'is_admin' => true,
            'microsoft_id' => 'test-sarah-johnson-789',
            'hire_date' => now()->subYears(2),
        ]);

        // Create some sample attendance records for testing
        $this->createSampleAttendanceRecords($testStaff);
        $this->createSampleAttendanceRecords($testStaff2);

        $this->command->info('Test staff members created successfully:');
        $this->command->info('1. John Doe (john.doe@africacdc.org) - Staff ID: RCC-002 [STAFF]');
        $this->command->info('2. Jane Smith (jane.smith@africacdc.org) - Staff ID: RCC-003 [STAFF]');
        $this->command->info('3. Sarah Johnson (sarah.johnson@africacdc.org) - Staff ID: RCC-004 [ADMIN]');
        $this->command->info('');
        $this->command->info('Note: These accounts use Microsoft SSO. For testing purposes,');
        $this->command->info('you can use the existing admin account: admin@africacdc.org / admin123');
    }

    private function createSampleAttendanceRecords(Staff $staff)
    {
        // Create some sample attendance records for the past week
        for ($i = 1; $i <= 7; $i++) {
            $date = now()->subDays($i);

            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            // Random attendance times
            $clockInTime = $date->setTime(8, rand(0, 30), rand(0, 59));
            $clockOutTime = $date->setTime(17, rand(0, 30), rand(0, 59));
            $totalHours = $clockOutTime->diffInHours($clockInTime) + ($clockOutTime->diffInMinutes($clockInTime) % 60) / 60;

            \App\Models\Attendance::create([
                'staff_id' => $staff->id,
                'date' => $date->toDateString(),
                'clock_in_time' => $clockInTime->format('H:i:s'),
                'clock_out_time' => $clockOutTime->format('H:i:s'),
                'clock_in_latitude' => 5.6037 + (rand(-100, 100) / 10000), // Around Accra
                'clock_in_longitude' => -0.1870 + (rand(-100, 100) / 10000),
                'clock_out_latitude' => 5.6037 + (rand(-100, 100) / 10000),
                'clock_out_longitude' => -0.1870 + (rand(-100, 100) / 10000),
                'clock_in_address' => 'Africa CDC Office, Accra, Ghana',
                'clock_out_address' => 'Africa CDC Office, Accra, Ghana',
                'total_hours' => round($totalHours, 2),
                'status' => 'present',
            ]);
        }
    }
}
