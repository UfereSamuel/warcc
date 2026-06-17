<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login - Africa CDC Western RCC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #348F41;
            --gold: #B4A269;
            --brown: #6B4C24;
            --light-brown: rgba(107, 76, 36, 0.1);
            --light-gold: rgba(180, 162, 105, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            height: 100vh;
        }

        /* Background with building image */
        .login-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('images/backgroundimages/africa-cdc-building.png') }}') center/cover no-repeat;
            z-index: -2;
        }

        /* Overlay for better contrast - darker for admin */
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                135deg,
                rgba(107, 76, 36, 0.9) 0%,
                rgba(52, 143, 65, 0.8) 50%,
                rgba(107, 76, 36, 0.9) 100%
            );
            z-index: -1;
        }

        /* Particle canvas */
        #particles-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .login-container {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow:
                0 30px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 480px;
            width: 100%;
            animation: slideUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--brown), var(--primary-green));
            color: white;
            padding: 50px 30px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: shimmer 4s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
            50% { transform: translate(-50%, -50%) rotate(180deg); }
        }

        .login-header img {
            width: 90px;
            height: 90px;
            margin-bottom: 20px;
            background: white;
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .login-header img:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .login-header h4 {
            margin-bottom: 8px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 40px 35px;
        }

        .welcome-text h5 {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--brown);
            box-shadow: 0 0 0 0.2rem rgba(107, 76, 36, 0.25);
            background: white;
            transform: translateY(-1px);
        }

        .btn-admin {
            background: linear-gradient(135deg, var(--brown), #5a3f1e);
            border: none;
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            width: 100%;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(107, 76, 36, 0.3);
        }

        .btn-admin::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-admin:hover::before {
            left: 100%;
        }

        .btn-admin:hover {
            background: linear-gradient(135deg, #5a3f1e, var(--brown));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107, 76, 36, 0.4);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 16px;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-1px);
        }

        .back-link {
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .back-link:hover {
            color: var(--brown);
            text-decoration: none;
            transform: translateX(-3px);
        }

        .alert {
            border: none;
            border-radius: 10px;
            background: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            color: #dc3545;
            margin-top: 5px;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Security badge animation */
        .security-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(107, 76, 36, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Floating animation for card */
        .login-card {
            animation: slideUp 0.8s ease-out, float 6s ease-in-out infinite 1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-card {
                margin: 10px;
            }

            .login-header {
                padding: 40px 25px 30px;
            }

            .login-body {
                padding: 30px 25px;
            }

            .security-badge {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Background with building image -->
    <div class="login-background"></div>
    <div class="background-overlay"></div>

    <!-- Particles canvas -->
    <canvas id="particles-canvas"></canvas>

    <div class="login-container">
        <div class="login-card">
            <div class="security-badge">
                <i class="fas fa-shield-alt me-1"></i>SECURE
            </div>
            <div class="login-header">
                <img src="{{ asset('images/logos/logo.png') }}" alt="Africa CDC Logo">
                <h4 class="mb-0">Administrator Portal</h4>
                <p class="mb-0">Africa CDC Western RCC</p>
            </div>
            <div class="login-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="text-center mb-4 welcome-text">
                    <h5>Administrator Access</h5>
                    <p>Enter your credentials to continue</p>
                </div>

                <form method="POST" action="{{ route('auth.admin.login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required
                               placeholder="admin@africacdc.org">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required
                               placeholder="Enter your password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-admin mb-4">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </button>
                </form>

                <div class="text-center mb-3">
                    <a href="{{ route('auth.login') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-users me-2"></i>Staff Login
                    </a>
                </div>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="back-link">
                        <i class="fas fa-arrow-left me-2"></i>Back to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced particle system for admin with different colors
        class AdminParticleSystem {
            constructor() {
                this.canvas = document.getElementById('particles-canvas');
                this.ctx = this.canvas.getContext('2d');
                this.particles = [];
                this.particleCount = 70;

                this.resize();
                this.init();
                this.animate();

                window.addEventListener('resize', () => this.resize());
            }

            resize() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
            }

            init() {
                for (let i = 0; i < this.particleCount; i++) {
                    this.particles.push({
                        x: Math.random() * this.canvas.width,
                        y: Math.random() * this.canvas.height,
                        vx: (Math.random() - 0.5) * 0.4,
                        vy: (Math.random() - 0.5) * 0.4,
                        radius: Math.random() * 4 + 1,
                        opacity: Math.random() * 0.8 + 0.2,
                        color: Math.random() > 0.6 ? 'rgba(107, 76, 36, 0.7)' :
                               Math.random() > 0.3 ? 'rgba(52, 143, 65, 0.6)' : 'rgba(180, 162, 105, 0.5)',
                        pulse: Math.random() * Math.PI * 2,
                        pulseSpeed: 0.02 + Math.random() * 0.02
                    });
                }
            }

            animate() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                // Update and draw particles
                this.particles.forEach((particle, index) => {
                    // Update position
                    particle.x += particle.vx;
                    particle.y += particle.vy;
                    particle.pulse += particle.pulseSpeed;

                    // Bounce off edges
                    if (particle.x < 0 || particle.x > this.canvas.width) particle.vx *= -1;
                    if (particle.y < 0 || particle.y > this.canvas.height) particle.vy *= -1;

                    // Keep particles in bounds
                    particle.x = Math.max(0, Math.min(this.canvas.width, particle.x));
                    particle.y = Math.max(0, Math.min(this.canvas.height, particle.y));

                    // Pulsing effect
                    const pulseRadius = particle.radius * (1 + Math.sin(particle.pulse) * 0.3);

                    // Draw particle
                    this.ctx.beginPath();
                    this.ctx.arc(particle.x, particle.y, pulseRadius, 0, Math.PI * 2);
                    this.ctx.fillStyle = particle.color;
                    this.ctx.fill();

                    // Draw glow effect
                    this.ctx.beginPath();
                    this.ctx.arc(particle.x, particle.y, pulseRadius * 2, 0, Math.PI * 2);
                    this.ctx.fillStyle = particle.color.replace(/[\d\.]+\)$/g, '0.1)');
                    this.ctx.fill();

                    // Draw connections to nearby particles
                    this.particles.slice(index + 1).forEach(otherParticle => {
                        const dx = particle.x - otherParticle.x;
                        const dy = particle.y - otherParticle.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);

                        if (distance < 120) {
                            this.ctx.beginPath();
                            this.ctx.moveTo(particle.x, particle.y);
                            this.ctx.lineTo(otherParticle.x, otherParticle.y);
                            this.ctx.strokeStyle = `rgba(255, 255, 255, ${0.15 * (1 - distance / 120)})`;
                            this.ctx.lineWidth = 0.5;
                            this.ctx.stroke();
                        }
                    });
                });

                requestAnimationFrame(() => this.animate());
            }
        }

        // Initialize particle system when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new AdminParticleSystem();
        });

        // Enhanced mouse interaction for admin
        document.addEventListener('mousemove', (e) => {
            const loginCard = document.querySelector('.login-card');
            const rect = loginCard.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            const rotateX = (y / rect.height) * 8;
            const rotateY = -(x / rect.width) * 8;

            loginCard.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(0)`;
        });

        document.addEventListener('mouseleave', () => {
            const loginCard = document.querySelector('.login-card');
            loginCard.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0)';
        });

        // Form interaction enhancements
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Security badge click effect
        document.querySelector('.security-badge').addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'pulse 2s infinite';
            }, 10);
        });
    </script>
</body>
</html>
