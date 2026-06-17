@extends('layouts.public')

@section('title', $event->title . ' - Africa CDC Western RCC')

@section('styles')
<style>
    .event-hero {
        position: relative;
        height: 400px;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
    }

    .event-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(52, 143, 65, 0.8) 0%, rgba(180, 162, 105, 0.6) 100%);
        z-index: 1;
    }

    .event-hero-content {
        position: relative;
        z-index: 2;
        color: white;
    }

    .event-info-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin-top: -80px;
        position: relative;
        z-index: 3;
    }

    .event-meta {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items-center;
        margin-bottom: 1rem;
    }

    .meta-item:last-child {
        margin-bottom: 0;
    }

    .meta-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-green);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .registration-card {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
    }

    .related-event {
        transition: transform 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .related-event:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .tags-container .badge {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .social-share {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .back-nav {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
<!-- Event Hero -->
<section class="event-hero" style="background-image: url('{{ $event->featured_image_url }}');">
    <div class="container">
        <div class="event-hero-content">
            <div class="row">
                <div class="col-lg-8">
                    <div class="back-nav d-inline-block">
                        <a href="{{ route('public.events') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Events
                        </a>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-{{ $event->category_color }} fs-6 me-2">{{ $event->category_label }}</span>
                        <span class="badge bg-{{ $event->event_status_color }} fs-6">{{ $event->event_status_label }}</span>
                        @if($event->is_featured)
                            <span class="badge bg-warning text-dark fs-6 ms-2">
                                <i class="fas fa-star me-1"></i>Featured
                            </span>
                        @endif
                    </div>
                    <h1 class="display-4 fw-bold mb-3">{{ $event->title }}</h1>
                    @if($event->summary)
                        <p class="lead">{{ $event->summary }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="pb-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="event-info-card">
                    <h3 class="fw-bold text-primary mb-4">About This Event</h3>
                    <div class="mb-4">
                        {!! nl2br(e($event->description)) !!}
                    </div>

                    @if($event->additional_info)
                    <h4 class="fw-bold text-primary mb-3">Additional Information</h4>
                    <div class="mb-4">
                        {!! nl2br(e($event->additional_info)) !!}
                    </div>
                    @endif

                    @if($event->tags && count($event->tags) > 0)
                    <h4 class="fw-bold text-primary mb-3">Topics & Tags</h4>
                    <div class="tags-container mb-4">
                        @foreach($event->tags as $tag)
                            <span class="badge bg-light text-dark border">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif

                    <!-- Social Share -->
                    <div class="social-share">
                        <h5 class="fw-bold mb-3">Share This Event</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-facebook-f me-1"></i>Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($event->title) }}"
                               target="_blank" class="btn btn-info btn-sm text-white">
                                <i class="fab fa-twitter me-1"></i>Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                               target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-linkedin-in me-1"></i>LinkedIn
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                                <i class="fas fa-link me-1"></i>Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Event Details -->
                <div class="event-meta mb-4">
                    <h4 class="fw-bold text-primary mb-4">Event Details</h4>

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div>
                            <strong>Date</strong><br>
                            <span class="text-muted">{{ $event->formatted_date_range }}</span>
                            @if($event->formatted_time_range)
                                <br><small class="text-muted">{{ $event->formatted_time_range }}</small>
                            @endif
                        </div>
                    </div>

                    @if($event->location)
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <strong>Location</strong><br>
                            <span class="text-muted">{{ $event->location }}</span>
                            @if($event->venue_address)
                                <br><small class="text-muted">{{ $event->venue_address }}</small>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($event->registration_required)
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <strong>Registration</strong><br>
                            <span class="text-{{ $event->can_register ? 'success' : 'warning' }}">
                                {{ $event->registration_status }}
                            </span>
                            @if($event->registration_deadline)
                                <br><small class="text-muted">Deadline: {{ $event->registration_deadline->format('M d, Y') }}</small>
                            @endif
                            @if($event->max_participants)
                                <br><small class="text-muted">
                                    {{ $event->current_registrations }}/{{ $event->max_participants }} registered
                                </small>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div>
                            <strong>Cost</strong><br>
                            <span class="text-muted">{{ $event->formatted_fee }}</span>
                        </div>
                    </div>

                    @if($event->contact_email || $event->contact_phone)
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <strong>Contact</strong><br>
                            @if($event->contact_email)
                                <a href="mailto:{{ $event->contact_email }}" class="text-primary text-decoration-none">
                                    {{ $event->contact_email }}
                                </a><br>
                            @endif
                            @if($event->contact_phone)
                                <a href="tel:{{ $event->contact_phone }}" class="text-primary text-decoration-none">
                                    {{ $event->contact_phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Registration Card -->
                @if($event->registration_required && $event->can_register && $event->registration_link)
                <div class="registration-card mb-4">
                    <h4 class="fw-bold mb-3">Ready to Join?</h4>
                    <p class="mb-4">
                        Register now to secure your spot at this event and be part of strengthening
                        health security across West Africa.
                    </p>
                    <a href="{{ $event->registration_link }}" target="_blank" class="btn btn-light btn-lg w-100">
                        <i class="fas fa-external-link-alt me-2"></i>Register Now
                    </a>
                    @if($event->fee)
                        <div class="text-center mt-3">
                            <small>Registration Fee: {{ $event->formatted_fee }}</small>
                        </div>
                    @endif
                </div>
                @elseif($event->registration_required && !$event->can_register)
                <div class="card border-warning mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h5 class="card-title">Registration Closed</h5>
                        <p class="card-text text-muted">
                            {{ $event->registration_status }}
                        </p>
                        @if($event->contact_email)
                            <a href="mailto:{{ $event->contact_email }}" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-1"></i>Contact Organizer
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="addToCalendar()">
                                <i class="fas fa-calendar-plus me-2"></i>Add to Calendar
                            </button>
                            @if($event->venue_address)
                            <a href="https://www.google.com/maps/search/{{ urlencode($event->venue_address) }}"
                               target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-map me-2"></i>View on Map
                            </a>
                            @endif
                            <a href="{{ route('public.events') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Browse All Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Events -->
@if($relatedEvents->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold text-primary mb-4">Related Events</h3>
                <div class="row g-4">
                    @foreach($relatedEvents as $relatedEvent)
                    <div class="col-lg-4 col-md-6">
                        <div class="card related-event h-100">
                            @if($relatedEvent->featured_image)
                                <img src="{{ $relatedEvent->featured_image_url }}" class="card-img-top"
                                     alt="{{ $relatedEvent->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-primary d-flex align-items-center justify-content-center"
                                     style="height: 200px;">
                                    <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-{{ $relatedEvent->category_color }}">{{ $relatedEvent->category_label }}</span>
                                </div>
                                <h5 class="card-title">{{ $relatedEvent->title }}</h5>
                                <p class="card-text text-muted flex-grow-1">
                                    {{ $relatedEvent->summary ?: Str::limit($relatedEvent->description, 120) }}
                                </p>
                                <div class="mt-auto">
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $relatedEvent->formatted_date_range }}
                                        </small>
                                    </div>
                                    <a href="{{ route('public.events.show', $relatedEvent) }}" class="btn btn-primary btn-sm w-100">
                                        Learn More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@section('scripts')
<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Event link copied to clipboard!');
    });
}

function addToCalendar() {
    const title = '{{ addslashes($event->title) }}';
    const startDate = '{{ $event->start_date->format('Ymd') }}{{ $event->start_time ? $event->start_time->format('His') : '000000' }}';
    const endDate = '{{ $event->end_date->format('Ymd') }}{{ $event->end_time ? $event->end_time->format('His') : '235959' }}';
    const description = '{{ addslashes(strip_tags($event->description)) }}';
    const location = '{{ addslashes($event->location ?: '') }}';

    const googleCalendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startDate}/${endDate}&details=${encodeURIComponent(description)}&location=${encodeURIComponent(location)}`;

    window.open(googleCalendarUrl, '_blank');
}
</script>
@endsection
