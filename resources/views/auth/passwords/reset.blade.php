
@extends('layouts.auth', ['title' => 'إعادة تعيين كلمة المرور'])
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
                                    <h3 class="text-primary fw-semibold mb-2">إعادة تعيين كلمة المرور</h3>
                                </div>
                                @if($errors->any())
                                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                                @endif
                                <form method="POST" action="{{ route('i5j1k6l9.y1z6a4b9') }}">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <input type="hidden" name="code" value="{{ $code }}">
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label fw-semibold">كلمة المرور الجديدة</label>
                                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>
                                        <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                    <div class="form-group mb-0 text-end">
                                        <button type="submit" class="btn btn-primary btn-lg fw-semibold">تغيير كلمة المرور</button>
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
