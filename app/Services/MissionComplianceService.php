<?php

namespace App\Services;

use App\Models\ActivityReport;
use App\Models\WeeklyTracker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MissionComplianceService
{
    public const STATUS_MISSING = 'missing';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_REVIEWED = 'reviewed';

    /**
     * @return array{
     *     week_start: string,
     *     week_label: string,
     *     summary: array{total: int, missing: int, draft: int, submitted: int, reviewed: int},
     *     items: Collection<int, array{
     *         tracker: WeeklyTracker,
     *         staff: \App\Models\Staff,
     *         mission_title: string|null,
     *         mission_range: string|null,
     *         report_status: string,
     *         report: ActivityReport|null
     *     }>
     * }
     */
    public function getMissionComplianceForWeek(?Carbon $weekStart = null): array
    {
        $weekStart = ($weekStart ?? now())->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $trackers = WeeklyTracker::query()
            ->with(['staff.position', 'activityReport'])
            ->whereDate('week_start_date', $weekStart)
            ->where('submission_status', 'submitted')
            ->where('status', 'on_mission')
            ->orderByDesc('mission_end_date')
            ->get();

        $items = $trackers->map(fn (WeeklyTracker $tracker) => $this->buildComplianceItem($tracker));

        $summary = [
            'total' => $items->count(),
            'missing' => $items->where('report_status', self::STATUS_MISSING)->count(),
            'draft' => $items->where('report_status', self::STATUS_DRAFT)->count(),
            'submitted' => $items->where('report_status', self::STATUS_SUBMITTED)->count(),
            'reviewed' => $items->where('report_status', self::STATUS_REVIEWED)->count(),
        ];

        return [
            'week_start' => $weekStart->toDateString(),
            'week_label' => $weekStart->format('M d').' – '.$weekEnd->format('M d, Y'),
            'summary' => $summary,
            'items' => $items,
        ];
    }

    public function resolveMissionReportStatus(WeeklyTracker $tracker): string
    {
        $report = $tracker->relationLoaded('activityReport')
            ? $tracker->activityReport
            : $tracker->getMissionReport();

        if (! $report) {
            return self::STATUS_MISSING;
        }

        return match ($report->status) {
            'draft' => self::STATUS_DRAFT,
            'submitted' => self::STATUS_SUBMITTED,
            'reviewed' => self::STATUS_REVIEWED,
            default => self::STATUS_MISSING,
        };
    }

    public function reportStatusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_MISSING => 'Report Missing',
            self::STATUS_DRAFT => 'Draft Report',
            self::STATUS_SUBMITTED => 'Report Submitted',
            self::STATUS_REVIEWED => 'Report Reviewed',
            default => ucfirst($status),
        };
    }

    public function reportStatusBadgeClass(string $status): string
    {
        return match ($status) {
            self::STATUS_MISSING => 'danger',
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'warning',
            self::STATUS_REVIEWED => 'success',
            default => 'light',
        };
    }

    /**
     * @return array{
     *     tracker: WeeklyTracker,
     *     staff: \App\Models\Staff,
     *     mission_title: string|null,
     *     mission_range: string|null,
     *     report_status: string,
     *     report: ActivityReport|null
     * }
     */
    public function buildComplianceItem(WeeklyTracker $tracker): array
    {
        $report = $tracker->relationLoaded('activityReport')
            ? $tracker->activityReport
            : $tracker->getMissionReport();

        $missionRange = null;
        if ($tracker->mission_start_date && $tracker->mission_end_date) {
            $missionRange = $tracker->mission_start_date->format('M d')
                .' – '
                .$tracker->mission_end_date->format('M d, Y');
        }

        return [
            'tracker' => $tracker,
            'staff' => $tracker->staff,
            'mission_title' => $tracker->mission_title,
            'mission_range' => $missionRange,
            'report_status' => $this->resolveMissionReportStatus($tracker),
            'report' => $report,
        ];
    }
}
