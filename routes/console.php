<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reminders:activity-reports')
    ->dailyAt(env('REMINDER_DAILY_AT', '08:00'))
    ->timezone(env('APP_TIMEZONE', 'UTC'))
    ->when(fn () => app(\App\Services\ReminderSettingsService::class)->activityReportsEnabled())
    ->withoutOverlapping();

Schedule::command('reminders:weekly-trackers --type=sunday')
    ->weeklyOn(0, env('REMINDER_WEEKLY_TRACKER_SUNDAY_AT', '18:00'))
    ->timezone(env('APP_TIMEZONE', 'UTC'))
    ->when(fn () => app(\App\Services\ReminderSettingsService::class)->weeklyTrackerSundayEnabled())
    ->withoutOverlapping();

Schedule::command('reminders:weekly-trackers --type=daily')
    ->weekdays()
    ->at(env('REMINDER_DAILY_AT', '08:00'))
    ->timezone(env('APP_TIMEZONE', 'UTC'))
    ->when(fn () => app(\App\Services\ReminderSettingsService::class)->weeklyTrackerDailyEnabled())
    ->withoutOverlapping();
