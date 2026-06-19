@extends('layouts.public')

@section('title', 'About - Africa CDC Western RCC')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">{{ $aboutContent['hero_title'] }}</h1>
                <p class="lead">{{ $aboutContent['hero_lead'] }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-bullseye fa-2x"></i>
                            </div>
                            <h3 class="fw-bold text-primary">{{ $organization['mission_title'] }}</h3>
                        </div>
                        <p class="text-muted">
                            {{ $organization['mission_text'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <h3 class="fw-bold text-primary">{{ $organization['vision_title'] }}</h3>
                        </div>
                        <p class="text-muted">
                            {{ $organization['vision_text'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Functions Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold text-primary mb-3">{{ $aboutContent['core_functions_title'] }}</h2>
                <p class="lead text-muted">{{ $aboutContent['core_functions_lead'] }}</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($aboutContent['core_functions'] as $function)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="{{ $function['icon'] }} fa-lg"></i>
                        </div>
                        <h5 class="card-title text-primary">{{ $function['title'] }}</h5>
                        <p class="card-text text-muted">{{ $function['text'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Coverage Area Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold text-primary mb-4">{{ $aboutContent['coverage_title'] }}</h2>
                <p class="lead text-muted mb-4">{{ $aboutContent['coverage_lead'] }}</p>

                <div class="row g-3">
                    @foreach($countries as $country)
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <span>{{ $country->name }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-6">
                <div class="text-center">
                    <img src="{{ asset('images/logos/logo.png') }}" alt="Africa CDC Logo" class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
