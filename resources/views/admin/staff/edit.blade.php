@extends('adminlte::page')

@section('title', 'Edit Staff - ' . $staff->full_name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Staff Member</h1>
                            <p class="text-muted">{{ $staff->full_name }} - {{ $staff->position_title }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.staff.show', $staff) }}">{{ $staff->full_name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Edit Staff Information
                </h3>
            </div>

            <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-warning border-bottom pb-2">Personal Information</h5>

                            <!-- Current Profile Picture -->
                            @if($staff->profile_picture_url)
                                <div class="form-group text-center mb-3">
                                    <img src="{{ $staff->profile_picture_url }}"
                                         alt="{{ $staff->full_name }}"
                                         class="img-circle elevation-2"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                    <p class="text-muted mt-1">Current Profile Picture</p>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="staff_id">Staff ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('staff_id') is-invalid @enderror"
                                       id="staff_id" name="staff_id" value="{{ old('staff_id', $staff->staff_id) }}"
                                       placeholder="e.g., RCC-001" required>
                                @error('staff_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                               id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required>
                                        @error('first_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                               id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required>
                                        @error('last_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $staff->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone', $staff->phone) }}"
                                               placeholder="+1234567890">
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select class="form-control @error('gender') is-invalid @enderror"
                                                id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $staff->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="profile_picture">Update Profile Picture</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('profile_picture') is-invalid @enderror"
                                               id="profile_picture" name="profile_picture" accept="image/*">
                                        <label class="custom-file-label" for="profile_picture">Choose new file</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Leave empty to keep current picture. Max: 2MB. Formats: JPEG, PNG, JPG</small>
                                @error('profile_picture')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-warning border-bottom pb-2">Professional Information</h5>

                            <div class="form-group">
                                <label for="position_id">Position <span class="text-danger">*</span></label>
                                <select class="form-control @error('position_id') is-invalid @enderror"
                                        id="position_id" name="position_id" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id', $staff->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Select the appropriate job position for this staff member</small>
                            </div>

                            <!-- Department field removed -->

                            <div class="form-group">
                                <label for="hire_date">Hire Date</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $staff->hire_date ? $staff->hire_date->format('Y-m-d') : '') }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('hire_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Optional: Set the date when this staff member was hired</small>
                            </div>

                            <div class="form-group">
                                <label for="annual_leave_balance">Annual Leave Balance (days) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('annual_leave_balance') is-invalid @enderror"
                                       id="annual_leave_balance" name="annual_leave_balance"
                                       value="{{ old('annual_leave_balance', $staff->annual_leave_balance) }}"
                                       min="0" max="50" required>
                                @error('annual_leave_balance')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Current balance: {{ $staff->annual_leave_balance }} days</small>
                            </div>

                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="is_admin"
                                           name="is_admin" value="1" {{ old('is_admin', $staff->is_admin) ? 'checked' : '' }}>
                                    <label for="is_admin" class="custom-control-label">
                                        <strong>Administrator Privileges</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    @if($staff->is_admin)
                                        Currently has admin access
                                    @else
                                        Currently does not have admin access
                                    @endif
                                </small>
                            </div>

                            <!-- Account Summary -->
                            <div class="card bg-light mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Account Summary</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Member since:</strong> {{ $staff->hire_date ? $staff->hire_date->format('M d, Y') . ' (' . $staff->hire_date->diffForHumans() . ')' : 'Not set' }}</li>
                                        <li><strong>Last updated:</strong> {{ $staff->updated_at->format('M d, Y g:i A') }}</li>
                                        <li><strong>Total attendance:</strong> {{ $staff->attendances()->count() }} days</li>
                                        <li><strong>Leave requests:</strong> {{ $staff->leaveRequests()->count() }} total</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="reset" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i> Reset Changes
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i> Update Staff Information
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .border-bottom {
            border-bottom: 2px solid #dee2e6 !important;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .text-danger {
            font-weight: bold;
        }

        .form-group label {
            font-weight: 600;
            color: #2c3e50;
        }

        .card.bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Handle department selection
            $('#department').change(function() {
                if ($(this).val() === 'other') {
                    $('#other-department-group').show();
                    $('#new_department').attr('required', true);
                } else {
                    $('#other-department-group').hide();
                    $('#new_department').attr('required', false);
                }
            });

            // Update department field when new department is entered
            $('#new_department').on('input', function() {
                if ($(this).val().trim() !== '') {
                    $('#department').val($(this).val());
                }
            });

            // File input label update
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
            });

            // Form validation feedback
            $('form').on('submit', function(e) {
                let isValid = true;

                // Check required fields
                $(this).find('[required]').each(function() {
                    if (!$(this).val().trim()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    toastr.error('Please fill in all required fields');
                }
            });

            // Warn about admin changes
            $('#is_admin').change(function() {
                if ($(this).is(':checked') && !{{ $staff->is_admin ? 'true' : 'false' }}) {
                    toastr.warning('You are granting administrator privileges to this user.');
                } else if (!$(this).is(':checked') && {{ $staff->is_admin ? 'true' : 'false' }}) {
                    toastr.warning('You are removing administrator privileges from this user.');
                }
            });
        });
    </script>
@stop
