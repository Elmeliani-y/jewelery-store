@extends('layouts.vertical', ['title' => 'إضافة فرع'])
@section('title','إضافة فرع')
@section('css')
<style>.form-card{border-radius:14px;border:1px solid var(--bs-border-color);}</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="page-header mb-4 d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <a href="{{ route('x9y4z1a6.index') }}" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
        <h5 class="mb-0"><iconify-icon icon="solar:add-circle-bold-duotone" class="fs-4 me-1"></iconify-icon> إضافة فرع جديد</h5>
    </div>

    <form action="{{ route('x9y4z1a6.store') }}" method="POST">@csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon> بيانات الفرع</h6>
                    <div class="mb-3">
                        <label class="form-label">اسم الفرع <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active',true)?'checked':'' }}>
                        <label class="form-check-label" for="is_active">مفعل</label>
                    </div>
                </div></div>
            </div>
            <div class="col-lg-4">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon> ملاحظات</h6>
                    <p class="text-muted small mb-0">تأكد من صحة البيانات خاصة الاسم حيث يستخدم في التقارير.</p>
                </div></div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" type="submit"><iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> حفظ الفرع</button>
                    <a href="{{ route('x9y4z1a6.index') }}" class="btn btn-light btn-lg"><iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon> إلغاء</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection