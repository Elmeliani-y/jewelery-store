@extends('layouts.auth', ['title' => 'تعيين كلمة مرور جديدة'])

@section('content')
<div class="col-xl-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="mb-0 p-0 p-lg-3">
                        <div class="mb-0 border-0 p-md-4 p-lg-0">
                            <div class="mb-3 p-0 text-center">
                                <div class="auth-brand">
                                    <a href="{{ route('login') }}" class="logo logo-light">
                                        <span class="logo-lg">
                                            <img src="{{ asset('images/logo-login.png') }}" alt="شعار الدخول" height="36">
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="auth-title-section mb-4 text-center">
                                <h3 class="text-primary fw-semibold mb-2">تعيين كلمة مرور جديدة</h3>
                                <p class="text-muted fs-14 mb-0">أدخل كلمة مرور جديدة لحسابك</p>
                            </div>
                            <div class="pt-0">
                                <form method="POST" action="{{ route('password.update') }}" class="my-4">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                                        <input class="form-control form-control-lg" type="email" name="email" id="email" required autofocus placeholder="أدخل بريدك الإلكتروني" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label fw-semibold">كلمة المرور الجديدة</label>
                                        <input class="form-control form-control-lg" type="password" name="password" id="password" required placeholder="أدخل كلمة المرور الجديدة">
                                        @error('password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>
                                        <input class="form-control form-control-lg" type="password" name="password_confirmation" id="password_confirmation" required placeholder="أعد إدخال كلمة المرور">
                                    </div>
                                    <div class="form-group mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary btn-lg fw-semibold" type="submit">
                                                <i class="ri-lock-password-line me-1"></i> تعيين كلمة المرور
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="text-secondary">العودة لتسجيل الدخول</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-7 d-none d-xl-inline-block">
    <div class="account-page-bg rounded-4">
        <div class="text-center">
            <div class="auth-image">
                <img src="{{ asset('images/logo-login.png') }}" class="mx-auto img-fluid" alt="شعار النظام">
            </div>
        </div>
    </div>
</div>
@endsection
