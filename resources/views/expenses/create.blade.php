@extends('layouts.vertical', ['title' => 'تسجيل مصروف جديد'])
@section('title')
    تسجيل مصروف جديد
@endsection

@section('css')
<style>
    .form-section {
        background: var(--ct-card-bg);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid var(--ct-border-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .form-section::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, var(--ct-primary), var(--ct-info));
    }
    
    [data-bs-theme="dark"] .form-section {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .section-header {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ct-heading-color);
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 16px;
        border: 1.5px solid var(--ct-border-color);
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--ct-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--ct-primary-rgb), 0.15);
    }
    
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: var(--ct-body-color);
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--ct-body-color);
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تسجيل مصروف جديد</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">المصروفات</a></li>
                        <li class="breadcrumb-item active">تسجيل جديد</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <iconify-icon icon="solar:danger-circle-bold" class="fs-5 me-2"></iconify-icon>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Main Information -->
                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                        معلومات المصروف
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">
                                الفرع <span class="text-danger">*</span>
                            </label>
                            <select name="branch_id" 
                                    id="branch_id" 
                                    class="form-select @error('branch_id') is-invalid @enderror"
                                    @if(isset($selectedBranchId)) disabled @endif
                                    required>
                                <option value="">اختر الفرع</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" 
                                            @if(old('branch_id', $selectedBranchId ?? null) == $branch->id) selected @endif>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if(isset($selectedBranchId))
                                <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                            @endif
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expense_type_id" class="form-label">
                                نوع المصروف <span class="text-danger">*</span>
                            </label>
                            <select name="expense_type_id" 
                                    id="expense_type_id" 
                                    class="form-select @error('expense_type_id') is-invalid @enderror"
                                    required>
                                <option value="">اختر نوع المصروف</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->id }}" @if(old('expense_type_id') == $type->id) selected @endif>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                المبلغ (جنيه) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="amount" 
                                   id="amount" 
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   step="0.01"
                                   min="0.01"
                                   placeholder="0.00"
                                   required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expense_date" class="form-label">
                                تاريخ المصروف <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="expense_date" 
                                   id="expense_date" 
                                   class="form-control @error('expense_date') is-invalid @enderror"
                                   value="{{ old('expense_date', date('Y-m-d')) }}"
                                   required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">
                                الوصف <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="description" 
                                   id="description" 
                                   class="form-control @error('description') is-invalid @enderror"
                                   value="{{ old('description') }}"
                                   placeholder="وصف المصروف"
                                   required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Action Buttons -->
                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                        الإجراءات
                    </h5>

                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <iconify-icon icon="solar:check-circle-bold" class="fs-5 me-2"></iconify-icon>
                            حفظ المصروف
                        </button>

                        <a href="{{ route('expenses.index') }}" class="btn btn-light btn-lg">
                            <iconify-icon icon="solar:close-circle-bold" class="fs-5 me-2"></iconify-icon>
                            إلغاء
                        </a>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-3">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-5 me-2"></iconify-icon>
                    <strong>تنبيه:</strong> تأكد من إدخال جميع البيانات بدقة. سيتم تسجيل المصروف مباشرة في النظام.
                </div>
            </div>
        </div>
    </form>

</div>
@endsection
