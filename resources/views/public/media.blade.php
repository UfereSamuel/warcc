@extends('layouts.public')

@section('title', 'Media & Videos - ' . setting('site_name', 'Africa CDC Western RCC'))

@section('styles')
<style>
    .media-hero {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
        color: white;
        padding: 100px 0;
        min-height: 400px;
        display: flex;
        align-items: center;
    }

    .youtube-embed {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
    }

    .youtube-embed iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 8px;
    }

    .media-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .media-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .media-card .card-body {
        padding: 1.5rem;
    }

    .media-stats {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
    }

    .youtube-subscribe-btn {
        background: #ff0000;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s ease;
    }

    .youtube-subscribe-btn:hover {
        background: #cc0000;
        color: white;
        text-decoration: none;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="media-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Media Center</h1>
                <p class="lead mb-4">
                    Stay informed with {{ setting('contact_organization', 'Africa CDC Western RCC') }}'s latest videos, 
                    live streams, press releases, and educational content.
                </p>
                @if($youtubeEnabled && $channelUrl)
                    <a href="{{ $channelUrl }}" target="_blank" class="youtube-subscribe-btn">
                        <i class="fab fa-youtube mr-2"></i>
                        Subscribe to Our Channel
                    </a>
                @endif
            </div>
            <div class="col-lg-4 text-center">
                <i class="fab fa-youtube fa-8x opacity-75"></i>
            </div>
        </div>
    </div>
</section>

@if($youtubeEnabled)
    @if($channelUrl)
        <!-- YouTube Channel Embed -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-center fw-bold mb-5">Latest Videos & Live Streams</h2>
                        
                        <!-- Channel Embed Options -->
                        <div class="row mb-5">
                            <div class="col-lg-8">
                                <div class="media-card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">
                                            <i class="fab fa-youtube text-danger mr-2"></i>
                                            {{ setting('contact_organization', 'Our') }} YouTube Channel
                                        </h5>
                                        
                                        @if($channelId)
                                            <!-- Embedded Channel -->
                                            <div class="youtube-embed">
                                                <iframe 
                                                    src="https://www.youtube.com/embed/videoseries?list=UU{{ substr($channelId, 2) }}&autoplay=0&mute=1"
                                                    title="YouTube Channel Videos"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                        @else
                                            <!-- Simple Channel Link -->
                                            <div class="text-center py-5">
                                                <i class="fab fa-youtube fa-4x text-danger mb-3"></i>
                                                <h4>Visit Our YouTube Channel</h4>
                                                <p class="text-muted mb-4">Watch our latest videos, live streams, and educational content</p>
                                                <a href="{{ $channelUrl }}" target="_blank" class="btn btn-danger btn-lg">
                                                    <i class="fab fa-youtube mr-2"></i>
                                                    Open YouTube Channel
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="media-card">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">What You'll Find</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-play-circle text-primary mr-2"></i>
                                                Latest video updates
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-broadcast-tower text-danger mr-2"></i>
                                                Live streams & events
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-newspaper text-info mr-2"></i>
                                                Press releases
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-graduation-cap text-success mr-2"></i>
                                                Educational content
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-microphone text-warning mr-2"></i>
                                                Interviews & discussions
                                            </li>
                                        </ul>
                                        
                                        @if($channelUrl)
                                            <a href="{{ $channelUrl }}" target="_blank" class="btn btn-outline-danger btn-block">
                                                <i class="fab fa-youtube mr-1"></i>
                                                Visit Channel
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Media Features -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h3 class="fw-bold">Stay Connected</h3>
                        <p class="text-muted">Multiple ways to access our media content</p>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="feature-icon bg-danger text-white">
                                <i class="fab fa-youtube"></i>
                            </div>
                            <h6 class="fw-bold">YouTube Channel</h6>
                            <p class="text-muted small">Subscribe for latest videos and live streams</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="feature-icon bg-primary text-white">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h6 class="fw-bold">Notifications</h6>
                            <p class="text-muted small">Get notified of new uploads and live events</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="feature-icon bg-success text-white">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h6 class="fw-bold">Mobile Access</h6>
                            <p class="text-muted small">Watch on any device, anywhere, anytime</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="feature-icon bg-info text-white">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <h6 class="fw-bold">Easy Sharing</h6>
                            <p class="text-muted small">Share important content with colleagues</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <!-- No Channel URL Configured -->
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <i class="fab fa-youtube fa-4x text-muted mb-4"></i>
                        <h3>YouTube Integration Enabled</h3>
                        <p class="text-muted mb-4">
                            YouTube channel integration is enabled, but the channel URL needs to be configured 
                            in the admin settings.
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Admin:</strong> Please configure the YouTube channel URL in 
                            <strong>Admin Panel → System Settings → Media & YouTube</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@else
    <!-- YouTube Integration Disabled -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <i class="fas fa-video fa-4x text-muted mb-4"></i>
                    <h3>Media Center</h3>
                    <p class="text-muted mb-4">
                        The media center is currently not configured. Please check back later for 
                        videos, live streams, and other media content.
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Admin:</strong> Enable YouTube integration in 
                        <strong>Admin Panel → System Settings → Media & YouTube</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Want to Stay Updated?</h3>
                <p class="mb-0">Follow our social media channels and subscribe to our newsletter for the latest updates and announcements.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                @if($youtubeEnabled && $channelUrl)
                    <a href="{{ $channelUrl }}" target="_blank" class="btn btn-light btn-lg">
                        <i class="fab fa-youtube mr-2"></i>Subscribe Now
                    </a>
                @else
                    <a href="{{ route('public.contact') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-envelope mr-2"></i>Contact Us
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Optional: Add analytics tracking for YouTube interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Track YouTube channel clicks
        document.querySelectorAll('a[href*="youtube.com"]').forEach(function(link) {
            link.addEventListener('click', function() {
                console.log('YouTube channel visited');
                // Add your analytics tracking here if needed
            });
        });
    });
</script>
@endsection
