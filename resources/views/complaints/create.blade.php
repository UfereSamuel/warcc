@extends('layouts.public')

@section('title', 'Submit Complaint')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white py-4">
                    <h2 class="mb-0 text-center">
                        <i class="fas fa-exclamation-circle"></i> Submit a Complaint
                    </h2>
                </div>
                <div class="card-body p-5">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Error:</strong> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Submission Type -->
                        <div class="mb-5">
                            <label class="form-label fw-bold fs-5 text-dark mb-3">
                                <i class="fas fa-user-secret text-primary"></i> SUBMISSION TYPE
                            </label>
                            <div class="border rounded p-4 bg-light">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="is_anonymous" id="anonymous" value="1" {{ old('is_anonymous', '1') == '1' ? 'checked' : '' }} style="width: 20px; height: 20px;">
                                    <label class="form-check-label ms-2" for="anonymous" style="font-size: 1.1rem;">
                                        <strong>Anonymous (Recommended)</strong>
                                        <small class="d-block text-muted mt-1">Your identity will remain completely confidential</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_anonymous" id="identified" value="0" {{ old('is_anonymous') == '0' ? 'checked' : '' }} style="width: 20px; height: 20px;">
                                    <label class="form-check-label ms-2" for="identified" style="font-size: 1.1rem;">
                                        <strong>Include my contact information</strong>
                                        <small class="d-block text-muted mt-1">We may contact you for follow-up if needed</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category" class="form-label fw-bold fs-5 text-dark">
                                <i class="fas fa-list"></i> COMPLAINT CATEGORY <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-lg @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold fs-5 text-dark">
                                <i class="fas fa-file-alt"></i> YOUR COMPLAINT <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-lg @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="8" 
                                      placeholder="Provide detailed information about your complaint..."
                                      required>{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Minimum 20 characters required</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Suggested Solution -->
                        <div class="mb-4">
                            <label for="suggested_solution" class="form-label fw-bold fs-5 text-dark">
                                <i class="fas fa-lightbulb"></i> YOUR SUGGESTED SOLUTION <small class="text-muted fw-normal">(Optional)</small>
                            </label>
                            <textarea class="form-control form-control-lg @error('suggested_solution') is-invalid @enderror" 
                                      id="suggested_solution" 
                                      name="suggested_solution" 
                                      rows="5" 
                                      placeholder="How do you think this issue can be resolved?">{{ old('suggested_solution') }}</textarea>
                            @error('suggested_solution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Evidence Upload -->
                        <div class="mb-5">
                            <label for="evidence" class="form-label fw-bold fs-5 text-dark">
                                <i class="fas fa-paperclip"></i> UPLOAD EVIDENCE <small class="text-muted fw-normal">(Optional)</small>
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('evidence') is-invalid @enderror" 
                                       id="evidence" 
                                       name="evidence"
                                       accept="image/jpeg,image/png,application/pdf">
                                <label class="custom-file-label" for="evidence">Choose file...</label>
                            </div>
                            <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF (Max 10MB)</small>
                            @error('evidence')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact Information (shown only if not anonymous) -->
                        <div id="contact-info" style="display: none;">
                            <div class="border-top pt-4 mb-4">
                                <h4 class="fw-bold text-dark mb-4">
                                    <i class="fas fa-address-card"></i> YOUR CONTACT INFORMATION
                                </h4>
                                
                                <div class="mb-3">
                                    <label for="complainant_name" class="form-label fw-bold">NAME <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg @error('complainant_name') is-invalid @enderror" 
                                           id="complainant_name" 
                                           name="complainant_name" 
                                           value="{{ old('complainant_name') }}"
                                           placeholder="Enter your full name">
                                    @error('complainant_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="complainant_email" class="form-label fw-bold">EMAIL <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control form-control-lg @error('complainant_email') is-invalid @enderror" 
                                           id="complainant_email" 
                                           name="complainant_email" 
                                           value="{{ old('complainant_email') }}"
                                           placeholder="Enter your email address">
                                    @error('complainant_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="complainant_phone" class="form-label fw-bold">PHONE <small class="text-muted fw-normal">(Optional)</small></label>
                                    <input type="tel" 
                                           class="form-control form-control-lg @error('complainant_phone') is-invalid @enderror" 
                                           id="complainant_phone" 
                                           name="complainant_phone" 
                                           value="{{ old('complainant_phone') }}"
                                           placeholder="Enter your phone number">
                                    @error('complainant_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Important Notice -->
                        <div class="alert alert-info border-info mb-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle"></i> Important Notice</h5>
                            <ul class="mb-0">
                                <li>All complaints are treated with strict confidentiality</li>
                                <li>Your submission will be reviewed by authorized personnel only</li>
                                <li>False or malicious complaints may result in disciplinary action</li>
                                <li>You will receive a complaint number for reference after submission</li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger btn-lg px-5 py-3" style="font-size: 1.2rem; font-weight: bold;">
                                <i class="fas fa-paper-plane"></i> SUBMIT FEEDBACK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle contact information based on anonymous selection
    $('input[name="is_anonymous"]').on('change', function() {
        if ($(this).val() == '0') {
            $('#contact-info').slideDown();
            $('#complainant_name, #complainant_email').attr('required', true);
        } else {
            $('#contact-info').slideUp();
            $('#complainant_name, #complainant_email').attr('required', false);
        }
    });

    // Trigger on page load (in case of validation errors)
    if ($('input[name="is_anonymous"]:checked').val() == '0') {
        $('#contact-info').show();
        $('#complainant_name, #complainant_email').attr('required', true);
    }

    // Update file input label when file is selected
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file...');
    });
});
</script>
@endpush
@endsection
