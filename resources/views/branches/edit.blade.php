@extends('layouts.vertical', ['title' => 'تعديل فرع'])
@section('css')<style>.form-card{border-radius:14px;border:1px solid var(--bs-border-color);}</style>@endsection
@section('content')
<div class="container-fluid">
    <div class="page-header mb-4 d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <a href="{{ route('branches.show',$branch) }}" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
        <h5 class="mb-0"><iconify-icon icon="solar:pen-bold-duotone" class="fs-4 me-1"></iconify-icon> تعديل الفرع: {{ $branch->name }}</h5>
    </div>

    <form action="{{ route('branches.update',$branch) }}" method="POST">@csrf @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon> بيانات الفرع</h6>
                    <div class="mb-3">
                        <label class="form-label">اسم الفرع <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$branch->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address',$branch->address) }}">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone',$branch->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active',$branch->is_active)?'checked':'' }}>
                        <label class="form-check-label" for="is_active">مفعل</label>
                    </div>
                </div></div>
            </div>
            <div class="col-lg-4">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon> ملاحظات</h6>
                    <p class="text-muted small mb-0">تعطيل الفرع يجعله غير متاح للاختيار مستقبلاً دون حذف البيانات السابقة.</p>
                </div></div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" type="submit"><iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> حفظ التعديلات</button>
                    <a href="{{ route('branches.show',$branch) }}" class="btn btn-light btn-lg"><iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon> إلغاء</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection