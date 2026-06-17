<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Regular annual vacation leave',
                'max_days' => 28,
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Medical or health-related leave',
                'max_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for new mothers',
                'max_days' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for new fathers',
                'max_days' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Compassionate Leave',
                'description' => 'Leave for family emergencies or bereavement',
                'max_days' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Leave',
                'description' => 'Urgent personal matters',
                'max_days' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Educational or training purposes',
                'max_days' => null,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
