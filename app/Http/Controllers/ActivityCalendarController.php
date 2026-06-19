<?php

namespace App\Http\Controllers;

use App\Models\ActivityCalendar;
use App\Models\Staff;
use App\Services\ActivityCalendarIcsService;
use App\Services\ActivityWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityCalendarController extends Controller
{
    public function __construct(
        private readonly ActivityWorkflowService $workflow,
        private readonly ActivityCalendarIcsService $ics
    ) {}

    /**
     * Display activity calendar for staff (read-only)
     */
    public function index(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $staff->ensureCalendarFeedToken();
        $query = ActivityCalendar::with('creator');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $activities = $query->orderBy('start_date', 'desc')->paginate(20);

        $reportStatuses = [];
        foreach ($activities as $activity) {
            $reportStatuses[$activity->id] = $this->workflow->getReportStatusForStaff($staff, $activity);
        }

        $pendingReports = $this->workflow->getPendingReportsForStaff($staff);

        $filterOptions = [
            'types' => ['meeting', 'training', 'event', 'mission', 'workshop', 'holiday', 'deadline'],
            'statuses' => ['not_yet_started', 'ongoing', 'done'],
        ];

        return view('staff.calendar.index', compact('activities', 'filterOptions', 'reportStatuses', 'pendingReports', 'staff'));
    }

    /**
     * Public ICS feed for Outlook / Google Calendar subscription.
     */
    public function icsFeed(string $token)
    {
        $staff = Staff::query()
            ->where('calendar_feed_token', $token)
            ->where('status', 'active')
            ->first();

        if (! $staff) {
            abort(404);
        }

        $activities = ActivityCalendar::query()
            ->orderBy('start_date')
            ->get();

        $ics = $this->ics->generate($activities);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="warcc-activities.ics"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /**
     * Rotate the staff member's personal calendar feed token.
     */
    public function regenerateFeedToken(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $staff->regenerateCalendarFeedToken();

        return redirect()
            ->route('staff.calendar.index')
            ->with('success', 'Calendar subscription link regenerated. Update Outlook or Google with the new URL.');
    }

    /**
     * API endpoint for calendar events (for FullCalendar.js integration)
     */
    public function apiEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $activities = ActivityCalendar::query()
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($inner) use ($start, $end) {
                            $inner->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                });
            })
            ->get();

        $events = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'start' => $activity->start_date->format('Y-m-d'),
                'end' => $activity->end_date->copy()->addDay()->format('Y-m-d'),
                'backgroundColor' => $this->eventColor($activity->type),
                'borderColor' => $this->eventColor($activity->type),
                'extendedProps' => [
                    'type' => $activity->type_label,
                    'status' => $activity->status,
                    'location' => $activity->location,
                ],
            ];
        });

        return response()->json($events);
    }

    private function eventColor(string $type): string
    {
        return match ($type) {
            'meeting' => '#007bff',
            'training' => '#17a2b8',
            'event' => '#28a745',
            'mission' => '#6f42c1',
            'workshop' => '#20c997',
            'holiday' => '#ffc107',
            'deadline' => '#dc3545',
            default => '#6c757d',
        };
    }
}
