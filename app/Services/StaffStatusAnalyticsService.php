<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaffStatusAnalyticsService
{
    public const STATUS_AT_DUTY_STATION = 'at_duty_station';

    public const STATUS_ON_MISSION = 'on_mission';

    public const STATUS_ON_LEAVE = 'on_leave';

    public const STATUS_NOT_SUBMITTED = 'not_submitted';

    /**
     * @return array<string, string>
     */
    public function statusLabels(): array
    {
        return [
            self::STATUS_AT_DUTY_STATION => 'At Duty Station',
            self::STATUS_ON_MISSION => 'On Mission',
            self::STATUS_ON_LEAVE => 'On Leave',
            self::STATUS_NOT_SUBMITTED => 'Not Submitted',
        ];
    }

    public function statusLabel(string $status): string
    {
        return $this->statusLabels()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            self::STATUS_AT_DUTY_STATION => 'success',
            self::STATUS_ON_MISSION => 'warning',
            self::STATUS_ON_LEAVE => 'info',
            self::STATUS_NOT_SUBMITTED => 'secondary',
            default => 'light',
        };
    }
    /**
     * Staff status counts from submitted weekly trackers for the given week.
     *
     * @return array{
     *     at_duty_station: int,
     *     on_mission: int,
     *     on_leave: int,
     *     not_submitted: int,
     *     week_start: string,
     *     week_label: string
     * }
     */
    public function getCurrentWeekTrackerStatusCounts(?Carbon $weekStart = null): array
    {
        $weekStart = ($weekStart ?? now())->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $trackers = WeeklyTracker::query()
            ->whereDate('week_start_date', $weekStart)
            ->where('submission_status', 'submitted')
            ->get();

        return [
            'at_duty_station' => $trackers->where('status', 'at_duty_station')->count(),
            'on_mission' => $trackers->where('status', 'on_mission')->count(),
            'on_leave' => $trackers->where('status', 'on_leave')->count(),
            'not_submitted' => $this->countActiveStaffWithoutSubmittedTracker($weekStart),
            'week_start' => $weekStart->toDateString(),
            'week_label' => $weekStart->format('M d').' – '.$weekEnd->format('M d, Y'),
        ];
    }

    /**
     * Dashboard-friendly staff status payload (includes legacy keys used by the chart view).
     *
     * @return array<string, mixed>
     */
    public function getDashboardStaffStatusData(?Carbon $weekStart = null): array
    {
        $counts = $this->getCurrentWeekTrackerStatusCounts($weekStart);

        return [
            ...$counts,
            'at_office' => $counts['at_duty_station'],
            'source' => 'weekly_trackers',
        ];
    }

    /**
     * Named roster grouped by weekly tracker status for the current week.
     *
     * @return array<string, Collection<int, array<string, mixed>>>
     */
    public function getCurrentWeekRoster(?Carbon $weekStart = null): array
    {
        return $this->getStaffRosterWidgetData($weekStart)['groups'];
    }

    /**
     * @return array{
     *     week_start: string,
     *     week_end: string,
     *     week_label: string,
     *     counts: array<string, int>,
     *     total_active: int,
     *     groups: array<string, Collection<int, array<string, mixed>>>
     * }
     */
    public function getStaffRosterWidgetData(?Carbon $weekStart = null): array
    {
        $weekStart = ($weekStart ?? now())->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $counts = $this->getCurrentWeekTrackerStatusCounts($weekStart);
        $clockedInToday = $this->getStaffClockedInTodayIds();
        $complianceService = app(MissionComplianceService::class);

        $trackers = WeeklyTracker::query()
            ->with(['staff.position', 'leaveType', 'activityReport'])
            ->whereDate('week_start_date', $weekStart)
            ->where('submission_status', 'submitted')
            ->get()
            ->sortBy(fn (WeeklyTracker $tracker) => $tracker->staff?->first_name ?? '');

        $groups = [
            self::STATUS_AT_DUTY_STATION => $trackers
                ->where('status', self::STATUS_AT_DUTY_STATION)
                ->values()
                ->map(fn (WeeklyTracker $tracker) => $this->buildRosterEntry(
                    $tracker->staff,
                    $tracker,
                    self::STATUS_AT_DUTY_STATION,
                    $clockedInToday,
                    $complianceService
                )),
            self::STATUS_ON_MISSION => $trackers
                ->where('status', self::STATUS_ON_MISSION)
                ->values()
                ->map(fn (WeeklyTracker $tracker) => $this->buildRosterEntry(
                    $tracker->staff,
                    $tracker,
                    self::STATUS_ON_MISSION,
                    $clockedInToday,
                    $complianceService
                )),
            self::STATUS_ON_LEAVE => $trackers
                ->where('status', self::STATUS_ON_LEAVE)
                ->values()
                ->map(fn (WeeklyTracker $tracker) => $this->buildRosterEntry(
                    $tracker->staff,
                    $tracker,
                    self::STATUS_ON_LEAVE,
                    $clockedInToday,
                    $complianceService
                )),
            self::STATUS_NOT_SUBMITTED => Staff::query()
                ->with('position')
                ->where('status', 'active')
                ->whereNotIn('id', $trackers->pluck('staff_id'))
                ->orderBy('first_name')
                ->get()
                ->map(fn (Staff $staff) => $this->buildRosterEntry(
                    $staff,
                    null,
                    self::STATUS_NOT_SUBMITTED,
                    $clockedInToday,
                    $complianceService
                )),
        ];

        return [
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'week_label' => $weekStart->format('M d').' – '.$weekEnd->format('M d, Y'),
            'counts' => $counts,
            'total_active' => Staff::where('status', 'active')->count(),
            'groups' => $groups,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function flattenRosterGroups(array $groups): Collection
    {
        return collect($groups)
            ->flatMap(fn (Collection $entries) => $entries)
            ->values();
    }

    /**
     * @param  array<string, Collection<int, array<string, mixed>>>  $groups
     * @return Collection<int, array<string, mixed>>
     */
    public function filterRosterGroups(
        array $groups,
        ?string $status = null,
        ?int $positionId = null,
        ?string $search = null
    ): Collection {
        return $this->flattenRosterGroups($groups)
            ->when($status, fn (Collection $entries) => $entries->where('status', $status))
            ->when($positionId, fn (Collection $entries) => $entries->where('position_id', $positionId))
            ->when($search, function (Collection $entries) use ($search) {
                $needle = mb_strtolower($search);

                return $entries->filter(function (array $entry) use ($needle) {
                    $staff = $entry['staff'];
                    $haystack = mb_strtolower(implode(' ', array_filter([
                        $staff->full_name,
                        $staff->staff_id,
                        $entry['position_title'],
                        $entry['mission_title'],
                        $entry['leave_type'],
                    ])));

                    return str_contains($haystack, $needle);
                });
            })
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRosterEntry(
        Staff $staff,
        ?WeeklyTracker $tracker,
        string $status,
        Collection $clockedInToday,
        MissionComplianceService $complianceService
    ): array {
        $missionRange = null;
        if ($tracker?->mission_start_date && $tracker?->mission_end_date) {
            $missionRange = $tracker->mission_start_date->format('M d')
                .' – '
                .$tracker->mission_end_date->format('M d, Y');
        }

        $leaveRange = null;
        if ($tracker?->leave_start_date && $tracker?->leave_end_date) {
            $leaveRange = $tracker->leave_start_date->format('M d')
                .' – '
                .$tracker->leave_end_date->format('M d, Y');
        }

        $missionReportStatus = null;
        if ($tracker && $status === self::STATUS_ON_MISSION) {
            $missionReportStatus = $complianceService->resolveMissionReportStatus($tracker);
        }

        return [
            'staff' => $staff,
            'tracker' => $tracker,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'status_badge_class' => $this->statusBadgeClass($status),
            'position_id' => $staff->position_id,
            'position_title' => $staff->position?->title,
            'mission_title' => $status === self::STATUS_ON_MISSION ? $tracker?->mission_title : null,
            'mission_range' => $missionRange,
            'leave_type' => $tracker?->leaveType?->name,
            'leave_range' => $leaveRange,
            'clocked_in_today' => $clockedInToday->contains($staff->id),
            'mission_report_status' => $missionReportStatus,
        ];
    }

    /**
     * @return Collection<int, int>
     */
    private function getStaffClockedInTodayIds(): Collection
    {
        return Attendance::query()
            ->whereDate('date', today())
            ->whereNotNull('clock_in_time')
            ->pluck('staff_id');
    }

    public function countActiveStaffWithoutSubmittedTracker(Carbon $weekStart): int
    {
        return Staff::query()
            ->where('status', 'active')
            ->where(function ($query) use ($weekStart) {
                $query->whereDoesntHave('weeklyTrackers', function ($trackerQuery) use ($weekStart) {
                    $trackerQuery->whereDate('week_start_date', $weekStart);
                })->orWhereHas('weeklyTrackers', function ($trackerQuery) use ($weekStart) {
                    $trackerQuery->whereDate('week_start_date', $weekStart)
                        ->where('submission_status', 'draft');
                });
            })
            ->count();
    }
}
