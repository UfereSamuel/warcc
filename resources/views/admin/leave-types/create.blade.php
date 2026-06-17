@extends('adminlte::page')

@section('title', 'Create Leave Type')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Create Leave Type</h1>
            <p class="text-muted">Add a new leave category</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leave-types.index') }}">Leave Types</a></li>
                <li class="breadcrumb-item active">Create</li>
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
                    <i class="fas fa-plus mr-2"></i>
                    Leave Type Information
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.leave-types.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Leave Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="max_days">Maximum Days (Optional)</label>
                        <input type="number" class="form-control @error('max_days') is-invalid @enderror"
                               id="max_days" name="max_days" value="{{ old('max_days') }}" min="1" max="365">
                        <small class="form-text text-muted">Leave empty for no limit</small>
                        @error('max_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="requires_approval"
                                   name="requires_approval" value="1" {{ old('requires_approval', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_approval">
                                Requires Admin Approval
                            </label>
                        </div>
                        <small class="form-text text-muted">Check if this leave type requires administrator approval</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Leave Type
                    </button>
                    <a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
