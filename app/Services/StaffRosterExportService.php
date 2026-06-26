<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffRosterExportService
{
    public function __construct(
        private StaffStatusAnalyticsService $analytics,
        private MissionComplianceService $compliance
    ) {}

    /**
     * Status counts for each week in a range (oldest first).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getWeeklyStatusSummaries(Carbon $endWeekStart, int $weekCount): Collection
    {
        $weekCount = max(1, min($weekCount, 52));
        $endWeekStart = $endWeekStart->copy()->startOfWeek(Carbon::MONDAY);

        return collect(range($weekCount - 1, 0))
            ->map(function (int $weeksAgo) use ($endWeekStart) {
                $weekStart = $endWeekStart->copy()->subWeeks($weeksAgo);
                $roster = $this->analytics->getStaffRosterWidgetData($weekStart);
                $counts = $roster['counts'];
                $listed = $counts['at_duty_station']
                    + $counts['on_mission']
                    + $counts['on_leave']
                    + $counts['not_submitted'];

                return [
                    'week_start' => $roster['week_start'],
                    'week_end' => $roster['week_end'],
                    'week_label' => $roster['week_label'],
                    'at_duty_station' => $counts['at_duty_station'],
                    'on_mission' => $counts['on_mission'],
                    'on_leave' => $counts['on_leave'],
                    'not_submitted' => $counts['not_submitted'],
                    'total_listed' => $listed,
                    'total_active' => $roster['total_active'],
                ];
            })
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $entries
     */
    public function streamDetailCsv(Collection $entries, array $staffRoster): StreamedResponse
    {
        $filename = sprintf(
            'staff_roster_%s_to_%s.csv',
            $staffRoster['week_start'],
            $staffRoster['week_end']
        );

        $isCurrentWeek = Carbon::parse($staffRoster['week_start'])->isSameDay(now()->startOfWeek(Carbon::MONDAY));

        return $this->streamCsv($filename, function ($handle) use ($entries, $staffRoster, $isCurrentWeek) {
            $headers = [
                'Week Start',
                'Week End',
                'Staff ID',
                'Staff Name',
                'Position',
                'Status',
                'Mission Title',
                'Mission Dates',
                'Leave Type',
                'Leave Dates',
                'Mission Report Status',
            ];

            if ($isCurrentWeek) {
                $headers[] = 'Clocked In Today';
            }

            fputcsv($handle, $headers);

            foreach ($entries as $entry) {
                $staff = $entry['staff'];
                $row = [
                    $staffRoster['week_start'],
                    $staffRoster['week_end'],
                    $staff->staff_id,
                    $staff->full_name,
                    $entry['position_title'] ?? 'Unassigned',
                    $entry['status_label'],
                    $entry['mission_title'] ?? '',
                    $entry['mission_range'] ?? '',
                    $entry['leave_type'] ?? '',
                    $entry['leave_range'] ?? '',
                    $entry['mission_report_status']
                        ? $this->compliance->reportStatusLabel($entry['mission_report_status'])
                        : '',
                ];

                if ($isCurrentWeek) {
                    $row[] = $entry['clocked_in_today'] ? 'Yes' : 'No';
                }

                fputcsv($handle, $row);
            }
        });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $summaries
     */
    public function streamSummaryCsv(Collection $summaries): StreamedResponse
    {
        $firstWeek = $summaries->first()['week_start'] ?? now()->toDateString();
        $lastWeek = $summaries->last()['week_start'] ?? $firstWeek;
        $filename = "staff_status_summary_{$firstWeek}_to_{$lastWeek}.csv";

        return $this->streamCsv($filename, function ($handle) use ($summaries) {
            fputcsv($handle, [
                'Week Start',
                'Week End',
                'Week Label',
                'At Duty Station',
                'On Mission',
                'On Leave',
                'Not Submitted',
                'Total Listed',
                'Total Active Staff',
            ]);

            foreach ($summaries as $summary) {
                fputcsv($handle, [
                    $summary['week_start'],
                    $summary['week_end'],
                    $summary['week_label'],
                    $summary['at_duty_station'],
                    $summary['on_mission'],
                    $summary['on_leave'],
                    $summary['not_submitted'],
                    $summary['total_listed'],
                    $summary['total_active'],
                ]);
            }
        });
    }

    private function streamCsv(string $filename, callable $writer): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($writer) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            $writer($handle);
            fclose($handle);
        }, 200, $headers);
    }
}
