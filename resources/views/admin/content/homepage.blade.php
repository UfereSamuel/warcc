@extends('adminlte::page')

@section('title', 'Homepage Content Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Homepage Content</h1>
            <p class="text-muted">Manage homepage content and settings</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.content.index') }}">Content</a></li>
                <li class="breadcrumb-item active">Homepage</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-home mr-2"></i>
                    Homepage Settings
                </h3>
            </div>
            <div class="card-body">
                @if(auth('staff')->user()?->hasPermission('manage_website'))
                    <div class="alert alert-success">
                        <i class="fas fa-globe mr-2"></i>
                        Full homepage content (hero text, mission sections, core values, and countries) is managed in
                        <a href="{{ route('admin.website-management.index') }}" class="alert-link">Website Management</a>.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Homepage hero slides are the main visual content. Additional homepage sections are managed by super administrators.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-images fa-3x text-primary mb-3"></i>
                                <h5>Hero Slides</h5>
                                <p class="text-muted">Manage homepage banner slides</p>
                                <a href="{{ route('admin.content.hero-slides.index') }}" class="btn btn-primary">
                                    <i class="fas fa-edit mr-1"></i> Manage Slides
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-outline card-secondary">
                            <div class="card-body text-center">
                                <i class="fas fa-globe fa-3x text-secondary mb-3"></i>
                                <h5>View Public Site</h5>
                                <p class="text-muted">Preview your homepage</p>
                                <a href="{{ route('home') }}" class="btn btn-secondary" target="_blank">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Site
                                </a>
                                @if(auth('staff')->user()?->hasPermission('manage_website'))
                                    <a href="{{ route('admin.website-management.index') }}" class="btn btn-outline-secondary mt-2">
                                        <i class="fas fa-cogs mr-1"></i> Website Management
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.content.hero-slides.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus mr-2 text-primary"></i>
                        Add New Hero Slide
                    </a>
                    <a href="{{ route('admin.content.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-arrow-left mr-2 text-secondary"></i>
                        Back to Content Management
                    </a>
                    <a href="{{ route('admin.calendar.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar mr-2 text-success"></i>
                        Manage Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
