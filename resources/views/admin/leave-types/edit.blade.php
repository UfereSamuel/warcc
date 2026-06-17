@extends('adminlte::page')

@section('title', 'Edit Leave Type')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Leave Type</h1>
            <p class="text-muted">Update leave category settings</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leave-types.index') }}">Leave Types</a></li>
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
                    Leave Type Information
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.leave-types.update', $leaveType) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Leave Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $leaveType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $leaveType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="max_days">Maximum Days (Optional)</label>
                        <input type="number" class="form-control @error('max_days') is-invalid @enderror"
                               id="max_days" name="max_days" value="{{ old('max_days', $leaveType->max_days) }}" min="1" max="365">
                        <small class="form-text text-muted">Leave empty for no limit</small>
                        @error('max_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="requires_approval"
                                   name="requires_approval" value="1" {{ old('requires_approval', $leaveType->requires_approval) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_approval">
                                Requires Admin Approval
                            </label>
                        </div>
                        <small class="form-text text-muted">Check if this leave type requires administrator approval</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Leave Type
                    </button>
                    <a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <form method="POST" action="{{ route('admin.leave-types.destroy', $leaveType) }}"
                          style="display: inline;" class="ml-2"
                          onsubmit="return confirm('Are you sure you want to delete this leave type?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Usage Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6"><strong>Created:</strong></div>
                    <div class="col-6">{{ $leaveType->created_at->format('M d, Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><strong>Requests:</strong></div>
                    <div class="col-6">{{ $leaveType->leaveRequests->count() }} total</div>
                </div>
                @if($leaveType->updated_at != $leaveType->created_at)
                <div class="row mb-3">
                    <div class="col-6"><strong>Last Updated:</strong></div>
                    <div class="col-6">{{ $leaveType->updated_at->format('M d, Y') }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Notes
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Changes to this leave type will affect all future leave requests. Existing requests will not be modified.
                </div>

                @if($leaveType->leaveRequests->count() > 0)
                <div class="alert alert-info">
                    <i class="fas fa-database mr-2"></i>
                    <strong>In Use:</strong> This leave type is currently being used by {{ $leaveType->leaveRequests->count() }} leave request(s).
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
