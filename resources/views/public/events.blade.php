@extends('layouts.public')

@section('title', 'Events - Africa CDC Western RCC')

@section('styles')
<style>
    .events-hero {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
        color: white;
        padding: 100px 0 80px;
    }

    .event-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .event-image {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .event-placeholder {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-sidebar {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .search-form {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .featured-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        color: #333;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    .event-status {
        border-radius: 20px;
        font-size: 0.75rem;
        padding: 4px 12px;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-left: 4px solid var(--primary-green);
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="events-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Events & Programs</h1>
                <p class="lead mb-4">
                    Discover upcoming conferences, workshops, training programs, and health initiatives
                    across West Africa. Join us in strengthening regional health security through
                    collaborative learning and knowledge exchange.
                </p>
            </div>
            <div class="col-lg-4 text-center">
                <div class="d-flex justify-content-center gap-4">
                    <div class="text-center">
                        <div class="h2 fw-bold">{{ $stats['total'] }}</div>
                        <small>Total Events</small>
                    </div>
                    <div class="text-center">
                        <div class="h2 fw-bold">{{ $stats['upcoming'] }}</div>
                        <small>Upcoming</small>
                    </div>
                    <div class="text-center">
                        <div class="h2 fw-bold">{{ $stats['featured'] }}</div>
                        <small>Featured</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filters -->
        <div class="row mb-5">
            <div class="col-12">
                <form method="GET" action="{{ route('public.events') }}" class="search-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Events</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ $search }}" placeholder="Search by title, description, location...">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                            @if($search || $category)
                                <a href="{{ route('public.events') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            @endif
                        </div>
                        <div class="col-md-2 text-end">
                            <small class="text-muted">{{ $events->total() }} events found</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Featured Events -->
        @if($featuredEvents->count() > 0 && !$search && !$category)
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="fw-bold text-primary mb-4">
                    <i class="fas fa-star me-2"></i>Featured Events
                </h3>
                <div class="row g-4">
                    @foreach($featuredEvents as $event)
                    <div class="col-lg-4 col-md-6">
                        <div class="card event-card h-100 position-relative">
                            <div class="featured-badge">
                                <i class="fas fa-star me-1"></i>Featured
                            </div>
                            @if($event->featured_image)
                                <img src="{{ $event->featured_image_url }}" class="event-image" alt="{{ $event->title }}">
                            @else
                                <div class="event-placeholder">
                                    <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-{{ $event->category_color }} event-status">{{ $event->category_label }}</span>
                                    <span class="badge bg-{{ $event->event_status_color }} event-status">{{ $event->event_status_label }}</span>
                                </div>
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted flex-grow-1">
                                    {{ $event->summary ?: Str::limit($event->description, 120) }}
                                </p>
                                <div class="mt-auto">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center text-muted mb-1">
                                            <i class="fas fa-calendar me-2"></i>
                                            <small>{{ $event->formatted_date_range }}</small>
                                        </div>
                                        @if($event->location)
                                        <div class="d-flex align-items-center text-muted mb-1">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <small>{{ $event->location }}</small>
                                        </div>
                                        @endif
                                        @if($event->registration_required)
                                        <div class="d-flex align-items-center {{ $event->can_register ? 'text-success' : 'text-warning' }}">
                                            <i class="fas fa-{{ $event->can_register ? 'check' : 'clock' }} me-2"></i>
                                            <small>{{ $event->registration_status }}</small>
                                        </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('public.events.show', $event) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-info-circle me-1"></i>Learn More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr class="my-5">
            </div>
        </div>
        @endif

        <!-- All Events -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-primary">
                        @if($search || $category)
                            Search Results
                        @else
                            All Events
                        @endif
                    </h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm active" onclick="switchView('grid')">
                            <i class="fas fa-th"></i> Grid
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="switchView('list')">
                            <i class="fas fa-list"></i> List
                        </button>
                    </div>
                </div>

                @if($events->count() > 0)
                    <div id="events-grid" class="row g-4">
                        @foreach($events as $event)
                        <div class="col-lg-4 col-md-6">
                            <div class="card event-card h-100 {{ $event->is_featured ? 'position-relative' : '' }}">
                                @if($event->is_featured)
                                <div class="featured-badge">
                                    <i class="fas fa-star me-1"></i>Featured
                                </div>
                                @endif
                                @if($event->featured_image)
                                    <img src="{{ $event->featured_image_url }}" class="event-image" alt="{{ $event->title }}">
                                @else
                                    <div class="event-placeholder">
                                        <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $event->category_color }} event-status">{{ $event->category_label }}</span>
                                        <span class="badge bg-{{ $event->event_status_color }} event-status">{{ $event->event_status_label }}</span>
                                    </div>
                                    <h5 class="card-title">{{ $event->title }}</h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        {{ $event->summary ?: Str::limit($event->description, 120) }}
                                    </p>
                                    <div class="mt-auto">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center text-muted mb-1">
                                                <i class="fas fa-calendar me-2"></i>
                                                <small>{{ $event->formatted_date_range }}</small>
                                            </div>
                                            @if($event->location)
                                            <div class="d-flex align-items-center text-muted mb-1">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                <small>{{ $event->location }}</small>
                                            </div>
                                            @endif
                                            @if($event->fee)
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="fas fa-tag me-2"></i>
                                                <small>{{ $event->formatted_fee }}</small>
                                            </div>
                                            @else
                                            <div class="d-flex align-items-center text-success">
                                                <i class="fas fa-check me-2"></i>
                                                <small>Free Event</small>
                                            </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('public.events.show', $event) }}" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-info-circle me-1"></i>Learn More
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div id="events-list" class="d-none">
                        @foreach($events as $event)
                        <div class="card event-card mb-3">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    @if($event->featured_image)
                                        <img src="{{ $event->featured_image_url }}" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="{{ $event->title }}">
                                    @else
                                        <div class="h-100 bg-primary d-flex align-items-center justify-content-center">
                                            <i class="fas fa-calendar-alt fa-2x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-{{ $event->category_color }} event-status me-1">{{ $event->category_label }}</span>
                                                <span class="badge bg-{{ $event->event_status_color }} event-status">{{ $event->event_status_label }}</span>
                                                @if($event->is_featured)
                                                    <span class="badge bg-warning text-dark event-status">
                                                        <i class="fas fa-star me-1"></i>Featured
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $event->formatted_date_range }}</small>
                                        </div>
                                        <h5 class="card-title">{{ $event->title }}</h5>
                                        <p class="card-text">{{ $event->summary ?: Str::limit($event->description, 200) }}</p>
                                        <div class="row">
                                            <div class="col-md-8">
                                                @if($event->location)
                                                <div class="d-flex align-items-center text-muted mb-1">
                                                    <i class="fas fa-map-marker-alt me-2"></i>
                                                    <small>{{ $event->location }}</small>
                                                </div>
                                                @endif
                                                @if($event->fee)
                                                <div class="d-flex align-items-center text-muted">
                                                    <i class="fas fa-tag me-2"></i>
                                                    <small>{{ $event->formatted_fee }}</small>
                                                </div>
                                                @else
                                                <div class="d-flex align-items-center text-success">
                                                    <i class="fas fa-check me-2"></i>
                                                    <small>Free Event</small>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <a href="{{ route('public.events.show', $event) }}" class="btn btn-primary">
                                                    <i class="fas fa-info-circle me-1"></i>Learn More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($events->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $events->appends(request()->query())->links() }}
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h4>No Events Found</h4>
                        @if($search || $category)
                            <p class="text-muted">Try adjusting your search criteria or browse all events.</p>
                            <a href="{{ route('public.events') }}" class="btn btn-primary">
                                <i class="fas fa-list me-1"></i>View All Events
                            </a>
                        @else
                            <p class="text-muted">No events are currently available. Check back soon for upcoming programs and initiatives.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
function switchView(view) {
    const gridView = document.getElementById('events-grid');
    const listView = document.getElementById('events-list');
    const gridBtn = document.querySelector('button[onclick="switchView(\'grid\')"]');
    const listBtn = document.querySelector('button[onclick="switchView(\'list\')"]');

    if (view === 'grid') {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    }
}
</script>
@endsection
