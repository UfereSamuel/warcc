<?php

namespace App\Console\Commands;

use App\Services\ActivityReportReminderService;
use Illuminate\Console\Command;

class SendActivityReportReminders extends Command
{
    protected $signature = 'reminders:activity-reports
                            {--dry-run : Preview reminders without sending email}';

    protected $description = 'Send email reminders for overdue post-activity reports via Microsoft Graph';

    public function handle(ActivityReportReminderService $reminderService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $reminderService->isEnabled()) {
            $this->warn('Activity report reminders are disabled or Microsoft Graph is not configured.');
            $this->line('Set MICROSOFT_CLIENT_ID, MICROSOFT_CLIENT_SECRET, MICROSOFT_TENANT_ID, and MICROSOFT_MAIL_FROM in .env');

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info('Dry run — no emails will be sent.');
        }

        $result = $reminderService->sendDueReminders($dryRun);

        $this->info("Sent: {$result['sent']} | Skipped (cooldown): {$result['skipped']} | Failed: {$result['failed']}");

        foreach ($result['details'] as $line) {
            $this->line('  • ' . $line);
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
