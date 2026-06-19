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
    ->when(fn () => (bool) config('reminders.enabled'))
    ->withoutOverlapping();
