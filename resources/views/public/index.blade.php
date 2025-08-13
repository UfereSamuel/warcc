@extends('layouts.public')

@section('title', 'Home - Africa CDC Western RCC')

@section('styles')
<style>
    .hero-carousel {
        height: 600px;
    }

    .hero-carousel .carousel-item {
        height: 600px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .hero-carousel .carousel-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(52, 143, 65, 0.8) 0%, rgba(180, 162, 105, 0.6) 100%);
        z-index: 1;
    }

    .hero-carousel .carousel-caption {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        text-align: center;
        width: 90%;
        max-width: 800px;
    }

    .hero-carousel .carousel-control-prev,
    .hero-carousel .carousel-control-next {
        z-index: 3;
    }

    .hero-carousel .carousel-indicators {
        z-index: 3;
    }

    .default-hero {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
        color: white;
        padding: 150px 0;
        min-height: 600px;
        display: flex;
        align-items: center;
    }
</style>
@endsection

@section('content')
<!-- Hero Slider Section -->
@if($heroSlides->count() > 0)
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
    <!-- Indicators -->
    <div class="carousel-indicators">
        @foreach($heroSlides as $index => $slide)
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                @if($index === 0) class="active" aria-current="true" @endif
                aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
    </div>

    <!-- Slides -->
    <div class="carousel-inner">
        @foreach($heroSlides as $index => $slide)
        <div class="carousel-item @if($index === 0) active @endif"
             style="background-image: url('{{ $slide->image_url }}');">
            <div class="carousel-caption">
                <div class="container">
                    @if($slide->subtitle)
                    <p class="lead mb-2 text-white-50">{{ $slide->subtitle }}</p>
                    @endif

                    <h1 class="display-4 fw-bold mb-4 text-white">{{ $slide->title }}</h1>

                    @if($slide->description)
                    <p class="lead mb-4 text-white">{{ $slide->description }}</p>
                    @endif

                    @if($slide->has_button)
                    <div class="mt-4">
                        <a href="{{ $slide->button_link }}" class="btn btn-light btn-lg">
                            {{ $slide->button_text }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
@else
<!-- Default Hero Section (when no slides are configured) -->
<section class="default-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Africa CDC Western RCC</h1>
                <p class="lead mb-4">
                    Strengthening health security and disease surveillance across West Africa through
                    collaborative partnerships, capacity building, and innovative health solutions.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('public.about') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Learn More
                    </a>
                    <a href="{{ route('public.contact') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone me-2"></i>Contact Us
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="{{ asset('images/logos/logo.png') }}" alt="Africa CDC Logo" class="img-fluid" style="max-height: 300px;">
            </div>
        </div>
    </div>
</section>
@endif

<!-- Mission Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold text-primary mb-3">Our Mission</h2>
                <p class="lead text-muted">
                    Coordinating regional health initiatives and supporting member states in building
                    resilient health systems for better health outcomes across West Africa.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-shield-virus fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">Disease Surveillance</h5>
                        <p class="card-text text-muted">
                            Advanced monitoring and early warning systems for disease outbreaks and health emergencies.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">Capacity Building</h5>
                        <p class="card-text text-muted">
                            Training and development programs to strengthen health workforce capabilities across the region.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-network-wired fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">Regional Coordination</h5>
                        <p class="card-text text-muted">
                            Facilitating collaboration and coordination among West African health systems and institutions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- West African Countries Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold text-primary mb-3">Serving West Africa</h2>
                <p class="lead text-muted">
                    The Western RCC serves 15 countries across West Africa, working together to strengthen
                    regional health security and build resilient health systems.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Benin -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/bj.png" alt="Benin Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Benin</h6>
                        <small class="text-muted">Republic of Benin</small>
                    </div>
                </div>
            </div>

            <!-- Burkina Faso -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/bf.png" alt="Burkina Faso Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Burkina Faso</h6>
                        <small class="text-muted">Burkina Faso</small>
                    </div>
                </div>
            </div>

            <!-- Cape Verde -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/cv.png" alt="Cape Verde Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Cape Verde</h6>
                        <small class="text-muted">Republic of Cabo Verde</small>
                    </div>
                </div>
            </div>

            <!-- Côte d'Ivoire -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/ci.png" alt="Côte d'Ivoire Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Côte d'Ivoire</h6>
                        <small class="text-muted">Republic of Côte d'Ivoire</small>
                    </div>
                </div>
            </div>

            <!-- Gambia -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/gm.png" alt="Gambia Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Gambia</h6>
                        <small class="text-muted">Republic of The Gambia</small>
                    </div>
                </div>
            </div>

            <!-- Ghana -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/gh.png" alt="Ghana Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Ghana</h6>
                        <small class="text-muted">Republic of Ghana</small>
                    </div>
                </div>
            </div>

            <!-- Guinea -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/gn.png" alt="Guinea Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Guinea</h6>
                        <small class="text-muted">Republic of Guinea</small>
                    </div>
                </div>
            </div>

            <!-- Guinea-Bissau -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/gw.png" alt="Guinea-Bissau Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Guinea-Bissau</h6>
                        <small class="text-muted">Republic of Guinea-Bissau</small>
                    </div>
                </div>
            </div>

            <!-- Liberia -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/lr.png" alt="Liberia Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Liberia</h6>
                        <small class="text-muted">Republic of Liberia</small>
                    </div>
                </div>
            </div>

            <!-- Mali -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/ml.png" alt="Mali Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Mali</h6>
                        <small class="text-muted">Republic of Mali</small>
                    </div>
                </div>
            </div>

            <!-- Mauritania -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/mr.png" alt="Mauritania Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Mauritania</h6>
                        <small class="text-muted">Islamic Republic of Mauritania</small>
                    </div>
                </div>
            </div>

            <!-- Niger -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/ne.png" alt="Niger Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Niger</h6>
                        <small class="text-muted">Republic of Niger</small>
                    </div>
                </div>
            </div>

            <!-- Nigeria -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/ng.png" alt="Nigeria Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Nigeria</h6>
                        <small class="text-muted">Federal Republic of Nigeria</small>
                    </div>
                </div>
            </div>

            <!-- Senegal -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/sn.png" alt="Senegal Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Senegal</h6>
                        <small class="text-muted">Republic of Senegal</small>
                    </div>
                </div>
            </div>

            <!-- Sierra Leone -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card text-center h-100 p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://flagcdn.com/w80/sl.png" alt="Sierra Leone Flag" class="img-fluid" style="height: 40px;">
                        </div>
                        <h6 class="fw-bold text-primary">Sierra Leone</h6>
                        <small class="text-muted">Republic of Sierra Leone</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Events Section -->
@if($featuredEvents->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold text-primary mb-3">Featured Events</h2>
                <p class="lead text-muted">
                    Don't miss these upcoming events and opportunities to strengthen health security across West Africa
                </p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($featuredEvents as $event)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    @if($event->featured_image)
                    <img src="{{ $event->featured_image_url }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-calendar-alt fa-3x text-white"></i>
                    </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-{{ $event->category_color }} text-white">{{ $event->category_label }}</span>
                            @if($event->registration_required)
                                <span class="badge bg-info text-white">
                                    <i class="fas fa-user-plus me-1"></i>Registration Required
                                </span>
                            @endif
                        </div>
                        <h5 class="card-title text-primary">{{ $event->title }}</h5>
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

        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ route('public.events') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-calendar me-2"></i>View All Events
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Core Values Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold text-primary mb-3">Our Core Values</h2>
                <p class="lead text-muted">Guiding principles that drive our commitment to health security in West Africa</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-handshake fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">Collaboration</h5>
                        <p class="card-text text-muted">
                            Working together across borders to achieve common health security goals.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-lightbulb fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">Innovation</h5>
                        <p class="card-text text-muted">
                            Embracing cutting-edge solutions and technologies for better health outcomes.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-heart fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">Excellence</h5>
                        <p class="card-text text-muted">
                            Maintaining the highest standards in all our health security initiatives.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
