@extends('adminlte::page')

@section('title', 'Edit Public Event')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Public Event</h1>
            <p class="text-muted">Update event details for public display</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.public-events.index') }}">Public Events</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.public-events.show', $publicEvent) }}">Event Details</a></li>
                <li class="breadcrumb-item active">Edit</li>
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
                    <i class="fas fa-edit mr-2"></i>
                    Event Details
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.public-events.update', $publicEvent) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @include('admin.public-events._form', ['event' => $publicEvent, 'isEdit' => true])
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Event
                    </button>
                    <a href="{{ route('admin.public-events.show', $publicEvent) }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @include('admin.public-events._sidebar')
</div>
@stop

@section('js')
    @include('admin.public-events._scripts')
@stop

@section('css')
<style>
    .badge-purple {
        background-color: #6f42c1;
    }
</style>
@stop
