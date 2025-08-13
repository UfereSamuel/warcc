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
                    Get in touch with the Africa CDC Western Regional Collaborating Centre.
                    We're here to support health security initiatives across West Africa.
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
                        University of Ghana Medical School<br>
                        College of Health Sciences<br>
                        Korle-Bu, Accra<br>
                        Ghana
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
                        Main Office: <a href="tel:+233302681109" class="text-primary">+233 30 268 1109</a><br>
                        Emergency Line: <a href="tel:+233544334455" class="text-primary">+233 54 433 4455</a><br>
                        Fax: +233 30 268 1110
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
                        General: <a href="mailto:info@africacdc-western.org" class="text-primary">info@africacdc-western.org</a><br>
                        Director: <a href="mailto:director@africacdc-western.org" class="text-primary">director@africacdc-western.org</a><br>
                        Emergency: <a href="mailto:emergency@africacdc-western.org" class="text-primary">emergency@africacdc-western.org</a>
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
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.7267719636916!2d-0.2058686!3d5.614818!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9084b2b7a773%3A0x6b8b7c0ec5f6d8a7!2sUniversity%20of%20Ghana%20Medical%20School!5e0!3m2!1sen!2sgh!4v1640995200000!5m2!1sen!2sgh"
                        width="100%"
                        height="400"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Regional Offices -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold text-primary text-center mb-5">Regional Presence</h3>
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="contact-card text-center">
                            <h6 class="fw-bold">Ghana Hub</h6>
                            <p class="text-muted small mb-0">
                                University of Ghana<br>
                                Accra, Ghana<br>
                                <a href="tel:+233302681109" class="text-primary">+233 30 268 1109</a>
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="contact-card text-center">
                            <h6 class="fw-bold">Nigeria Office</h6>
                            <p class="text-muted small mb-0">
                                Coming Soon<br>
                                Abuja, Nigeria<br>
                                <span class="text-muted">In Development</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="contact-card text-center">
                            <h6 class="fw-bold">Senegal Office</h6>
                            <p class="text-muted small mb-0">
                                Coming Soon<br>
                                Dakar, Senegal<br>
                                <span class="text-muted">In Development</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="contact-card text-center">
                            <h6 class="fw-bold">Côte d'Ivoire Office</h6>
                            <p class="text-muted small mb-0">
                                Coming Soon<br>
                                Abidjan, Côte d'Ivoire<br>
                                <span class="text-muted">In Development</span>
                            </p>
                        </div>
                    </div>
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
