<?php

namespace App\Services;

use App\Models\ActivityCalendar;
use App\Models\EmailReminderLog;
use App\Models\Staff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ActivityReportReminderService
{
    public function __construct(
        private readonly ActivityWorkflowService $workflow,
        private readonly MicrosoftGraphService $graph,
        private readonly ReminderSettingsService $settings
    ) {}

    public function isEnabled(): bool
    {
        return $this->settings->activityReportsEnabled() && $this->graph->isConfigured();
    }

    /**
     * @return array{sent: int, skipped: int, failed: int, details: list<string>}
     */
    public function sendDueReminders(bool $dryRun = false): array
    {
        $result = [
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
            'details' => [],
        ];

        if (! $this->isEnabled()) {
            $result['details'][] = 'Reminders disabled or Microsoft Graph is not configured.';

            return $result;
        }

        $sendAs = config('reminders.microsoft.send_as');
        if (empty($sendAs)) {
            $result['details'][] = 'MICROSOFT_MAIL_FROM / MAIL_FROM_ADDRESS is not set.';

            return $result;
        }

        $staffMembers = Staff::query()
            ->where('status', 'active')
            ->whereNotNull('email')
            ->get();

        foreach ($staffMembers as $staff) {
            $pendingActivities = $this->workflow->getPendingReportsForStaff($staff);

            foreach ($pendingActivities as $activity) {
                if (! $this->shouldSend($staff, $activity)) {
                    $result['skipped']++;
                    continue;
                }

                if ($dryRun) {
                    $result['sent']++;
                    $result['details'][] = "[dry-run] Would remind {$staff->email} about: {$activity->title}";
                    continue;
                }

                try {
                    $this->graph->sendEmail(
                        $staff->email,
                        $this->buildSubject($activity),
                        $this->buildBody($staff, $activity),
                        $sendAs
                    );

                    EmailReminderLog::create([
                        'staff_id' => $staff->id,
                        'activity_calendar_id' => $activity->id,
                        'reminder_type' => EmailReminderLog::TYPE_ACTIVITY_REPORT_DUE,
                        'recipient_email' => $staff->email,
                        'sent_at' => now(),
                    ]);

                    $result['sent']++;
                    $result['details'][] = "Sent to {$staff->email}: {$activity->title}";
                } catch (\Throwable $e) {
                    $result['failed']++;
                    $result['details'][] = "Failed for {$staff->email} ({$activity->title}): {$e->getMessage()}";
                    Log::error('Activity report reminder failed', [
                        'staff_id' => $staff->id,
                        'activity_id' => $activity->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Preview who would be reminded (admin diagnostics).
     */
    public function previewDueReminders(): Collection
    {
        return Staff::query()
            ->where('status', 'active')
            ->whereNotNull('email')
            ->get()
            ->map(function (Staff $staff) {
                $activities = $this->workflow->getPendingReportsForStaff($staff)
                    ->filter(fn (ActivityCalendar $activity) => $this->shouldSend($staff, $activity))
                    ->values();

                return [
                    'staff' => $staff,
                    'activities' => $activities,
                ];
            })
            ->filter(fn (array $row) => $row['activities']->isNotEmpty())
            ->values();
    }

    private function shouldSend(Staff $staff, ActivityCalendar $activity): bool
    {
        $cooldownDays = (int) config('reminders.activity_reports.cooldown_days', 3);

        $recent = EmailReminderLog::query()
            ->where('staff_id', $staff->id)
            ->where('activity_calendar_id', $activity->id)
            ->where('reminder_type', EmailReminderLog::TYPE_ACTIVITY_REPORT_DUE)
            ->where('sent_at', '>=', now()->subDays($cooldownDays))
            ->exists();

        return ! $recent;
    }

    private function buildSubject(ActivityCalendar $activity): string
    {
        return 'Reminder: Submit your activity report — ' . $activity->title;
    }

    private function buildBody(Staff $staff, ActivityCalendar $activity): string
    {
        return view('emails.activity-report-reminder', [
            'staff' => $staff,
            'activity' => $activity,
            'submitUrl' => route('staff.activity-reports.create', [
                'activity_calendar_id' => $activity->id,
            ]),
            'dashboardUrl' => route('staff.dashboard'),
            'appName' => config('app.name', 'WARCC'),
        ])->render();
    }
}
