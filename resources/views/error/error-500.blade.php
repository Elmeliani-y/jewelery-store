@extends('layouts.error', ['title' => 'Error 500'])

@section('content')

<div class="row">
    <div class="col-md-4 mx-auto">

        <div class="card overflow-hidden text-center">
            <div class="card-body">

                <div class="mb-4 p-0 text-center">
                    <div class="auth-brand">
                        <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="logo logo-light">
                            <span class="logo-lg">
                                <img src="/images/logo-light.png" alt="" height="24">
                            </span>
                        </a>
                        <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="logo logo-dark">
                            <span class="logo-lg">
                                <img src="/images/logo-dark.png" alt="" height="24">
                            </span>
                        </a>
                    </div>
                </div>

                <div class="text-center">
                    <h3 class="fw-semibold text-primary lh-base">Internal Server Error</h3>
                    <h4 class="fw-semibold mt-2 text-dark lh-base fs-18">Something's Missing.....! Internal Server Error</h4>
                    <p class="text-muted text-center mb-0">Our internal server has gone on a uninformed vacation</p>

                    <div class="error-page my-4">
                        <img src="/images/svg/500-error.png" class="img-fluid" alt="coming-soon">
                    </div>

                    <a class="btn btn-primary mt-0" href="{{ route('second', [ 'dashboard' , 'index']) }}">Back to Home</a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection