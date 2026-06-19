@extends('adminlte::page')

@section('title', 'Public Event Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Public Event Details</h1>
            <p class="text-muted">{{ $publicEvent->title }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.public-events.index') }}">Public Events</a></li>
                <li class="breadcrumb-item active">Event Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.public-events.edit', $publicEvent) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Edit Event
            </a>
            <a href="{{ route('admin.public-events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Events
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ $publicEvent->title }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $publicEvent->status_color }}">{{ $publicEvent->status_label }}</span>
                        @if($publicEvent->is_featured)
                            <span class="badge badge-warning ml-1"><i class="fas fa-star"></i> Featured</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($publicEvent->featured_image)
                        <div class="mb-4 text-center">
                            <img src="{{ $publicEvent->featured_image_url }}" alt="{{ $publicEvent->title }}"
                                 class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    @endif

                    @if($publicEvent->summary)
                        <div class="alert alert-light border">
                            <strong>Summary:</strong> {{ $publicEvent->summary }}
                        </div>
                    @endif

                    <h5>Description</h5>
                    <div class="mb-4">{!! nl2br(e($publicEvent->description)) !!}</div>

                    @if($publicEvent->additional_info)
                        <h5>Additional Information</h5>
                        <div class="mb-4">{!! nl2br(e($publicEvent->additional_info)) !!}</div>
                    @endif

                    @if($publicEvent->tags)
                        <h5>Tags</h5>
                        <p>
                            @foreach($publicEvent->tags as $tag)
                                <span class="badge badge-secondary mr-1">{{ $tag }}</span>
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i> Event Info</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Category</dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-{{ $publicEvent->category_color }}">{{ $publicEvent->category_label }}</span>
                        </dd>

                        <dt class="col-sm-5">Event Status</dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-{{ $publicEvent->event_status_color }}">{{ $publicEvent->event_status_label }}</span>
                        </dd>

                        <dt class="col-sm-5">Date</dt>
                        <dd class="col-sm-7">{{ $publicEvent->formatted_date_range }}</dd>

                        @if($publicEvent->formatted_time_range)
                            <dt class="col-sm-5">Time</dt>
                            <dd class="col-sm-7">{{ $publicEvent->formatted_time_range }}</dd>
                        @endif

                        @if($publicEvent->location)
                            <dt class="col-sm-5">Location</dt>
                            <dd class="col-sm-7">{{ $publicEvent->location }}</dd>
                        @endif

                        @if($publicEvent->venue_address)
                            <dt class="col-sm-5">Address</dt>
                            <dd class="col-sm-7">{{ $publicEvent->venue_address }}</dd>
                        @endif

                        <dt class="col-sm-5">Registration</dt>
                        <dd class="col-sm-7">{{ $publicEvent->registration_status }}</dd>

                        @if($publicEvent->registration_required)
                            <dt class="col-sm-5">Fee</dt>
                            <dd class="col-sm-7">{{ $publicEvent->formatted_fee }}</dd>

                            @if($publicEvent->max_participants)
                                <dt class="col-sm-5">Capacity</dt>
                                <dd class="col-sm-7">{{ $publicEvent->current_registrations }} / {{ $publicEvent->max_participants }}</dd>
                            @endif

                            @if($publicEvent->registration_deadline)
                                <dt class="col-sm-5">Deadline</dt>
                                <dd class="col-sm-7">{{ $publicEvent->registration_deadline->format('M d, Y') }}</dd>
                            @endif

                            @if($publicEvent->registration_link)
                                <dt class="col-sm-5">Register</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ $publicEvent->registration_link }}" target="_blank">Registration Link</a>
                                </dd>
                            @endif
                        @endif

                        @if($publicEvent->contact_email)
                            <dt class="col-sm-5">Email</dt>
                            <dd class="col-sm-7"><a href="mailto:{{ $publicEvent->contact_email }}">{{ $publicEvent->contact_email }}</a></dd>
                        @endif

                        @if($publicEvent->contact_phone)
                            <dt class="col-sm-5">Phone</dt>
                            <dd class="col-sm-7">{{ $publicEvent->contact_phone }}</dd>
                        @endif

                        <dt class="col-sm-5">Created</dt>
                        <dd class="col-sm-7">
                            {{ $publicEvent->created_at->format('M d, Y') }}
                            @if($publicEvent->creator)
                                <br><small class="text-muted">by {{ $publicEvent->creator->full_name }}</small>
                            @endif
                        </dd>

                        @if($publicEvent->published_at)
                            <dt class="col-sm-5">Published</dt>
                            <dd class="col-sm-7">{{ $publicEvent->published_at->format('M d, Y g:i A') }}</dd>
                        @endif

                        @if($publicEvent->updater)
                            <dt class="col-sm-5">Last Updated</dt>
                            <dd class="col-sm-7">
                                {{ $publicEvent->updated_at->format('M d, Y') }}
                                <br><small class="text-muted">by {{ $publicEvent->updater->full_name }}</small>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .badge-purple {
        background-color: #6f42c1;
        color: #fff;
    }
</style>
@stop
