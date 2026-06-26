<?php

namespace Tests\Feature;

use App\Models\EmailReminderLog;
use App\Models\Position;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use App\Services\ReminderSettingsService;
use App\Services\WeeklyTrackerReminderService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class WeeklyTrackerReminderTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_weekly_tracker_reminder_commands_are_scheduled(): void
    {
        $schedule = app(Schedule::class);
        $commands = collect($schedule->events())->pluck('command')->filter()->implode(' ');

        $this->assertStringContainsString('reminders:weekly-trackers --type=sunday', $commands);
        $this->assertStringContainsString('reminders:weekly-trackers --type=daily', $commands);
    }

    public function test_staff_without_submitted_tracker_are_identified(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $pending = Staff::create([
            'staff_id' => 'RCC-930',
            'first_name' => 'Pending',
            'last_name' => 'Tracker',
            'email' => 'pending.tracker@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        $submitted = Staff::create([
            'staff_id' => 'RCC-931',
            'first_name' => 'Submitted',
            'last_name' => 'Tracker',
            'email' => 'submitted.tracker@africacdc.org',
            'gender' => 'female',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $submitted->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'submitted',
            'submitted_at' => now(),
        ]);

        WeeklyTracker::create([
            'staff_id' => $pending->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'status' => 'at_duty_station',
            'submission_status' => 'draft',
        ]);

        $service = app(WeeklyTrackerReminderService::class);
        $needing = $service->getStaffNeedingSubmission($weekStart);

        $this->assertTrue($needing->contains('id', $pending->id));
        $this->assertFalse($needing->contains('id', $submitted->id));
    }

    public function test_admin_can_disable_all_notifications_from_settings(): void
    {
        $this->seed(SettingsSeeder::class);
        Setting::clearCache();

        Setting::where('key', 'notifications_enabled')->update(['value' => '0']);
        Setting::clearCache();

        $settings = app(ReminderSettingsService::class);

        $this->assertFalse($settings->masterEnabled());
        $this->assertFalse($settings->weeklyTrackerSundayEnabled());
        $this->assertFalse($settings->weeklyTrackerDailyEnabled());
        $this->assertFalse($settings->activityReportsEnabled());
    }

    public function test_sunday_reminder_is_skipped_after_already_sent_for_week(): void
    {
        $position = Position::create(['title' => 'Officer', 'is_active' => true]);
        $weekStart = now()->startOfWeek();

        $staff = Staff::create([
            'staff_id' => 'RCC-932',
            'first_name' => 'Sunday',
            'last_name' => 'Reminder',
            'email' => 'sunday.reminder@africacdc.org',
            'gender' => 'male',
            'position_id' => $position->id,
            'status' => 'active',
            'is_admin' => false,
            'hire_date' => now()->toDateString(),
        ]);

        EmailReminderLog::create([
            'staff_id' => $staff->id,
            'week_start_date' => $weekStart->toDateString(),
            'reminder_type' => EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY,
            'recipient_email' => $staff->email,
            'sent_at' => now(),
        ]);

        $preview = app(WeeklyTrackerReminderService::class)
            ->previewPendingReminders(EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY);

        $this->assertFalse($preview->contains('id', $staff->id));
    }

    public function test_admin_settings_page_shows_notifications_tab(): void
    {
        $admin = $this->seedWarccAdmin();
        $this->seed(SettingsSeeder::class);
        Setting::clearCache();

        $this->actingAs($admin, 'staff')
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Staff Email Notifications', false)
            ->assertSee('Sunday Weekly Tracker Reminder', false)
            ->assertSee('Weekday Morning Tracker Reminder', false);
    }
}
