@extends('adminlte::page')

@section('title', 'Content Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Content Management</h1>
            <p class="text-muted">Manage website content and media</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Content</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<!-- Content Statistics -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-images"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Hero Slides</span>
                <span class="info-box-number">{{ $heroSlidesCount }}</span>
                <span class="progress-description">
                    {{ $activeHeroSlides }} active
                </span>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Activities</span>
                <span class="info-box-number">{{ $activitiesCount }}</span>
                <span class="progress-description">
                    Scheduled events
                </span>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-globe"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Website</span>
                <span class="info-box-number">Online</span>
                <span class="progress-description">
                    Content published
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Content Management Options -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-images mr-2"></i>
                    Homepage Content
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Manage homepage hero slides and banners</p>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.content.hero-slides.index') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-images mr-1"></i> Hero Slides
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.content.homepage') }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-home mr-1"></i> Homepage Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-2"></i>
                    Events & Activities
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Manage organizational calendar and events</p>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.calendar.index') }}" class="btn btn-success btn-block">
                            <i class="fas fa-calendar mr-1"></i> Calendar
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.calendar.create') }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-plus mr-1"></i> Add Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Page Content
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Manage static page content and information</p>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.content.about') }}" class="btn btn-info btn-block">
                            <i class="fas fa-info-circle mr-1"></i> About Page
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('public.contact') }}" class="btn btn-outline-info btn-block" target="_blank">
                            <i class="fas fa-envelope mr-1"></i> Contact Info
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-2"></i>
                    Site Management
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">General website settings and configuration</p>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-block" target="_blank">
                            <i class="fas fa-external-link-alt mr-1"></i> View Site
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.content.hero-slides.create') }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-plus mr-1"></i> Add Hero Slide
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.calendar.create') }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-calendar-plus mr-1"></i> Add Event
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-info btn-block">
                            <i class="fas fa-users mr-1"></i> Manage Staff
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-chart-bar mr-1"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
