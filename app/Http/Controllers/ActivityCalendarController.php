<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityCalendar;

class ActivityCalendarController extends Controller
{
    /**
     * Display activity calendar for staff (read-only)
     */
    public function index(Request $request)
    {
        $query = ActivityCalendar::with('creator');

        // Apply simple filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $activities = $query->orderBy('start_date', 'desc')->paginate(20);

        // Get filter options for dropdowns
        $filterOptions = [
            'types' => ['meeting', 'training', 'event', 'holiday', 'deadline'],
            'statuses' => ['not_yet_started', 'ongoing', 'done']
        ];

        return view('staff.calendar.index', compact('activities', 'filterOptions'));
    }

    /**
     * API endpoint for calendar events (for FullCalendar.js integration)
     */
    public function apiEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $query = ActivityCalendar::with(['creator']);

        // Apply filters from request (for calendar view filtering)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Date range filters (custom filters take precedence over FullCalendar range)
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        } elseif ($start) {
            // Use FullCalendar's start if no custom date_from filter
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($query) use ($start, $end) {
                      $query->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                  });
            });
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $activities = $query->get();

        $events = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'start' => $activity->start_date->format('Y-m-d'),
                'end' => $activity->end_date->addDay()->format('Y-m-d'), // FullCalendar end date is exclusive
                'description' => $activity->description,
                'location' => $activity->location,
                'type' => $activity->type,
                'type_label' => $activity->type_label,
                'status' => $activity->status,
                'backgroundColor' => $this->getEventColor($activity->type),
                'borderColor' => $this->getEventColor($activity->type),
                'textColor' => $activity->type === 'holiday' ? '#212529' : '#ffffff',
                'extendedProps' => [
                    'type' => $activity->type,
                    'type_label' => $activity->type_label,
                    'status' => $activity->status,
                    'description' => $activity->description,
                    'location' => $activity->location,
                    'creator' => $activity->creator->full_name ?? 'Unknown'
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get event color based on activity type
     */
    private function getEventColor($type)
    {
        return match($type) {
            'meeting' => '#007bff',
            'training' => '#17a2b8',
            'event' => '#28a745',
            'holiday' => '#ffc107',
            'deadline' => '#dc3545',
            default => '#6c757d'
        };
    }
}
