<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Portal') - RCC Staff Management</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <style>
        :root {
            --africa-green: #348F41;
            --africa-gold: #B4A269;
            --africa-red: #782C2D;
            --africa-yellow: #E08F2A;
            --africa-brown: #6B4C24;
            --africa-orange: #C45B39;
        }

        .main-header .navbar {
            background-color: var(--africa-green) !important;
        }

        .main-sidebar {
            background-color: var(--africa-green) !important;
        }

        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: var(--africa-gold);
            color: #fff;
        }

        .btn-primary {
            background-color: var(--africa-green);
            border-color: var(--africa-green);
        }

        .btn-primary:hover {
            background-color: var(--africa-gold);
            border-color: var(--africa-gold);
        }

        .card-primary.card-outline {
            border-top: 3px solid var(--africa-green);
        }

        .info-box-icon {
            background-color: var(--africa-green) !important;
        }

        .attendance-card {
            border-left: 4px solid var(--africa-green);
        }

        .clock-button {
            min-height: 60px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .clock-in-btn {
            background-color: var(--africa-green);
            border-color: var(--africa-green);
        }

        .clock-out-btn {
            background-color: var(--africa-red);
            border-color: var(--africa-red);
        }

        .status-present {
            color: var(--africa-green);
            font-weight: bold;
        }

        .status-absent {
            color: var(--africa-red);
            font-weight: bold;
        }

        .loading-spinner {
            display: none;
        }

        .loading-spinner.show {
            display: inline-block;
        }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    {{ Auth::guard('staff')->user()->first_name }}
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item-text">{{ Auth::guard('staff')->user()->full_name }}</span>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('staff.profile') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('staff.dashboard') }}" class="brand-link">
            <img src="{{ asset('images/logos/logo.png') }}" alt="Africa CDC Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">RCC Staff Portal</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('staff.attendance.index') }}" class="nav-link {{ request()->routeIs('staff.attendance.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>Attendance</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('staff.attendance.history') }}" class="nav-link {{ request()->routeIs('staff.attendance.history') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Attendance History</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('staff.tracker.index') }}" class="nav-link {{ request()->routeIs('staff.tracker.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Weekly Tracker</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('staff.calendar.index') }}" class="nav-link {{ request()->routeIs('staff.calendar.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Activity Calendar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('staff.activity-requests.index') }}" class="nav-link {{ request()->routeIs('staff.activity-requests.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-plus"></i>
                            <p>Activity Requests</p>
                        </a>
                    </li>
                    @if(auth()->guard('staff')->user()->is_admin && auth()->guard('staff')->user()->email !== 'admin@africacdc.org')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Admin Panel</p>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Profile</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="https://africacdc.org">Africa CDC</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>

<!-- jQuery -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

<script>
    // Global CSRF token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
