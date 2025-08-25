@extends('adminlte::page')

@section('title', 'Complete Your Profile')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="text-center">
                    <i class="fas fa-user-plus text-primary mr-2"></i>
                    Complete Your Profile
                </h1>
                <p class="text-center text-muted lead">
                    Welcome! Please complete your profile to access the system.
                </p>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Welcome Alert -->
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-info"></i> Welcome to the System!</h5>
            <p class="mb-0">
                Your Microsoft SSO authentication was successful. To complete your registration, 
                please fill in the required information below.
            </p>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Profile Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning">Required</span>
                </div>
            </div>

            <form method="POST" action="{{ route('staff.profile.complete.post') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Personal Information Section -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-user mr-1"></i>
                                Personal Information
                            </h5>

                            <!-- Current Info (Pre-filled from SSO) -->
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" class="form-control-plaintext" 
                                       value="{{ $staff->full_name }}" readonly>
                                <small class="form-text text-muted">
                                    <i class="fas fa-check text-success mr-1"></i>
                                    Retrieved from your Microsoft account
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control-plaintext" 
                                       value="{{ $staff->email }}" readonly>
                                <small class="form-text text-muted">
                                    <i class="fas fa-check text-success mr-1"></i>
                                    Retrieved from your Microsoft account
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="staff_id">Staff ID</label>
                                <input type="text" class="form-control-plaintext" 
                                       value="{{ $staff->staff_id }}" readonly>
                                <small class="form-text text-muted">
                                    <i class="fas fa-check text-success mr-1"></i>
                                    Auto-generated unique identifier
                                </small>
                            </div>

                            <!-- Gender - Required -->
                            <div class="form-group">
                                <label for="gender">Gender <span class="text-danger">*</span></label>
                                <select class="form-control @error('gender') is-invalid @enderror" 
                                        id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone - Optional -->
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
                                       placeholder="e.g., +233 20 123 4567">
                                <small class="form-text text-muted">Optional: Your contact phone number</small>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Work Information Section -->
                        <div class="col-md-6">
                            <h5 class="text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-briefcase mr-1"></i>
                                Work Information
                            </h5>

                            <!-- Position - Required -->
                            <div class="form-group">
                                <label for="position_id">Position/Job Title <span class="text-danger">*</span></label>
                                <select class="form-control @error('position_id') is-invalid @enderror"
                                        id="position_id" name="position_id" required>
                                    <option value="">Select Your Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Select your current job title or position from the list</small>
                                @error('position_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department field removed -->

                            <!-- Hire Date - Optional -->
                            <div class="form-group">
                                <label for="hire_date">Hire Date</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $staff->hire_date ? $staff->hire_date->format('Y-m-d') : '') }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-calendar text-info mr-1"></i>
                                    Optional: Set your hire date (can be updated by admin later)
                                </small>
                            </div>

                            <!-- Profile Picture - Optional -->
                            <div class="form-group">
                                <label for="profile_picture">Profile Picture</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('profile_picture') is-invalid @enderror" 
                                           id="profile_picture" name="profile_picture" accept="image/*">
                                    <label class="custom-file-label" for="profile_picture">Choose file...</label>
                                </div>
                                <small class="form-text text-muted">Optional: Upload a professional photo (JPEG, PNG max 2MB)</small>
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Information Notice -->
                    <div class="alert alert-light border-left-primary mt-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            Important Information
                        </h6>
                        <ul class="mb-0 small">
                            <li>Fields marked with <span class="text-danger">*</span> are required to access the system</li>
                            <li>Your information will be used for official communications and system access</li>
                            <li>You can update most of this information later in your profile settings</li>
                            <li>Contact the administrator if you need assistance or have questions</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Your information is secure and will not be shared externally.
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check mr-1"></i>
                                Complete Profile & Continue
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-question-circle text-info mr-2"></i>
                    Need Help?
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-phone text-success mr-2"></i>Contact Support</h6>
                        <p class="small text-muted">
                            If you have questions about completing your profile, 
                            contact the system administrator or IT support.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-user-tie text-primary mr-2"></i>Admin Assistance</h6>
                        <p class="small text-muted">
                            Your position can be updated by administrators 
                            after you complete the initial setup.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    
    .card-outline.card-primary {
        border-top: 3px solid #007bff;
    }
    
    .form-control-plaintext {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
    }

    .alert-light {
        background-color: #fefefe;
        border: 1px solid #dee2e6;
    }

    .badge {
        font-size: 0.7em;
    }

    .card-header .card-tools .badge {
        margin-top: 2px;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // File input styling
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Form submission loading state
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Processing...');
        });

        // Auto-focus on first required field that's empty
        if (!$('#gender').val()) {
            $('#gender').focus();
        } else if (!$('#position_id').val()) {
            $('#position_id').focus();
        }
    });
</script>
@stop
