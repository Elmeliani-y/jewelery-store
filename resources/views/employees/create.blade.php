@extends('layouts.vertical', ['title' => ' إضافة موظف جديد'])
@section('css')<style>.form-card{border:1px solid var(--bs-border-color);border-radius:14px;}</style>@endsection
@section('content')
<div class="container-fluid">
    <div class="page-header mb-4 d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#198754,#20c997);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <a href="{{ route('employees.index') }}" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
        <h5 class="mb-0"><iconify-icon icon="solar:add-circle-bold-duotone" class="fs-4 me-1"></iconify-icon> إضافة موظف جديد</h5>
    </div>

    <form action="{{ route('employees.store') }}" method="POST">@csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon> البيانات الأساسية</h6>
                    <div class="mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الراتب (ريال) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary','0') }}" required>
                        @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الفرع <span class="text-danger">*</span></label>
                        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                            <option value="">اختر الفرع</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active',true)?'checked':'' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div></div>
            </div>
            <div class="col-lg-4">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon> ملاحظات</h6>
                    <p class="text-muted small mb-0">سيظهر الموظف في شاشة المبيعات تحت الفرع المختار إذا كان نشطاً.</p>
                </div></div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg"><iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> حفظ الموظف</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-light btn-lg"><iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon> إلغاء</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection