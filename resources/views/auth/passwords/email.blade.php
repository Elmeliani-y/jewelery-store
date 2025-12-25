@extends('layouts.auth', ['title' => 'استعادة كلمة المرور'])
@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" dir="rtl" style="text-align:right;">
    <div class="row w-100">
        <div class="col-xl-5 d-flex align-items-center justify-content-center">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <div class="mb-0 p-0 p-lg-3">
                            <div class="mb-0 border-0 p-md-4 p-lg-0">
                                <div class="mb-3 p-0 text-center">
                                    <div class="auth-brand">
                                        <span class="logo-lg">
                                            <img src="{{ asset('images/logo-login.png') }}" alt="شعار الدخول" height="36">
                                        </span>
                                    </div>
                                </div>
                                <div class="auth-title-section mb-4 text-center">
                                    <h3 class="text-primary fw-semibold mb-2">استعادة كلمة المرور برمز</h3>
                                    <p class="text-light fs-14 mb-0">
                                        @if(!session('show_code_form'))
                                            أدخل بريدك الإلكتروني لإرسال رمز استعادة كلمة المرور.
                                        @else
                                            تم إرسال رمز الاستعادة إلى بريدك الإلكتروني:
                                            <br><strong>{{ session('email') }}</strong>
                                        @endif
                                    </p>
                                </div>
                                <div class="pt-0">
                                    @if(session('status'))
                                        <div class="alert alert-success">{{ session('status') }}</div>
                                    @endif
                                    @if(!session('show_code_form'))
                                        <form method="POST" action="{{ route('password.code.request') }}">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                                                <input type="email" class="form-control form-control-lg" id="email" name="email" required placeholder="أدخل بريدك الإلكتروني">
                                            </div>
                                            <div class="form-group mb-0">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">إرسال الرمز</button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('password.code.verify') }}">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ session('email') }}">
                                            <div class="form-group mb-3">
                                                <label for="code" class="form-label fw-semibold">رمز الاستعادة</label>
                                                <input type="text" class="form-control form-control-lg" id="code" name="code" required maxlength="6" placeholder="أدخل الرمز المرسل">
                                            </div>
                                            <div class="form-group mb-0">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">تأكيد الرمز</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 d-none d-xl-flex align-items-center justify-content-center">
            <div class="account-page-bg rounded-4">
                <div class="text-center">
                    <div class="auth-image">
                        <img src="{{ asset('images/logo-login.png') }}" class="mx-auto img-fluid" alt="شعار النظام" style="max-width: 400px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
