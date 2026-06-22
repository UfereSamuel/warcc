<?php

namespace App\Services;

use App\Models\ActivityCalendar;
use App\Models\ActivityReport;
use App\Models\ActivityRequest;
use App\Models\Staff;
use App\Models\WeeklyTracker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActivityWorkflowService
{
    /** Activity types that require a post-activity report when marked done. */
    public const REPORTABLE_TYPES = ['training', 'event', 'mission', 'workshop'];

    public function reportableTypes(): array
    {
        return self::REPORTABLE_TYPES;
    }

    public function activityRequiresReport(ActivityCalendar $activity): bool
    {
        return in_array($activity->type, self::REPORTABLE_TYPES, true);
    }

    /**
     * Calendar activities a staff member can link when reporting on mission for a given week.
     */
    public function getLinkableActivitiesForStaff(Staff $staff, Carbon $weekStart, Carbon $weekEnd): Collection
    {
        $requestedIds = ActivityRequest::query()
            ->where('requested_by', $staff->id)
            ->where('status', 'approved')
            ->whereNotNull('approved_activity_id')
            ->pluck('approved_activity_id');

        return ActivityCalendar::query()
            ->whereNotIn('type', ['holiday', 'deadline'])
            ->whereIn('status', ['not_yet_started', 'ongoing'])
            ->where(function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('start_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhereBetween('end_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhere(function ($inner) use ($weekStart, $weekEnd) {
                        $inner->where('start_date', '<=', $weekStart)
                            ->where('end_date', '>=', $weekEnd);
                    });
            })
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Submitted on-mission weekly trackers that still need a mission report filed.
     */
    public function getUnreportedMissionTrackers(Staff $staff): Collection
    {
        $reportedTrackerIds = ActivityReport::query()
            ->where('staff_id', $staff->id)
            ->whereNotNull('weekly_tracker_id')
            ->whereIn('status', ['submitted', 'reviewed'])
            ->pluck('weekly_tracker_id');

        return WeeklyTracker::query()
            ->with('activity')
            ->where('staff_id', $staff->id)
            ->where('status', 'on_mission')
            ->where('submission_status', 'submitted')
            ->whereNotNull('mission_title')
            ->whereNotIn('id', $reportedTrackerIds)
            ->orderByDesc('mission_end_date')
            ->get();
    }

    /**
     * Mission trackers available when creating a new report (unreported + draft in progress).
     */
    public function getSelectableMissionTrackersForReport(Staff $staff): Collection
    {
        $draftTrackerIds = ActivityReport::query()
            ->where('staff_id', $staff->id)
            ->whereNotNull('weekly_tracker_id')
            ->where('status', 'draft')
            ->pluck('weekly_tracker_id');

        return $this->getUnreportedMissionTrackers($staff)
            ->filter(fn (WeeklyTracker $tracker) => ! $draftTrackerIds->contains($tracker->id))
            ->values();
    }

    public function trackerBelongsToStaff(WeeklyTracker $tracker, Staff $staff): bool
    {
        return (int) $tracker->staff_id === (int) $staff->id;
    }

    public function trackerIsReportable(WeeklyTracker $tracker): bool
    {
        return $tracker->status === 'on_mission'
            && $tracker->submission_status === 'submitted'
            && filled($tracker->mission_title)
            && ! $tracker->hasCompletedMissionReport();
    }

    /**
     * Prefill activity report fields from a weekly tracker mission.
     *
     * @return array<string, mixed>
     */
    public function reportFieldsFromTracker(WeeklyTracker $tracker): array
    {
        return [
            'weekly_tracker_id' => $tracker->id,
            'activity_calendar_id' => $tracker->activity_calendar_id,
            'title' => $tracker->mission_title,
            'report_date' => optional($tracker->mission_end_date)->format('Y-m-d') ?? now()->toDateString(),
            'summary' => $tracker->mission_purpose ?? '',
        ];
    }

    /**
     * Activities where the staff member owes a post-activity report.
     */
    public function getPendingReportsForStaff(Staff $staff): Collection
    {
        $participatedIds = $this->getParticipatedActivityIds($staff);

        if ($participatedIds->isEmpty()) {
            return collect();
        }

        return ActivityCalendar::query()
            ->whereIn('id', $participatedIds)
            ->where('status', 'done')
            ->orderByDesc('end_date')
            ->get()
            ->filter(fn (ActivityCalendar $activity) => $this->activityRequiresReport($activity))
            ->filter(fn (ActivityCalendar $activity) => ! $this->staffHasCompletedReport($staff, $activity))
            ->values();
    }

    public function getParticipatedActivityIds(Staff $staff): Collection
    {
        $fromTrackers = WeeklyTracker::query()
            ->where('staff_id', $staff->id)
            ->where('status', 'on_mission')
            ->whereNotNull('activity_calendar_id')
            ->pluck('activity_calendar_id');

        $fromRequests = ActivityRequest::query()
            ->where('requested_by', $staff->id)
            ->where('status', 'approved')
            ->whereNotNull('approved_activity_id')
            ->pluck('approved_activity_id');

        return $fromTrackers->merge($fromRequests)->unique()->values();
    }

    public function staffParticipatedInActivity(Staff $staff, ActivityCalendar $activity): bool
    {
        return $this->getParticipatedActivityIds($staff)->contains($activity->id);
    }

    public function staffHasCompletedReport(Staff $staff, ActivityCalendar $activity): bool
    {
        return ActivityReport::query()
            ->where('staff_id', $staff->id)
            ->where('activity_calendar_id', $activity->id)
            ->whereIn('status', ['submitted', 'reviewed'])
            ->exists();
    }

    public function getReportStatusForStaff(Staff $staff, ActivityCalendar $activity): ?string
    {
        if (! $this->staffParticipatedInActivity($staff, $activity)) {
            return null;
        }

        $report = ActivityReport::query()
            ->where('staff_id', $staff->id)
            ->where('activity_calendar_id', $activity->id)
            ->latest()
            ->first();

        if (! $report) {
            return $activity->status === 'done' && $this->activityRequiresReport($activity)
                ? 'pending'
                : null;
        }

        return $report->status;
    }

    /**
     * Prefill mission fields from a linked calendar activity.
     *
     * @return array<string, mixed>
     */
    public function missionFieldsFromActivity(ActivityCalendar $activity): array
    {
        return [
            'mission_title' => $activity->title,
            'mission_start_date' => $activity->start_date->format('Y-m-d'),
            'mission_end_date' => $activity->end_date->format('Y-m-d'),
            'mission_purpose' => $activity->description ?? '',
        ];
    }
}
