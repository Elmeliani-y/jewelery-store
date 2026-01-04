@extends('layouts.vertical')
@section('title', 'إضافة صنف جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0">إضافة صنف جديد</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('v5w9x4y1.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">اسم الصنف <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">العيار الافتراضي</label>
                            <select name="default_caliber_id" class="form-select @error('default_caliber_id') is-invalid @enderror">
                                @foreach($calibers as $caliber)
                                    <option value="{{ $caliber->id }}" {{ old('default_caliber_id', $caliber->name == '21' ? $caliber->id : null) == $caliber->id ? 'selected' : '' }}>
                                        {{ $caliber->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('default_caliber_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">سيتم اختيار هذا العيار تلقائياً عند إضافة مبيعة لهذا الصنف</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">نشط</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                                حفظ
                            </button>
                            <a href="{{ route('v5w9x4y1.index') }}" class="btn btn-secondary">
                                <iconify-icon icon="solar:close-circle-bold-duotone" class="me-1"></iconify-icon>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
