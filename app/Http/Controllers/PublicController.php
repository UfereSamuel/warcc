<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityCalendar;
use App\Models\HeroSlide;
use App\Models\PublicEvent;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    /**
     * Show the public landing page
     */
    public function index()
    {
        // Get active hero slides for the slider
        $heroSlides = HeroSlide::active()
            ->ordered()
            ->get();

        // Get featured events for homepage
        $featuredEvents = PublicEvent::published()
            ->featured()
            ->upcoming()
            ->orderByDate()
            ->take(3)
            ->get();

        return view('public.index', compact('heroSlides', 'featuredEvents'));
    }

    /**
     * Show the about page
     */
    public function about()
    {
        return view('public.about');
    }

    /**
     * Show the activities page
     */
    public function activities()
    {
        $activities = ActivityCalendar::orderBy('start_date', 'desc')
            ->paginate(12);

        $stats = [
            'total' => ActivityCalendar::count(),
            'completed' => ActivityCalendar::completed()->count(),
            'ongoing' => ActivityCalendar::ongoing()->count(),
            'upcoming' => ActivityCalendar::upcoming()->count(),
        ];

        return view('public.activities', compact('activities', 'stats'));
    }

    /**
     * Show the events page
     */
    public function events(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');

        $query = PublicEvent::published()->orderByDate();

        // Filter by category
        if ($category) {
            $query->byCategory($category);
        }

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(12);

        // Get featured events
        $featuredEvents = PublicEvent::published()
            ->featured()
            ->upcoming()
            ->orderByDate()
            ->take(3)
            ->get();

        // Get event statistics
        $stats = [
            'total' => PublicEvent::published()->count(),
            'upcoming' => PublicEvent::published()->upcoming()->count(),
            'ongoing' => PublicEvent::published()->ongoing()->count(),
            'featured' => PublicEvent::published()->featured()->count(),
        ];

        // Get categories for filter
        $categories = [
            'conference' => 'Conferences',
            'workshop' => 'Workshops',
            'training' => 'Training',
            'seminar' => 'Seminars',
            'meeting' => 'Meetings',
            'announcement' => 'Announcements',
            'celebration' => 'Celebrations'
        ];

        return view('public.events', compact('events', 'featuredEvents', 'stats', 'categories', 'category', 'search'));
    }

    /**
     * Show individual event details
     */
    public function eventShow(PublicEvent $event)
    {
        // Only show published events to public
        if ($event->status !== 'published') {
            abort(404);
        }

        // Get related events (same category, exclude current)
        $relatedEvents = PublicEvent::published()
            ->byCategory($event->category)
            ->where('id', '!=', $event->id)
            ->upcoming()
            ->orderByDate()
            ->take(3)
            ->get();

        return view('public.event-detail', compact('event', 'relatedEvents'));
    }

    /**
     * Get events data for calendar/API
     */
    public function eventsApi(Request $request)
    {
        $events = PublicEvent::published()->get();

        $eventData = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->format('Y-m-d'),
                'end' => $event->end_date->addDay()->format('Y-m-d'), // FullCalendar end date is exclusive
                'url' => route('public.events.show', $event),
                'description' => $event->summary ?? Str::limit($event->description, 100),
                'location' => $event->location,
                'category' => $event->category,
                'backgroundColor' => $this->getEventColor($event->category),
                'borderColor' => $this->getEventColor($event->category),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'category' => $event->category,
                    'category_label' => $event->category_label,
                    'status' => $event->event_status,
                    'featured' => $event->is_featured,
                    'registration_required' => $event->registration_required,
                    'fee' => $event->formatted_fee,
                ]
            ];
        });

        return response()->json($eventData);
    }

    /**
     * Show the contact page
     */
    public function contact()
    {
        return view('public.contact');
    }

    /**
     * Show the media/videos page
     */
    public function media()
    {
        // Check if YouTube integration is enabled
        $youtubeEnabled = setting('youtube_embed_channel', '0') === '1';
        $channelUrl = setting('youtube_channel_url');
        $channelId = setting('youtube_channel_id');
        
        return view('public.media', compact('youtubeEnabled', 'channelUrl', 'channelId'));
    }

    /**
     * Get event color based on category
     */
    private function getEventColor($category)
    {
        return match($category) {
            'conference' => '#007bff',
            'workshop' => '#17a2b8',
            'training' => '#28a745',
            'seminar' => '#ffc107',
            'meeting' => '#6c757d',
            'announcement' => '#dc3545',
            'celebration' => '#6f42c1',
            default => '#6c757d'
        };
    }
}
