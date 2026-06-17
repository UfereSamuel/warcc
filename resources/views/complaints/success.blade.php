@extends('layouts.public')

@section('title', 'Complaint Submitted')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Complaint Submitted Successfully</h2>
                    
                    <p class="lead">Thank you for bringing this matter to our attention.</p>
                    
                    <div class="alert alert-info my-4">
                        <h5 class="mb-3">Your Complaint Number:</h5>
                        <h3 class="mb-0">
                            <strong>{{ $complaint->complaint_number }}</strong>
                        </h3>
                        <small class="text-muted d-block mt-2">Please save this number for your records</small>
                    </div>

                    @if($complaint->is_anonymous)
                        <div class="alert alert-success">
                            <i class="fas fa-user-secret"></i>
                            <strong>Anonymous Submission</strong>
                            <p class="mb-0 mt-2">Your identity remains completely confidential.</p>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-envelope"></i>
                            <strong>Identified Submission</strong>
                            <p class="mb-0 mt-2">We may contact you at <strong>{{ $complaint->complainant_email }}</strong> if we need more information.</p>
                        </div>
                    @endif

                    <hr class="my-4">

                    <h5 class="mb-3">What Happens Next?</h5>
                    <ul class="text-left d-inline-block text-muted">
                        <li class="mb-2">Your complaint will be reviewed by authorized personnel</li>
                        <li class="mb-2">All information will be handled with strict confidentiality</li>
                        <li class="mb-2">Appropriate action will be taken based on the findings</li>
                        @if(!$complaint->is_anonymous)
                        <li class="mb-2">You may be contacted for additional information if needed</li>
                        @endif
                    </ul>

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home"></i> Return to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


