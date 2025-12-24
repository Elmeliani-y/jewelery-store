
@extends('layouts.app')

@section('navbar')
<!-- Remove navbar for this page -->
@stop

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        @if(isset($pendingUser) && $pendingUser)
            <div class="text-center mb-4">
                <h3 class="mb-2">توثيق الجهاز</h3>
                <p class="text-muted mb-0" style="font-size: 0.97rem;">يرجى إدخال رمز الربط الذي حصلت عليه من المسؤول لتوثيق هذا الجهاز.</p>
            </div>
            <form method="POST" action="{{ route('pair-device.pair') }}">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">رمز الربط</label>
                    <input type="text" name="code" id="code" class="form-control text-center" required autofocus autocomplete="off" style="font-size:1.2rem; letter-spacing:2px;">
                </div>
                <button type="submit" class="btn btn-success w-100">توثيق</button>
            </form>
            @if($errors->any())
                <div class="alert alert-danger mt-3 text-center">
                    {{ $errors->first('code') }}
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-success mt-3 text-center">
                    {{ session('status') }}
                </div>
            @endif
        @else
            <div class="alert alert-info text-center">
                يجب عليك تسجيل الدخول أولاً لتوثيق الجهاز.
            </div>
        @endif
    </div>
</div>
@endsection
