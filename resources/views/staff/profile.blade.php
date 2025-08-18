@extends('layouts.staff')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row">
    <!-- Profile Information -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @if($staff->profile_picture)
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('storage/images/uploads/' . $staff->profile_picture) }}"
                             alt="Profile Picture">
                    @else
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('vendor/adminlte/dist/img/avatar5.png') }}"
                             alt="Default Profile Picture">
                    @endif
                </div>

                <h3 class="profile-username text-center">{{ $staff->full_name }}</h3>

                <p class="text-muted text-center">{{ $staff->position ?? 'Staff Member' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Staff ID</b> <a class="float-right">{{ $staff->staff_id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Department</b> <a class="float-right">{{ $staff->department ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $staff->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <a class="float-right">{{ $staff->phone ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Joined</b> <a class="float-right">{{ $staff->created_at->format('M Y') }}</a>
                    </li>
                </ul>

                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#editProfileModal">
                    <i class="fas fa-edit mr-1"></i>
                    Edit Profile
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Quick Stats
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{ $staff->annual_leave_balance ?? 21 }}</h5>
                            <span class="description-text">Leave Days</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{ $staff->attendances()->thisMonth()->count() }}</h5>
                            <span class="description-text">This Month</span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{ $staff->missions()->count() }}</h5>
                            <span class="description-text">Total Missions</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{ number_format($staff->attendances()->whereNotNull('total_hours')->avg('total_hours') ?? 0, 1) }}h</h5>
                            <span class="description-text">Avg Hours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-md-8">
        <!-- Personal Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>
                    Personal Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name</label>
                            <p class="form-control-static">{{ $staff->first_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name</label>
                            <p class="form-control-static">{{ $staff->last_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Address</label>
                            <p class="form-control-static">{{ $staff->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <p class="form-control-static">{{ $staff->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Staff ID</label>
                            <p class="form-control-static">{{ $staff->staff_id }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Position</label>
                            <p class="form-control-static">{{ $staff->position ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-briefcase mr-2"></i>
                    Employment Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Department</label>
                            <p class="form-control-static">{{ $staff->department ?? 'Not assigned' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employment Status</label>
                            <p class="form-control-static">
                                <span class="badge badge-{{ $staff->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($staff->status ?? 'active') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Join Date</label>
                            <p class="form-control-static">{{ $staff->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Annual Leave Balance</label>
                            <p class="form-control-static">{{ $staff->annual_leave_balance ?? 21 }} days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Recent Activity
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @php
                        $recentAttendances = $staff->attendances()->orderBy('date', 'desc')->take(5)->get();
                    @endphp

                    @forelse($recentAttendances as $attendance)
                    <div class="time-label">
                        <span class="bg-green">{{ $attendance->date->format('M d') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-clock bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time">
                                <i class="fas fa-clock"></i>
                                @if($attendance->clock_in_time)
                                    {{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('h:i A') }}
                                    @if($attendance->clock_out_time)
                                        - {{ \Carbon\Carbon::parse($attendance->clock_out_time)->format('h:i A') }}
                                    @endif
                                @endif
                            </span>
                            <h3 class="timeline-header">Attendance - {{ $attendance->date->format('l') }}</h3>
                            <div class="timeline-body">
                                Status: <span class="badge badge-{{ $attendance->status === 'present' ? 'success' : 'danger' }}">{{ ucfirst($attendance->status) }}</span>
                                @if($attendance->total_hours)
                                    | Total Hours: {{ number_format($attendance->total_hours, 1) }}h
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name"
                                       name="first_name"
                                       value="{{ old('first_name', $staff->first_name) }}"
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name"
                                       name="last_name"
                                       value="{{ old('last_name', $staff->last_name) }}"
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel"
                               class="form-control @error('phone') is-invalid @enderror"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $staff->phone) }}"
                               placeholder="+233 XX XXX XXXX">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">Profile Picture</label>
                        <div class="custom-file">
                            <input type="file"
                                   class="custom-file-input @error('profile_picture') is-invalid @enderror"
                                   id="profile_picture"
                                   name="profile_picture"
                                   accept="image/*">
                            <label class="custom-file-label" for="profile_picture">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Maximum file size: 2MB. Accepted formats: JPEG, PNG, JPG</small>
                        @error('profile_picture')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Profile Picture Preview -->
                    @if($staff->profile_picture)
                    <div class="form-group">
                        <label>Current Profile Picture</label>
                        <div>
                            <img src="{{ asset('storage/images/uploads/' . $staff->profile_picture) }}"
                                 alt="Current Profile Picture"
                                 class="img-thumbnail"
                                 style="max-width: 150px;">
                        </div>
                    </div>
                    @endif

                    <!-- New Image Preview -->
                    <div class="form-group" id="new-image-preview" style="display: none;">
                        <label>New Profile Picture Preview</label>
                        <div>
                            <img id="preview-image"
                                 alt="New Profile Picture Preview"
                                 class="img-thumbnail"
                                 style="max-width: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update custom file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);

        // Show image preview
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#new-image-preview').show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Show edit modal if there are validation errors
    @if($errors->any())
        $('#editProfileModal').modal('show');
    @endif
});
</script>
@endpush
