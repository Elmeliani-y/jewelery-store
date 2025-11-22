@extends('layouts.maintenance', ['title' => 'Maintenance'])

@section('content')

<div class="col-md-5 mx-auto">
    <div class="maintenance-img text-center">
        <img src="/images/svg/maintenance.svg" class="img-fluid" alt="maintenance-image">
    </div>

    <div class="text-center mt-4">
        <h3 class="mt-0 fw-semibold text-primary text-uppercase display-6 mb-3">Maintenance</h3>
        <h5 class="fs-15 text-dark mb-4 maintaince-title">Our site is currently maintenance We will be back shortly <br> Thank you for patience</h5>
        <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="btn btn-primary mb-4">Go Back to Home</a>
    </div>
</div>
<!-- container-fluid -->
@endsection