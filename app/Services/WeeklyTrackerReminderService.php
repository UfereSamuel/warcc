<?php

namespace App\Services;

use App\Models\EmailReminderLog;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WeeklyTrackerReminderService
{
    public function __construct(
        private readonly MicrosoftGraphService $graph,
        private readonly ReminderSettingsService $settings
    ) {}

    public function isSundayRunEnabled(): bool
    {
        return $this->settings->weeklyTrackerSundayEnabled() && $this->graph->isConfigured();
    }

    public function isDailyRunEnabled(): bool
    {
        return $this->settings->weeklyTrackerDailyEnabled() && $this->graph->isConfigured();
    }

    /**
     * @return array{sent: int, skipped: int, failed: int, details: list<string>}
     */
    public function sendSundayReminders(bool $dryRun = false): array
    {
        return $this->sendReminders(
            EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY,
            fn (Staff $staff, Carbon $weekStart) => $this->shouldSendSunday($staff, $weekStart),
            $dryRun,
            $this->isSundayRunEnabled(),
            'Sunday weekly tracker reminders are disabled or Microsoft Graph is not configured.'
        );
    }

    /**
     * @return array{sent: int, skipped: int, failed: int, details: list<string>}
     */
    public function sendDailyReminders(bool $dryRun = false): array
    {
        return $this->sendReminders(
            EmailReminderLog::TYPE_WEEKLY_TRACKER_DAILY,
            fn (Staff $staff, Carbon $weekStart) => $this->shouldSendDaily($staff, $weekStart),
            $dryRun,
            $this->isDailyRunEnabled(),
            'Daily weekly tracker reminders are disabled or Microsoft Graph is not configured.'
        );
    }

    public function previewPendingReminders(string $type): Collection
    {
        $weekStart = now()->startOfWeek();
        $checker = $type === EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY
            ? fn (Staff $staff) => $this->shouldSendSunday($staff, $weekStart)
            : fn (Staff $staff) => $this->shouldSendDaily($staff, $weekStart);

        return $this->getStaffNeedingSubmission($weekStart)
            ->filter($checker)
            ->values();
    }

    public function getStaffNeedingSubmission(?Carbon $weekStart = null): Collection
    {
        $weekStart = ($weekStart ?? now())->copy()->startOfWeek();

        $submittedStaffIds = WeeklyTracker::query()
            ->whereDate('week_start_date', $weekStart)
            ->where('submission_status', 'submitted')
            ->pluck('staff_id');

        return Staff::query()
            ->where('status', 'active')
            ->whereNotNull('email')
            ->whereNotIn('id', $submittedStaffIds)
            ->orderBy('first_name')
            ->get();
    }

    /**
     * @param  callable(Staff, Carbon): bool  $shouldSend
     * @return array{sent: int, skipped: int, failed: int, details: list<string>}
     */
    private function sendReminders(
        string $reminderType,
        callable $shouldSend,
        bool $dryRun,
        bool $enabled,
        string $disabledMessage
    ): array {
        $result = [
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
            'details' => [],
        ];

        if (! $enabled) {
            $result['details'][] = $disabledMessage;

            return $result;
        }

        $sendAs = config('reminders.microsoft.send_as');
        if (empty($sendAs)) {
            $result['details'][] = 'MICROSOFT_MAIL_FROM / MAIL_FROM_ADDRESS is not set.';

            return $result;
        }

        $weekStart = now()->startOfWeek();
        $weekLabel = $weekStart->format('M d').' – '.$weekStart->copy()->endOfWeek()->format('M d, Y');

        foreach ($this->getStaffNeedingSubmission($weekStart) as $staff) {
            if (! $shouldSend($staff, $weekStart)) {
                $result['skipped']++;
                continue;
            }

            if ($dryRun) {
                $result['sent']++;
                $result['details'][] = "[dry-run] Would remind {$staff->email} for week {$weekLabel}";
                continue;
            }

            try {
                $this->graph->sendEmail(
                    $staff->email,
                    $this->buildSubject($reminderType, $weekLabel),
                    $this->buildBody($staff, $weekStart, $weekLabel, $reminderType),
                    $sendAs
                );

                EmailReminderLog::create([
                    'staff_id' => $staff->id,
                    'activity_calendar_id' => null,
                    'week_start_date' => $weekStart->toDateString(),
                    'reminder_type' => $reminderType,
                    'recipient_email' => $staff->email,
                    'sent_at' => now(),
                ]);

                $result['sent']++;
                $result['details'][] = "Sent to {$staff->email} ({$weekLabel})";
            } catch (\Throwable $e) {
                $result['failed']++;
                $result['details'][] = "Failed for {$staff->email}: {$e->getMessage()}";
                Log::error('Weekly tracker reminder failed', [
                    'staff_id' => $staff->id,
                    'reminder_type' => $reminderType,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    private function shouldSendSunday(Staff $staff, Carbon $weekStart): bool
    {
        return ! EmailReminderLog::query()
            ->where('staff_id', $staff->id)
            ->where('reminder_type', EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY)
            ->whereDate('week_start_date', $weekStart)
            ->exists();
    }

    private function shouldSendDaily(Staff $staff, Carbon $weekStart): bool
    {
        return ! EmailReminderLog::query()
            ->where('staff_id', $staff->id)
            ->where('reminder_type', EmailReminderLog::TYPE_WEEKLY_TRACKER_DAILY)
            ->whereDate('week_start_date', $weekStart)
            ->where('sent_at', '>=', now()->startOfDay())
            ->exists();
    }

    private function buildSubject(string $reminderType, string $weekLabel): string
    {
        $prefix = $reminderType === EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY
            ? 'Reminder: Submit your weekly tracker'
            : 'Reminder: Weekly tracker still pending';

        return "{$prefix} ({$weekLabel})";
    }

    private function buildBody(Staff $staff, Carbon $weekStart, string $weekLabel, string $reminderType): string
    {
        return view('emails.weekly-tracker-reminder', [
            'staff' => $staff,
            'weekLabel' => $weekLabel,
            'isSundayReminder' => $reminderType === EmailReminderLog::TYPE_WEEKLY_TRACKER_SUNDAY,
            'trackerUrl' => route('staff.tracker.index'),
            'createUrl' => route('staff.tracker.create'),
            'dashboardUrl' => route('staff.dashboard'),
            'appName' => config('app.name', 'WARCC'),
        ])->render();
    }
}
