@extends('layouts.auth', ['title' => 'Login'])

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
                                    <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="logo logo-light">
                                        <span class="logo-lg">
                                            <img src="{{ asset('images/logo-login.png') }}" alt="شعار الدخول" height="36">
                                        </span>
                                    </a>
                                    <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="logo logo-dark">
                                        <span class="logo-lg">
                                            <img src="{{ asset('images/logo-login.png') }}" alt="شعار الدخول" height="36">
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <div class="auth-title-section mb-4 text-center">
                                <h3 class="text-primary fw-semibold mb-2">تسجيل الدخول</h3>
                                <p class="text-muted fs-14 mb-0">أدخل اسم المستخدم وكلمة المرور للدخول إلى النظام</p>
                            </div>

                            <div class="pt-0">
                                <form method="POST" action="{{ url(env('APP_URL_PREFIX', 'xK9wR2vP8nL4tY6zA5bM3cH0jG7eF1dQ') . '/k2m7n3p8') }}" class="my-4">
                                    @csrf
                                    @if (sizeof($errors) > 0)
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        @foreach ($errors->all() as $error)
                                        <p class="mb-0">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                    @endif

                                    <div class="form-group mb-3">
                                        <label for="username" class="form-label fw-semibold">اسم المستخدم</label>
                                        <input class="form-control form-control-lg" type="text" name="email" id="username" required placeholder="أدخل اسم المستخدم" autofocus>
                                    </div>
        
                                    <div class="form-group mb-4">
                                        <label for="password" class="form-label fw-semibold">كلمة المرور</label>
                                        <input class="form-control form-control-lg" type="password" required id="password" name="password" placeholder="أدخل كلمة المرور">
                                    </div>
                                    
                                    <div class="form-group mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary btn-lg fw-semibold" type="submit">
                                                <i class="ri-login-box-line me-1"></i> تسجيل الدخول
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <a href="{{ route('i5j1k6l9.m3n8o2p7') }}" class="text-primary">هل نسيت كلمة المرور؟</a>
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