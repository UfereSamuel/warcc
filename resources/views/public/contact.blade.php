@extends('layouts.public')

@section('title', 'Contact Us - Africa CDC Western RCC')

@section('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--gold) 100%);
        color: white;
        padding: 100px 0 80px;
    }

    .contact-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-green);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .map-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .office-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 2rem;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead mb-4">
                    Get in touch with {{ setting('contact_organization', 'the Africa CDC Western Regional Collaborating Centre') }}.
                    {{ setting('site_description', 'We\'re here to support health security initiatives across West Africa.') }}
                </p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-envelope fa-6x opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Our Location</h5>
                    <p class="text-muted mb-0">
                        {!! nl2br(e(setting('contact_address', "Plot 114 Yakubu Gowon Cres, Asokoro\nAso 900103, Federal Capital Territory\nNigeria"))) !!}
                    </p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="fas fa-phone fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Phone</h5>
                    <p class="text-muted mb-0">
                        @if(setting('contact_phone'))
                            Main Office: <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', setting('contact_phone')) }}" class="text-primary">{{ setting('contact_phone') }}</a><br>
                        @else
                            Main Office: <a href="tel:+233302681109" class="text-primary">+233 30 268 1109</a><br>
                        @endif
                        @if(setting('contact_fax'))
                            Fax: {{ setting('contact_fax') }}
                        @else
                            Fax: +233 30 268 1110
                        @endif
                    </p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="fas fa-envelope fa-lg"></i>
                    </div>
                    <h5 class="fw-bold">Email</h5>
                    <p class="text-muted mb-0">
                        @if(setting('contact_email'))
                            General: <a href="mailto:{{ setting('contact_email') }}" class="text-primary">{{ setting('contact_email') }}</a><br>
                        @else
                            General: <a href="mailto:westernrcc@africacdc.org" class="text-primary">westernrcc@africacdc.org</a><br>
                        @endif
                        @if(setting('contact_website'))
                            Website: <a href="{{ setting('contact_website') }}" target="_blank" class="text-primary">{{ setting('contact_website') }}</a>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Office Hours & Location Details -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="office-info">
                    <h3 class="fw-bold text-primary mb-4">Office Hours</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Regular Hours</h6>
                            <ul class="list-unstyled">
                                <li><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM</li>
                                <li><strong>Saturday:</strong> 9:00 AM - 1:00 PM</li>
                                <li><strong>Sunday:</strong> Closed</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Emergency Response</h6>
                            <ul class="list-unstyled">
                                <li><strong>24/7 Emergency Line</strong></li>
                                <li>Public Health Emergencies</li>
                                <li>Disease Outbreak Response</li>
                                <li>Surveillance Alerts</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="office-info">
                    <h3 class="fw-bold text-primary mb-4">Key Departments</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-virus me-2 text-primary"></i> Disease Surveillance</li>
                                <li><i class="fas fa-shield-alt me-2 text-primary"></i> Emergency Preparedness</li>
                                <li><i class="fas fa-microscope me-2 text-primary"></i> Laboratory Services</li>
                                <li><i class="fas fa-graduation-cap me-2 text-primary"></i> Training & Capacity</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chart-line me-2 text-primary"></i> Data Analytics</li>
                                <li><i class="fas fa-handshake me-2 text-primary"></i> Partnerships</li>
                                <li><i class="fas fa-broadcast-tower me-2 text-primary"></i> Communications</li>
                                <li><i class="fas fa-cogs me-2 text-primary"></i> Administration</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold text-primary text-center mb-5">Find Us</h3>
                <div class="map-container">
                    <iframe
                        src="{{ setting('contact_map_embed_url', 'https://maps.google.com/maps?q=Plot+114+Yakubu+Gowon+Cres,+Asokoro,+Abuja,+Nigeria&output=embed') }}"
                        width="100%"
                        height="400"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Western RCC office location">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Contact -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Need Immediate Assistance?</h3>
                <p class="mb-0">For public health emergencies and urgent surveillance matters, contact our 24/7 emergency response team.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="tel:+233544334455" class="btn btn-light btn-lg">
                    <i class="fas fa-phone me-2"></i>Emergency Line
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
