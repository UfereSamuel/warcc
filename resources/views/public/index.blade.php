@extends('layouts.public')

@section('title', 'Home - Africa CDC Western RCC')
@section('body_class', 'page-home')
@section('nav_class', 'navbar-home')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
<style>
    .page-home {
        --hp-green: #2f8a3c;
        --hp-forest: #163821;
        --hp-deep: #0f2416;
        --hp-sand: #e8efe6;
        --hp-gold: #c4ae6a;
        --hp-ink: #142018;
        font-family: 'Manrope', system-ui, sans-serif;
        background: #f4f7f3;
    }

    .page-home h1,
    .page-home h2,
    .page-home h3,
    .page-home .hp-display {
        font-family: 'Syne', system-ui, sans-serif;
        letter-spacing: -0.02em;
    }

    /* Transparent nav over hero */
    .page-home .navbar-home {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 40;
        background: transparent !important;
        box-shadow: none !important;
        transition: background 0.35s ease, box-shadow 0.35s ease, padding 0.35s ease;
    }

    .page-home .navbar-home .nav-link,
    .page-home .navbar-home .navbar-brand span {
        color: rgba(255, 255, 255, 0.92) !important;
    }

    .page-home .navbar-home .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.45);
    }

    .page-home .navbar-home .navbar-toggler-icon {
        filter: invert(1);
    }

    .page-home .navbar-home.is-scrolled {
        position: fixed;
        background: rgba(255, 255, 255, 0.96) !important;
        box-shadow: 0 8px 30px rgba(15, 36, 22, 0.08) !important;
        backdrop-filter: blur(12px);
    }

    .page-home .navbar-home.is-scrolled .nav-link,
    .page-home .navbar-home.is-scrolled .navbar-brand span {
        color: var(--hp-ink) !important;
    }

    .page-home .navbar-home.is-scrolled .navbar-toggler-icon {
        filter: none;
    }

    .page-home .navbar-home .navbar-collapse.show,
    .page-home .navbar-home .navbar-collapse.collapsing {
        background: rgba(15, 36, 22, 0.96);
        margin-top: 0.75rem;
        padding: 1rem;
        border-radius: 1rem;
    }

    .page-home .navbar-home.is-scrolled .navbar-collapse.show,
    .page-home .navbar-home.is-scrolled .navbar-collapse.collapsing {
        background: #fff;
        box-shadow: 0 12px 30px rgba(15, 36, 22, 0.08);
    }

    .page-home .navbar-home .btn-primary {
        background: #fff;
        color: var(--hp-forest);
        border: none;
        font-weight: 600;
    }

    .page-home .navbar-home.is-scrolled .btn-primary {
        background: var(--hp-green);
        color: #fff;
    }

    /* Full-bleed hero slider */
    .hp-hero {
        position: relative;
        min-height: min(78vh, 760px);
        color: #fff;
        overflow: hidden;
        background: var(--hp-deep);
    }

    .hp-hero .carousel,
    .hp-hero .carousel-inner,
    .hp-hero .carousel-item {
        height: min(78vh, 760px);
        min-height: 480px;
    }

    .hp-slide-media {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        transform: scale(1.04);
        animation: hpKenBurns 16s ease-out forwards;
    }

    .carousel-item.active .hp-slide-media {
        animation: hpKenBurns 16s ease-out forwards;
    }

    @keyframes hpKenBurns {
        from { transform: scale(1.06); }
        to { transform: scale(1); }
    }

    .hp-slide-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 36, 22, 0.78) 0%, rgba(15, 36, 22, 0.18) 45%, rgba(15, 36, 22, 0.25) 100%);
        z-index: 1;
    }

    .hp-slide-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        align-items: flex-end;
        padding: 0 0 5.5rem;
    }

    .hp-slide-caption {
        max-width: 28rem;
    }

    .hp-slide-title {
        font-family: 'Syne', system-ui, sans-serif;
        font-size: clamp(1.35rem, 2.6vw, 2rem);
        font-weight: 700;
        line-height: 1.2;
        margin: 0 0 0.85rem;
        color: #fff;
        text-shadow: 0 8px 24px rgba(0,0,0,0.25);
    }

    .hp-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1.2rem;
        border-radius: 999px;
        background: #fff;
        color: var(--hp-forest);
        font-weight: 700;
        font-size: 0.92rem;
        text-decoration: none;
        transition: transform 0.25s ease, background 0.25s ease;
    }

    .hp-cta:hover {
        transform: translateY(-2px);
        background: var(--hp-gold);
        color: var(--hp-deep);
    }

    .hp-cta-ghost {
        background: transparent;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        margin-left: 0.6rem;
    }

    .hp-cta-ghost:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .hp-hero .carousel-indicators {
        bottom: 2rem;
        gap: 0.4rem;
        margin: 0;
        z-index: 5;
    }

    .hp-hero .carousel-indicators [data-bs-target] {
        width: 2.4rem;
        height: 3px;
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.35);
        opacity: 1;
    }

    .hp-hero .carousel-indicators .active {
        background: #fff;
        width: 3.2rem;
    }

    .hp-hero .carousel-control-prev,
    .hp-hero .carousel-control-next {
        width: 3.5rem;
        height: 3.5rem;
        top: auto;
        bottom: 2rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        opacity: 1;
        z-index: 5;
    }

    .hp-hero .carousel-control-prev { left: auto; right: 6.2rem; }
    .hp-hero .carousel-control-next { right: 2rem; }

    /* Sections */
    .hp-section {
        padding: clamp(4rem, 8vw, 7rem) 0;
    }

    .hp-section-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 700;
        color: var(--hp-forest);
        margin-bottom: 0.75rem;
    }

    .hp-section-lead {
        font-size: 1.1rem;
        color: rgba(20, 32, 24, 0.68);
        max-width: 40rem;
        margin: 0 auto 2.5rem;
    }

    .hp-reveal {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }

    .hp-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Mission / Vision editorial */
    .hp-split-wrap {
        display: grid;
        gap: 0;
        background: #fff;
    }

    @media (min-width: 992px) {
        .hp-split-wrap {
            grid-template-columns: 1.05fr 1fr;
            min-height: 420px;
        }
    }

    .hp-split-media {
        min-height: 280px;
        background:
            linear-gradient(135deg, rgba(15, 36, 22, 0.35), rgba(47, 138, 60, 0.25)),
            var(--hp-forest);
        background-size: cover;
        background-position: center;
    }

    .hp-split {
        display: grid;
        gap: 0;
        background: linear-gradient(160deg, #163821 0%, #1f4a2c 55%, #2f6a3a 100%);
        color: #fff;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .hp-split {
            grid-template-columns: 1fr 1fr;
        }
    }

    .hp-split-panel {
        padding: clamp(2rem, 4vw, 3.25rem);
        position: relative;
    }

    .hp-split-panel + .hp-split-panel {
        background: rgba(255, 255, 255, 0.04);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    @media (min-width: 768px) {
        .hp-split-panel + .hp-split-panel {
            border-top: 0;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
        }
    }

    .hp-split-panel h3 {
        font-size: clamp(1.4rem, 2.5vw, 1.85rem);
        margin-bottom: 0.85rem;
    }

    .hp-split-panel p {
        color: rgba(255, 255, 255, 0.82);
        margin: 0;
        line-height: 1.7;
        font-size: 1rem;
    }

    .hp-gallery {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .hp-gallery {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .hp-gallery-item {
        position: relative;
        border-radius: 1.1rem;
        overflow: hidden;
        min-height: 220px;
        background: #d7e3d9;
    }

    .hp-gallery-item img {
        width: 100%;
        height: 100%;
        min-height: 220px;
        object-fit: cover;
        display: block;
        transition: transform 0.45s ease;
    }

    .hp-gallery-item:hover img {
        transform: scale(1.04);
    }

    .hp-gallery-item figcaption {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 1rem 1.1rem;
        background: linear-gradient(to top, rgba(15, 36, 22, 0.75), transparent);
        color: #fff;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Focus areas — no cards */
    .hp-focus-grid {
        display: grid;
        gap: 1.5rem;
    }

    @media (min-width: 768px) {
        .hp-focus-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
    }

    .hp-focus-item {
        padding-top: 1.25rem;
        border-top: 2px solid var(--hp-green);
    }

    .hp-focus-item h4 {
        font-size: 1.25rem;
        color: var(--hp-forest);
        margin-bottom: 0.65rem;
    }

    .hp-focus-item p {
        margin: 0;
        color: rgba(20, 32, 24, 0.68);
        line-height: 1.65;
    }

    .hp-ahss {
        background:
            radial-gradient(circle at top left, rgba(47, 138, 60, 0.12), transparent 42%),
            linear-gradient(180deg, #f7faf6 0%, #eef4ec 100%);
    }

    .hp-ahss-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .hp-ahss-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1200px) {
        .hp-ahss-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }

    .hp-ahss-pillar {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(22, 56, 33, 0.08);
        border-radius: 1rem;
        padding: 1.25rem 1.15rem;
        height: 100%;
    }

    .hp-ahss-pillar .num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 999px;
        background: var(--hp-forest);
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .hp-ahss-pillar h4 {
        font-size: 1.02rem;
        color: var(--hp-forest);
        margin-bottom: 0.55rem;
        line-height: 1.3;
    }

    .hp-ahss-pillar p {
        margin: 0;
        color: rgba(20, 32, 24, 0.68);
        font-size: 0.92rem;
        line-height: 1.55;
    }

    /* Countries — clean mosaic */
    .hp-countries {
        background:
            radial-gradient(circle at top right, rgba(47, 138, 60, 0.08), transparent 40%),
            linear-gradient(180deg, #eef4ec 0%, #f7faf6 100%);
    }

    .hp-country-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.85rem;
    }

    @media (min-width: 576px) {
        .hp-country-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (min-width: 992px) {
        .hp-country-grid { grid-template-columns: repeat(5, 1fr); gap: 1rem; }
    }

    .hp-country {
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(22, 56, 33, 0.08);
        border-radius: 1rem;
        padding: 1.1rem 0.85rem;
        text-align: center;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .hp-country:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(15, 36, 22, 0.08);
    }

    .hp-country img {
        height: 28px;
        width: auto;
        margin-bottom: 0.65rem;
        border-radius: 2px;
    }

    .hp-country strong {
        display: block;
        font-size: 0.92rem;
        color: var(--hp-forest);
        margin-bottom: 0.2rem;
    }

    .hp-country span {
        display: block;
        font-size: 0.72rem;
        color: rgba(20, 32, 24, 0.55);
        line-height: 1.35;
    }

    /* Events */
    .hp-event {
        display: grid;
        grid-template-rows: 180px 1fr;
        background: #fff;
        border-radius: 1.25rem;
        overflow: hidden;
        border: 1px solid rgba(22, 56, 33, 0.08);
        height: 100%;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        text-decoration: none;
        color: inherit;
    }

    .hp-event:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(15, 36, 22, 0.1);
        color: inherit;
    }

    .hp-event-media {
        background: linear-gradient(135deg, #163821, #2f8a3c);
        background-size: cover;
        background-position: center;
    }

    .hp-event-body {
        padding: 1.35rem 1.4rem 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .hp-event-meta {
        font-size: 0.8rem;
        color: rgba(20, 32, 24, 0.55);
        margin-bottom: 0.65rem;
    }

    .hp-event h3 {
        font-size: 1.2rem;
        color: var(--hp-forest);
        margin-bottom: 0.55rem;
    }

    .hp-event p {
        color: rgba(20, 32, 24, 0.65);
        font-size: 0.95rem;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .hp-link {
        font-weight: 700;
        color: var(--hp-green);
        text-decoration: none;
    }

    .hp-affiliate {
        background: #fff;
        border-block: 1px solid rgba(22, 56, 33, 0.08);
        padding: 1.75rem 0;
    }

    .hp-affiliate-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .hp-affiliate p {
        margin: 0;
        color: rgba(20, 32, 24, 0.72);
        max-width: 40rem;
        line-height: 1.55;
    }

    .hp-affiliate strong {
        color: var(--hp-forest);
    }

    .hp-affiliate-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.7rem 1.2rem;
        border-radius: 999px;
        border: 1px solid rgba(47, 138, 60, 0.35);
        color: var(--hp-forest);
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .hp-affiliate-link:hover {
        background: var(--hp-forest);
        color: #fff;
        border-color: var(--hp-forest);
    }

    /* Values strip */
    .hp-values {
        background: var(--hp-deep);
        color: #fff;
    }

    .hp-values .hp-section-title,
    .hp-values .hp-section-lead {
        color: #fff;
    }

    .hp-values .hp-section-lead {
        color: rgba(255, 255, 255, 0.7);
    }

    .hp-value-item {
        padding: 1.5rem 0;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }

    .hp-value-item h4 {
        font-size: 1.35rem;
        margin-bottom: 0.5rem;
    }

    .hp-value-item p {
        margin: 0;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.65;
    }

    .page-home .footer {
        margin-top: 0 !important;
    }

    @media (max-width: 767.98px) {
        .hp-hero .carousel-control-prev,
        .hp-hero .carousel-control-next {
            display: none;
        }

        .hp-cta-ghost {
            margin-left: 0;
            margin-top: 0.75rem;
        }

        .hp-slide-content .d-flex {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
@php
    $homepageImages = $homepageContent['images'] ?? ['mission_image_url' => null, 'gallery' => []];
    $galleryItems = collect($homepageImages['gallery'] ?? [])->filter(fn ($item) => ! empty($item['url']));
@endphp

<!-- Image-first hero slider (minimal caption for internal portal) -->
<section class="hp-hero" aria-label="Featured highlights">
    @if($heroSlides->count() > 0)
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="7000" data-bs-pause="hover">
        <div class="carousel-indicators">
            @foreach($heroSlides as $index => $slide)
                <button type="button"
                        data-bs-target="#heroCarousel"
                        data-bs-slide-to="{{ $index }}"
                        @if($index === 0) class="active" aria-current="true" @endif
                        aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach($heroSlides as $index => $slide)
            <div class="carousel-item @if($index === 0) active @endif">
                <div class="hp-slide-media" style="background-image: url('{{ $slide->image_url }}');" role="img" aria-label="{{ $slide->title }}"></div>
                <div class="hp-slide-overlay"></div>
                <div class="hp-slide-content">
                    <div class="container">
                        <div class="hp-slide-caption">
                            @if($index === 0)
                                <h1 class="hp-slide-title">{{ $slide->title }}</h1>
                            @else
                                <h2 class="hp-slide-title">{{ $slide->title }}</h2>
                            @endif
                            @if($slide->has_button)
                                <a href="{{ $slide->button_link }}" class="hp-cta">
                                    {{ $slide->button_text }}
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                            <a href="{{ route('auth.login') }}" class="hp-cta hp-cta-ghost">Staff login</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    @else
    <div style="height:min(78vh,760px); min-height:480px; position:relative;">
        <div class="hp-slide-media" style="background:
            radial-gradient(circle at 70% 30%, rgba(196,174,106,0.35), transparent 35%),
            linear-gradient(135deg, #0f2416 0%, #2f8a3c 100%);"></div>
        <div class="hp-slide-overlay"></div>
        <div class="hp-slide-content">
            <div class="container">
                <div class="hp-slide-caption">
                    <h1 class="hp-slide-title">{{ $homepageContent['default_hero_title'] }}</h1>
                    <a href="{{ route('auth.login') }}" class="hp-cta">Staff login <i class="fas fa-arrow-right"></i></a>
                    <a href="{{ route('public.about') }}" class="hp-cta hp-cta-ghost">About</a>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

<!-- Mission & Vision -->
<section class="hp-split-wrap hp-reveal">
    <div class="hp-split-media"
         @if(!empty($homepageImages['mission_image_url']))
             style="background-image: url('{{ $homepageImages['mission_image_url'] }}');"
         @endif>
    </div>
    <div class="hp-split">
        <div class="hp-split-panel">
            <h3>{{ $homepageContent['organization_mission_title'] }}</h3>
            <p>{{ $homepageContent['organization_mission_text'] }}</p>
        </div>
        <div class="hp-split-panel">
            <h3>{{ $homepageContent['organization_vision_title'] }}</h3>
            <p>{{ $homepageContent['organization_vision_text'] }}</p>
        </div>
    </div>
</section>

@if($galleryItems->isNotEmpty())
<!-- Admin-managed gallery -->
<section class="hp-section">
    <div class="container">
        <div class="text-center hp-reveal">
            <h2 class="hp-section-title">At the Western RCC</h2>
            <p class="hp-section-lead">Moments from our regional work across West Africa.</p>
        </div>
        <div class="hp-gallery hp-reveal">
            @foreach($galleryItems as $item)
                <figure class="hp-gallery-item">
                    <img src="{{ $item['url'] }}" alt="{{ $item['caption'] ?: 'Western RCC gallery image' }}" loading="lazy">
                    @if($item['caption'])
                        <figcaption>{{ $item['caption'] }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- AHSS Agenda -->
<section class="hp-section hp-ahss">
    <div class="container">
        <div class="text-center hp-reveal">
            <h2 class="hp-section-title">{{ $homepageContent['ahss_title'] }}</h2>
            <p class="hp-section-lead">{{ $homepageContent['ahss_lead'] }}</p>
            <a class="hp-affiliate-link mb-4"
               href="{{ $homepageContent['ahss_link_url'] }}"
               target="_blank"
               rel="noopener noreferrer">
                {{ $homepageContent['ahss_link_label'] }}
                <i class="fas fa-external-link-alt fa-xs"></i>
            </a>
        </div>
        <div class="hp-ahss-grid hp-reveal">
            @foreach($homepageContent['ahss_pillars'] as $pillar)
                <article class="hp-ahss-pillar">
                    <span class="num">{{ $pillar['number'] }}</span>
                    <h4>{{ $pillar['title'] }}</h4>
                    <p>{{ $pillar['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<!-- Focus areas -->
<section class="hp-section">
    <div class="container">
        <div class="text-center hp-reveal">
            <h2 class="hp-section-title">{{ $homepageContent['mission_title'] }}</h2>
            <p class="hp-section-lead">{{ $homepageContent['mission_description'] }}</p>
        </div>
        <div class="hp-focus-grid hp-reveal">
            @foreach($homepageContent['mission_cards'] as $card)
                <article class="hp-focus-item">
                    <h4>{{ $card['title'] }}</h4>
                    <p>{{ $card['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<!-- Countries -->
<section class="hp-section hp-countries">
    <div class="container">
        <div class="text-center hp-reveal">
            <h2 class="hp-section-title">{{ $homepageContent['serving_title'] }}</h2>
            <p class="hp-section-lead">{{ $homepageContent['serving_description'] }}</p>
        </div>

        <div class="hp-country-grid hp-reveal">
            @forelse($countries as $country)
                <div class="hp-country">
                    <img src="{{ $country->flag_url }}" alt="{{ $country->name }} flag" loading="lazy">
                    <strong>{{ $country->name }}</strong>
                    <span>{{ $country->official_name }}</span>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">No countries configured yet.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured events -->
@if($featuredEvents->count() > 0)
<section class="hp-section">
    <div class="container">
        <div class="text-center hp-reveal">
            <h2 class="hp-section-title">{{ $homepageContent['featured_events_title'] }}</h2>
            <p class="hp-section-lead">{{ $homepageContent['featured_events_description'] }}</p>
        </div>

        <div class="row g-4 hp-reveal">
            @foreach($featuredEvents as $event)
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('public.events.show', $event) }}" class="hp-event">
                        <div class="hp-event-media"
                             @if($event->featured_image)
                                 style="background-image: url('{{ $event->featured_image_url }}');"
                             @endif>
                        </div>
                        <div class="hp-event-body">
                            <div class="hp-event-meta">
                                {{ $event->formatted_date_range }}
                                @if($event->location)
                                    · {{ $event->location }}
                                @endif
                            </div>
                            <h3>{{ $event->title }}</h3>
                            <p>{{ $event->summary ?: Str::limit($event->description, 110) }}</p>
                            <span class="hp-link">View details →</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('public.events') }}" class="hp-cta" style="background: var(--hp-forest); color: #fff;">
                View all events <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Africa CDC affiliate -->
<section class="hp-affiliate">
    <div class="container">
        <div class="hp-affiliate-inner hp-reveal">
            <p>
                <strong>Guided by the AHSS Agenda.</strong>
                Western RCC works under Africa CDC to advance Africa’s Health Security and Sovereignty Agenda across West Africa.
            </p>
            <a class="hp-affiliate-link"
               href="{{ setting('contact_website') ?: 'https://africacdc.org' }}"
               target="_blank"
               rel="noopener noreferrer">
                Visit africacdc.org <i class="fas fa-external-link-alt fa-xs"></i>
            </a>
        </div>
    </div>
</section>

<!-- Core values -->
<section class="hp-section hp-values">
    <div class="container">
        <div class="text-center hp-reveal">
            <h2 class="hp-section-title">{{ $homepageContent['core_values_title'] }}</h2>
            <p class="hp-section-lead">{{ $homepageContent['core_values_description'] }}</p>
        </div>
        <div class="row g-4 hp-reveal">
            @foreach($homepageContent['core_values_cards'] as $card)
                <div class="col-lg-4">
                    <article class="hp-value-item">
                        <h4>{{ $card['title'] }}</h4>
                        <p>{{ $card['text'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(() => {
    const nav = document.getElementById('siteNavbar');
    const onScroll = () => {
        if (!nav) return;
        nav.classList.toggle('is-scrolled', window.scrollY > 40);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    const reveals = document.querySelectorAll('.hp-reveal');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.16, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach((el) => io.observe(el));
    } else {
        reveals.forEach((el) => el.classList.add('is-visible'));
    }
})();
</script>
@endsection
