@extends('adminlte::page')

@section('title', 'About Page Content')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">About Page Content</h1>
            <p class="text-muted">Manage the public about page</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.content.index') }}">Content</a></li>
                <li class="breadcrumb-item active">About Page</li>
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
            <a href="{{ route('public.about') }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-external-link-alt mr-1"></i> View Public About Page
            </a>
        </div>
    </div>

    <div class="card card-outline card-info mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-eye mr-2"></i> Vision &amp; Mission</h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                Vision and mission statements are shared across the website and edited in Website Management.
            </p>
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $organization['mission_title'] }}</h5>
                    <p class="text-muted">{{ Str::limit($organization['mission_text'], 200) }}</p>
                </div>
                <div class="col-md-6">
                    <h5>{{ $organization['vision_title'] }}</h5>
                    <p class="text-muted">{{ Str::limit($organization['vision_text'], 200) }}</p>
                </div>
            </div>
            @if(auth('staff')->user()?->hasPermission('manage_website'))
                <a href="{{ route('admin.website-management.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-edit mr-1"></i> Edit in Website Management
                </a>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.content.about.update') }}">
        @csrf
        @method('PUT')

        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-heading mr-2"></i> Hero Section</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="hero_title">Page Title</label>
                    <input type="text" class="form-control @error('hero_title') is-invalid @enderror"
                           id="hero_title" name="hero_title"
                           value="{{ old('hero_title', $content['hero_title']) }}" required>
                    @error('hero_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-0">
                    <label for="hero_lead">Intro Text</label>
                    <textarea class="form-control @error('hero_lead') is-invalid @enderror"
                              id="hero_lead" name="hero_lead" rows="3" required>{{ old('hero_lead', $content['hero_lead']) }}</textarea>
                    @error('hero_lead')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-th-large mr-2"></i> Core Functions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="core_functions_title">Section Title</label>
                            <input type="text" class="form-control @error('core_functions_title') is-invalid @enderror"
                                   id="core_functions_title" name="core_functions_title"
                                   value="{{ old('core_functions_title', $content['core_functions_title']) }}" required>
                            @error('core_functions_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="core_functions_lead">Section Subtitle</label>
                            <input type="text" class="form-control @error('core_functions_lead') is-invalid @enderror"
                                   id="core_functions_lead" name="core_functions_lead"
                                   value="{{ old('core_functions_lead', $content['core_functions_lead']) }}" required>
                            @error('core_functions_lead')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @foreach($content['core_functions'] as $index => $function)
                    @php $num = $index + 1; @endphp
                    <div class="border rounded p-3 mb-3">
                        <h5 class="text-muted mb-3">
                            <i class="{{ $function['icon'] }} mr-2"></i> Function {{ $num }}
                        </h5>
                        <div class="form-group">
                            <label for="function_{{ $num }}_title">Title</label>
                            <input type="text" class="form-control @error("function_{$num}_title") is-invalid @enderror"
                                   id="function_{{ $num }}_title" name="function_{{ $num }}_title"
                                   value="{{ old("function_{$num}_title", $function['title']) }}" required>
                            @error("function_{$num}_title")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label for="function_{{ $num }}_text">Description</label>
                            <textarea class="form-control @error("function_{$num}_text") is-invalid @enderror"
                                      id="function_{{ $num }}_text" name="function_{{ $num }}_text"
                                      rows="2" required>{{ old("function_{$num}_text", $function['text']) }}</textarea>
                            @error("function_{$num}_text")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card card-outline card-success mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt mr-2"></i> Coverage Area</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="coverage_title">Section Title</label>
                    <input type="text" class="form-control @error('coverage_title') is-invalid @enderror"
                           id="coverage_title" name="coverage_title"
                           value="{{ old('coverage_title', $content['coverage_title']) }}" required>
                    @error('coverage_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-0">
                    <label for="coverage_lead">Description</label>
                    <textarea class="form-control @error('coverage_lead') is-invalid @enderror"
                              id="coverage_lead" name="coverage_lead" rows="3" required>{{ old('coverage_lead', $content['coverage_lead']) }}</textarea>
                    <small class="form-text text-muted">Use <code>{count}</code> as a placeholder for the number of active countries.</small>
                    @error('coverage_lead')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save About Page
                </button>
                <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop
