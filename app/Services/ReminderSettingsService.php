<?php

namespace App\Services;

use App\Models\Setting;

class ReminderSettingsService
{
    public function masterEnabled(): bool
    {
        return $this->bool('notifications_enabled', (bool) config('reminders.enabled', true));
    }

    public function activityReportsEnabled(): bool
    {
        return $this->masterEnabled()
            && $this->bool('activity_report_reminders_enabled', (bool) config('reminders.activity_reports.enabled', true));
    }

    public function weeklyTrackerSundayEnabled(): bool
    {
        return $this->masterEnabled()
            && $this->bool('weekly_tracker_sunday_reminders_enabled', (bool) config('reminders.weekly_trackers.sunday_enabled', true));
    }

    public function weeklyTrackerDailyEnabled(): bool
    {
        return $this->masterEnabled()
            && $this->bool('weekly_tracker_daily_reminders_enabled', (bool) config('reminders.weekly_trackers.daily_enabled', true));
    }

    /**
     * @return array<string, bool>
     */
    public function summary(): array
    {
        return [
            'master' => $this->masterEnabled(),
            'activity_reports' => $this->activityReportsEnabled(),
            'weekly_tracker_sunday' => $this->weeklyTrackerSundayEnabled(),
            'weekly_tracker_daily' => $this->weeklyTrackerDailyEnabled(),
        ];
    }

    private function bool(string $key, bool $default): bool
    {
        $value = Setting::get($key);

        if ($value === null) {
            return $default;
        }

        return in_array($value, ['1', 1, 'true', true], true);
    }
}
