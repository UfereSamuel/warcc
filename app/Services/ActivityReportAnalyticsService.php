<?php

namespace App\Services;

use App\Models\ActivityReport;

class ActivityReportAnalyticsService
{
    /**
     * @return array{
     *     total: int,
     *     submitted: int,
     *     reviewed: int,
     *     mission_total: int,
     *     mission_submitted: int
     * }
     */
    public function getDashboardStats(): array
    {
        return [
            'total' => ActivityReport::count(),
            'submitted' => ActivityReport::where('status', 'submitted')->count(),
            'reviewed' => ActivityReport::where('status', 'reviewed')->count(),
            'mission_total' => ActivityReport::whereNotNull('weekly_tracker_id')->count(),
            'mission_submitted' => ActivityReport::where('status', 'submitted')
                ->whereNotNull('weekly_tracker_id')
                ->count(),
        ];
    }

    /**
     * @return array{
     *     total: int,
     *     draft: int,
     *     submitted: int,
     *     reviewed: int,
     *     mission: int,
     *     mission_submitted: int,
     *     mission_reviewed: int,
     *     calendar: int,
     *     standalone: int
     * }
     */
    public function getIndexStats(): array
    {
        return [
            'total' => ActivityReport::count(),
            'draft' => ActivityReport::where('status', 'draft')->count(),
            'submitted' => ActivityReport::where('status', 'submitted')->count(),
            'reviewed' => ActivityReport::where('status', 'reviewed')->count(),
            'mission' => ActivityReport::whereNotNull('weekly_tracker_id')->count(),
            'mission_submitted' => ActivityReport::whereNotNull('weekly_tracker_id')
                ->where('status', 'submitted')
                ->count(),
            'mission_reviewed' => ActivityReport::whereNotNull('weekly_tracker_id')
                ->where('status', 'reviewed')
                ->count(),
            'calendar' => ActivityReport::whereNotNull('activity_calendar_id')
                ->whereNull('weekly_tracker_id')
                ->count(),
            'standalone' => ActivityReport::whereNull('activity_calendar_id')
                ->whereNull('weekly_tracker_id')
                ->count(),
        ];
    }
}
