<?php

namespace App\Console\Commands;

use App\Services\WeeklyTrackerReminderService;
use Illuminate\Console\Command;

class SendWeeklyTrackerReminders extends Command
{
    protected $signature = 'reminders:weekly-trackers
                            {--type=sunday : sunday or daily}
                            {--dry-run : Preview reminders without sending email}';

    protected $description = 'Send weekly tracker submission reminders via Microsoft Graph';

    public function handle(WeeklyTrackerReminderService $reminderService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $type = strtolower((string) $this->option('type'));

        if ($dryRun) {
            $this->info('Dry run — no emails will be sent.');
        }

        $result = match ($type) {
            'sunday' => $reminderService->sendSundayReminders($dryRun),
            'daily' => $reminderService->sendDailyReminders($dryRun),
            default => null,
        };

        if ($result === null) {
            $this->error('Invalid --type. Use sunday or daily.');

            return self::FAILURE;
        }

        $this->info("Sent: {$result['sent']} | Skipped: {$result['skipped']} | Failed: {$result['failed']}");

        foreach ($result['details'] as $line) {
            $this->line('  • '.$line);
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
