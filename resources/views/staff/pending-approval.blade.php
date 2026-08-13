@extends('adminlte::page')

@section('title', 'Account Pending Approval')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12 text-center">
                <h1>
                    <i class="fas fa-user-clock text-warning mr-2"></i>
                    Account Pending Verification
                </h1>
                <p class="text-muted lead mb-0">
                    Welcome to Western RCC Staff Portal, {{ $staff->full_name }}!
                </p>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <!-- Main Pending Card -->
        <div class="card card-warning card-outline elevation-2">
            <div class="card-header text-center bg-light">
                <h3 class="card-title w-100 font-weight-bold">
                    <i class="fas fa-shield-alt text-warning mr-2"></i>
                    Administrator Approval Required
                </h3>
            </div>
            <div class="card-body">
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-info-circle"></i> {{ session('info') }}
                    </div>
                @endif

                <div class="text-center my-4">
                    <div class="mb-3">
                        <span class="fa-stack fa-3x">
                            <i class="fas fa-circle fa-stack-2x text-warning-light"></i>
                            <i class="fas fa-user-clock fa-stack-1x text-warning"></i>
                        </span>
                    </div>
                    <h5 class="font-weight-bold">Verification in Progress</h5>
                    <p class="text-muted px-3">
                        Your account was authenticated via Microsoft SSO. To ensure secure access for Africa CDC Western RCC personnel, an administrator must verify your staff status before portal access is activated.
                    </p>
                </div>

                <div class="bg-light p-3 rounded border mb-4">
                    <h6 class="font-weight-bold text-secondary mb-3">
                        <i class="fas fa-id-card mr-2"></i>Registration Details
                    </h6>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Full Name:</div>
                        <div class="col-sm-8 font-weight-bold">{{ $staff->full_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Email Address:</div>
                        <div class="col-sm-8 font-weight-bold">{{ $staff->email }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Staff ID:</div>
                        <div class="col-sm-8 font-weight-bold"><code>{{ $staff->staff_id }}</code></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-muted">Current Status:</div>
                        <div class="col-sm-8">
                            <span class="badge badge-warning px-2 py-1">
                                <i class="fas fa-clock mr-1"></i> Pending Admin Approval
                            </span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light border border-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-info fa-2x mr-3"></i>
                        <div>
                            <strong>What happens next?</strong><br>
                            <span class="small text-muted">Once a Western RCC Administrator reviews and approves your account, you can refresh this page or sign in again to access your staff dashboard and complete your setup.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <a href="{{ route('staff.pending-approval') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Status
                </a>

                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center text-muted small mt-3">
            Africa CDC Western Regional Collaborating Centre &bull; WARCC Portal
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .text-warning-light {
        color: #fff3cd;
    }
</style>
@stop
