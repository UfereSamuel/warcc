@extends('adminlte::page')

@section('title', 'Create Hero Slide')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Create Hero Slide</h1>
        <a href="{{ route('admin.content.hero-slides.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Slides
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Slide Information</h3>
            </div>

            <form action="{{ route('admin.content.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subtitle</label>
                        <input type="text" class="form-control @error('subtitle') is-invalid @enderror"
                               id="subtitle" name="subtitle" value="{{ old('subtitle') }}">
                        @error('subtitle')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">Maximum 500 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="image">Slide Image <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('image') is-invalid @enderror"
                                   id="image" name="image" accept="image/*" required>
                            <label class="custom-file-label" for="image">Choose image file...</label>
                        </div>
                        @error('image')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            Recommended size: 1920x600px. Maximum file size: 5MB.
                            Supported formats: JPEG, PNG, JPG, WebP
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_text">Button Text</label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror"
                                       id="button_text" name="button_text" value="{{ old('button_text') }}">
                                @error('button_text')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_link">Button Link</label>
                                <input type="url" class="form-control @error('button_link') is-invalid @enderror"
                                       id="button_link" name="button_link" value="{{ old('button_link') }}"
                                       placeholder="https://example.com">
                                @error('button_link')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order_index">Display Order <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('order_index') is-invalid @enderror"
                                       id="order_index" name="order_index" value="{{ old('order_index', 1) }}"
                                       min="0" required>
                                @error('order_index')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Slide
                    </button>
                    <a href="{{ route('admin.content.hero-slides.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Preview Guidelines</h3>
            </div>
            <div class="card-body">
                <h6>Image Requirements:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Recommended: 1920x600 pixels</li>
                    <li><i class="fas fa-check text-success"></i> Maximum file size: 5MB</li>
                    <li><i class="fas fa-check text-success"></i> Formats: JPEG, PNG, JPG, WebP</li>
                </ul>

                <h6 class="mt-3">Content Tips:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-lightbulb text-warning"></i> Keep titles concise and impactful</li>
                    <li><i class="fas fa-lightbulb text-warning"></i> Use subtitles for additional context</li>
                    <li><i class="fas fa-lightbulb text-warning"></i> Descriptions should be under 500 characters</li>
                    <li><i class="fas fa-lightbulb text-warning"></i> Button text should be action-oriented</li>
                </ul>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> The slide will be displayed with an overlay to ensure text readability.
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Custom file input label update
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Character counter for description
    $('#description').on('input', function() {
        const maxLength = 500;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;

        if (!$('#char-counter').length) {
            $(this).after('<small id="char-counter" class="form-text text-muted"></small>');
        }

        $('#char-counter').text(`${remaining} characters remaining`);

        if (remaining < 0) {
            $('#char-counter').removeClass('text-muted').addClass('text-danger');
        } else {
            $('#char-counter').removeClass('text-danger').addClass('text-muted');
        }
    });

    // Button link validation
    $('#button_text, #button_link').on('input', function() {
        const buttonText = $('#button_text').val();
        const buttonLink = $('#button_link').val();

        if (buttonText && !buttonLink) {
            $('#button_link').addClass('is-invalid');
            if (!$('#button-link-error').length) {
                $('#button_link').after('<div id="button-link-error" class="invalid-feedback">Button link is required when button text is provided</div>');
            }
        } else {
            $('#button_link').removeClass('is-invalid');
            $('#button-link-error').remove();
        }
    });
});
</script>
@stop
