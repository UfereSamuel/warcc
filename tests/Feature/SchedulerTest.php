<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class SchedulerTest extends TestCase
{
    public function test_activity_report_reminder_command_is_scheduled(): void
    {
        $schedule = app(Schedule::class);

        $matched = collect($schedule->events())->contains(function ($event) {
            return str_contains($event->command ?? '', 'reminders:activity-reports');
        });

        $this->assertTrue($matched, 'Expected reminders:activity-reports to be scheduled.');
    }
}
