@extends('layouts.public')

@section('title', 'Activities - Africa CDC Western RCC')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Our Activities</h1>
                <p class="lead">
                    Explore our comprehensive range of health security and capacity building initiatives
                    across West Africa.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h3 class="fw-bold text-primary">{{ $stats['total'] }}</h3>
                        <p class="text-muted mb-0">Total Activities</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <h3 class="fw-bold text-success">{{ $stats['completed'] }}</h3>
                        <p class="text-muted mb-0">Completed</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-play-circle fa-lg"></i>
                        </div>
                        <h3 class="fw-bold text-warning">{{ $stats['ongoing'] }}</h3>
                        <p class="text-muted mb-0">Ongoing</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-calendar-plus fa-lg"></i>
                        </div>
                        <h3 class="fw-bold text-info">{{ $stats['upcoming'] }}</h3>
                        <p class="text-muted mb-0">Upcoming</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Activities Section -->
<section class="py-5">
    <div class="container">
        @if($activities->count() > 0)
            <div class="row g-4">
                @foreach($activities as $activity)
                <div class="col-lg-4 col-md-6">
                    <div class="card activity-card h-100
                        @if($activity->status === 'ongoing') ongoing
                        @elseif($activity->status === 'done') completed
                        @endif">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold">{{ $activity->title }}</h5>
                                <span class="badge
                                    @if($activity->status === 'ongoing') bg-warning text-dark
                                    @elseif($activity->status === 'done') bg-success
                                    @elseif($activity->status === 'upcoming') bg-primary
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </div>

                            <p class="card-text text-muted">{{ Str::limit($activity->description, 150) }}</p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $activity->start_date->format('M d') }} - {{ $activity->end_date->format('M d, Y') }}
                                    </small>
                                </div>

                                @if($activity->location)
                                <div class="d-flex align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $activity->location }}
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $activities->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-calendar-times fa-5x text-muted"></i>
                </div>
                <h3 class="text-muted">No Activities Found</h3>
                <p class="text-muted">There are currently no activities to display.</p>
            </div>
        @endif
    </div>
</section>
@endsection
