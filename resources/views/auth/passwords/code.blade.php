
@extends('layouts.auth', ['title' => 'إدخال رمز الاستعادة'])
@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" dir="rtl" style="text-align:right;">
    <div class="row w-100 justify-content-center align-items-center">
        <div class="col-xl-5 d-flex align-items-center justify-content-center">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <div class="mb-0 p-0 p-lg-3">
                            <div class="mb-0 border-0 p-md-4 p-lg-0">
                                <div class="auth-title-section mb-4 text-center">
                                    <h3 class="text-primary fw-semibold mb-2">أدخل رمز الاستعادة</h3>
                                </div>
                                @if($errors->any())
                                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                                @endif
                                <form method="POST" action="{{ route('password.code.verify') }}">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="code" class="form-label fw-semibold">رمز الاستعادة</label>
                                        <input type="text" class="form-control form-control-lg" id="code" name="code" required maxlength="6">
                                    </div>
                                    <div class="form-group mb-0 text-end">
                                        <button type="submit" class="btn btn-primary btn-lg fw-semibold">تأكيد الرمز</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
