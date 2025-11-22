@extends('layouts.vertical', ['title' => 'تسجيل مبيعة جديدة'])

@section('css')
<style>
    .arabic-text {
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        direction: rtl;
        text-align: right;
    }
    
    /* Modern Form Section */
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
    
    .form-section:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    
    /* Dark Mode Enhancements */
    [data-bs-theme="dark"] .form-section {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    [data-bs-theme="dark"] .form-section:hover {
        background: rgba(255, 255, 255, 0.04);
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    
    /* Section Headers */
    .section-header {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ct-heading-color);
    }
    
    .section-header iconify-icon {
        font-size: 1.5rem;
        opacity: 0.9;
    }
    
    /* Form Controls Enhancement */
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 16px;
        border: 1.5px solid var(--ct-border-color);
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--ct-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--ct-primary-rgb), 0.15);
        transform: translateY(-1px);
    }
    
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: var(--ct-body-color);
    }
    
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: var(--ct-primary);
    }
    
    /* Form Labels */
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--ct-body-color);
        font-size: 0.9rem;
    }
    
    /* Payment Method Cards */
    .payment-card {
        background: var(--ct-card-bg);
        border: 2px solid var(--ct-border-color);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
    }
    
    .payment-card:hover {
        border-color: var(--ct-primary);
        background: rgba(var(--ct-primary-rgb), 0.05);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .payment-card input[type="radio"]:checked ~ .payment-content {
        color: var(--ct-primary);
    }
    
    .payment-card input[type="radio"]:checked ~ * .payment-icon {
        color: var(--ct-primary);
        transform: scale(1.1);
    }
    
    .payment-card.active {
        border-color: var(--ct-primary);
        background: rgba(var(--ct-primary-rgb), 0.1);
        box-shadow: 0 4px 12px rgba(var(--ct-primary-rgb), 0.2);
    }
    
    .payment-icon {
        font-size: 2rem;
        transition: all 0.3s ease;
    }
    
    .payment-content {
        flex: 1;
    }
    
    .payment-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 2px;
    }
    
    .payment-desc {
        font-size: 0.75rem;
        opacity: 0.7;
    }
    
    [data-bs-theme="dark"] .payment-card {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    [data-bs-theme="dark"] .payment-card:hover {
        background: rgba(255, 255, 255, 0.06);
    }
    
    [data-bs-theme="dark"] .payment-card.active {
        background: rgba(var(--ct-primary-rgb), 0.15);
    }
    
    /* Calculation Cards */
    .calc-card {
        background: var(--ct-card-bg);
        border: 2px solid var(--ct-border-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .calc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .calc-card.info {
        border-color: var(--ct-info);
        background: rgba(var(--ct-info-rgb), 0.05);
    }
    
    .calc-card.success {
        border-color: var(--ct-success);
        background: rgba(var(--ct-success-rgb), 0.05);
    }
    
    .calc-card.warning {
        border-color: var(--ct-warning);
        background: rgba(var(--ct-warning-rgb), 0.05);
    }
    
    .calc-label {
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0.8;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .calc-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }
    
    .calc-icon {
        font-size: 2.5rem;
        opacity: 0.3;
        margin-bottom: 8px;
    }
    
    [data-bs-theme="dark"] .calc-card {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    [data-bs-theme="dark"] .calc-card.info {
        background: rgba(var(--ct-info-rgb), 0.1);
    }
    
    [data-bs-theme="dark"] .calc-card.success {
        background: rgba(var(--ct-success-rgb), 0.1);
    }
    
    [data-bs-theme="dark"] .calc-card.warning {
        background: rgba(var(--ct-warning-rgb), 0.1);
    }
    
    /* Action Buttons */
    .btn {
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    
    .btn iconify-icon {
        font-size: 1.2rem;
    }
    
    /* Responsive Improvements */
    @media (max-width: 768px) {
        .form-section {
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .section-header {
            font-size: 1rem;
        }
        
        .payment-card {
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .calc-value {
            font-size: 1.2rem;
        }
        
        .calc-icon {
            font-size: 2rem;
        }
        
        .btn {
            padding: 10px 20px;
            width: 100%;
            justify-content: center;
        }
    }
    
    /* Loading State */
    .form-control.loading {
        background-image: linear-gradient(90deg, transparent, rgba(var(--ct-primary-rgb), 0.1), transparent);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Smooth transitions for show/hide */
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0 arabic-text">تسجيل مبيعة جديدة</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0 arabic-text">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-primary">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-primary">المبيعات</a></li>
                        <li class="breadcrumb-item active">تسجيل مبيعة جديدة</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title arabic-text">
                        <iconify-icon icon="solar:add-circle-bold-duotone" class="me-2"></iconify-icon>
                        بيانات المبيعة
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.store') }}" method="POST" class="arabic-text">
                        @csrf
                        
                        <!-- Branch and Employee Section -->
                        <div class="form-section">
                            <h5 class="section-header">
                                <iconify-icon icon="solar:buildings-bold-duotone"></iconify-icon>
                                معلومات الفرع والموظف
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">الفرع <span class="text-danger">*</span></label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" 
                                            id="branch_id" name="branch_id" required 
                                            {{ isset($selectedBranchId) ? 'disabled' : '' }}>
                                        <option value="">اختر الفرع</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" 
                                                {{ (old('branch_id', $selectedBranchId ?? null) == $branch->id) ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($selectedBranchId))
                                        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                        <small class="text-muted">أنت مسجل كحساب لهذا الفرع</small>
                                    @endif
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="employee_id" class="form-label">الموظف <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            id="employee_id" name="employee_id" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="form-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="section-header mb-0">
                                    <iconify-icon icon="solar:gem-bold-duotone"></iconify-icon>
                                    المنتجات
                                </h5>
                                <button type="button" class="btn btn-sm btn-success" id="add-product">
                                    <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                                    إضافة منتج
                                </button>
                            </div>
                            
                            <div id="products-container">
                                <!-- Product Item Template will be inserted here -->
                            </div>
                        </div>
                        
                        <!-- Hidden Product Template -->
                        <template id="product-item-template">
                            <div class="product-item mb-4 p-3 border rounded-3 position-relative" style="border: 2px dashed var(--ct-border-color);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold">
                                        <iconify-icon icon="solar:box-bold-duotone" class="text-primary"></iconify-icon>
                                        منتج <span class="product-number"></span>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-product">
                                        <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">الصنف <span class="text-danger">*</span></label>
                                        <select class="form-select product-category" name="products[INDEX][category_id]" required>
                                            <option value="">اختر الصنف</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">العيار <span class="text-danger">*</span></label>
                                        <select class="form-select product-caliber" name="products[INDEX][caliber_id]" required>
                                            <option value="">اختر العيار</option>
                                            @foreach($calibers as $caliber)
                                                <option value="{{ $caliber->id }}" data-tax-rate="{{ $caliber->tax_rate }}">
                                                    {{ $caliber->name }} ({{ $caliber->tax_rate }}%)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">الوزن (جرام) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.001" class="form-control product-weight" 
                                               name="products[INDEX][weight]" placeholder="0.000" required>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">المبلغ (جنيه) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control product-amount" 
                                               name="products[INDEX][amount]" placeholder="0.00" required>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="d-flex gap-3 flex-wrap">
                                            <small class="text-muted">
                                                <strong>الضريبة:</strong> 
                                                <span class="product-tax">0.00</span> جنيه
                                            </small>
                                            <small class="text-success">
                                                <strong>الصافي:</strong> 
                                                <span class="product-net">0.00</span> جنيه
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Payment Section -->
                        <div class="form-section">
                            <h5 class="section-header">
                                <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                                طريقة الدفع
                            </h5>
                            
                            <div class="mb-4">
                                <label class="form-label mb-3">اختر طريقة الدفع <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    <div class="col-md-4 col-sm-6">
                                        <label for="payment_cash" class="payment-card" id="cash-card">
                                            <input class="form-check-input d-none" type="radio" name="payment_method" 
                                                   id="payment_cash" value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                            <iconify-icon icon="solar:money-bag-bold-duotone" class="payment-icon text-success"></iconify-icon>
                                            <div class="payment-content">
                                                <div class="payment-title">نقدي</div>
                                                <div class="payment-desc">دفع كاش</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <label for="payment_network" class="payment-card" id="network-card">
                                            <input class="form-check-input d-none" type="radio" name="payment_method" 
                                                   id="payment_network" value="network" {{ old('payment_method') == 'network' ? 'checked' : '' }}>
                                            <iconify-icon icon="solar:card-bold-duotone" class="payment-icon text-info"></iconify-icon>
                                            <div class="payment-content">
                                                <div class="payment-title">شبكة</div>
                                                <div class="payment-desc">فيزا أو ماستركارد</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <label for="payment_mixed" class="payment-card" id="mixed-card">
                                            <input class="form-check-input d-none" type="radio" name="payment_method" 
                                                   id="payment_mixed" value="mixed" {{ old('payment_method') == 'mixed' ? 'checked' : '' }}>
                                            <iconify-icon icon="solar:wallet-2-bold-duotone" class="payment-icon text-warning"></iconify-icon>
                                            <div class="payment-content">
                                                <div class="payment-title">مشترك</div>
                                                <div class="payment-desc">نقدي + شبكة</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('payment_method')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Details -->
                            <div id="payment_details">
                                <div class="row">
                                    <div class="col-md-4 mb-3" id="cash_amount_field" style="display: none;">
                                        <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                                        <input type="number" step="0.01" class="form-control @error('cash_amount') is-invalid @enderror" 
                                               id="cash_amount" name="cash_amount" value="{{ old('cash_amount') }}" 
                                               placeholder="0.00">
                                        @error('cash_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3" id="network_amount_field" style="display: none;">
                                        <label for="network_amount" class="form-label">مبلغ الشبكة</label>
                                        <input type="number" step="0.01" class="form-control @error('network_amount') is-invalid @enderror" 
                                               id="network_amount" name="network_amount" value="{{ old('network_amount') }}" 
                                               placeholder="0.00">
                                        @error('network_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3" id="network_reference_field" style="display: none;">
                                        <label for="network_reference" class="form-label">رقم المعاملة</label>
                                        <input type="text" class="form-control @error('network_reference') is-invalid @enderror" 
                                               id="network_reference" name="network_reference" value="{{ old('network_reference') }}" 
                                               placeholder="رقم المعاملة">
                                        @error('network_reference')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tax Calculation Display -->
                        <div class="form-section">
                            <h5 class="section-header">
                                <iconify-icon icon="solar:calculator-bold-duotone"></iconify-icon>
                                الإجمالي الكلي
                            </h5>
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6">
                                    <div class="calc-card info">
                                        <iconify-icon icon="solar:box-bold-duotone" class="calc-icon text-primary"></iconify-icon>
                                        <div class="calc-label text-primary">الوزن الكلي</div>
                                        <h3 class="calc-value text-primary" id="total_weight_display">0.000 جم</h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="calc-card warning">
                                        <iconify-icon icon="solar:wallet-bold-duotone" class="calc-icon text-warning"></iconify-icon>
                                        <div class="calc-label text-warning">الإجمالي</div>
                                        <h3 class="calc-value text-warning" id="total_amount_display">0.00 جنيه</h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="calc-card info">
                                        <iconify-icon icon="solar:bill-list-bold-duotone" class="calc-icon text-info"></iconify-icon>
                                        <div class="calc-label text-info">الضريبة</div>
                                        <h3 class="calc-value text-info" id="tax_amount_display">0.00 جنيه</h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="calc-card success">
                                        <iconify-icon icon="solar:wallet-money-bold-duotone" class="calc-icon text-success"></iconify-icon>
                                        <div class="calc-label text-success">الصافي</div>
                                        <h3 class="calc-value text-success" id="net_amount_display">0.00 جنيه</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden input to store total amount for payment -->
                        <input type="hidden" id="final_total_amount" name="total_amount" value="0">
                        <input type="hidden" id="final_total_weight" name="weight" value="0">

                        <!-- Notes Section -->
                        <div class="form-section">
                            <h5 class="section-header">
                                <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                                ملاحظات
                            </h5>
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات إضافية</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="أي ملاحظات إضافية حول هذه المبيعة">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-3">
                                    <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-lg">
                                        <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                                        إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <iconify-icon icon="solar:diskette-bold"></iconify-icon>
                                        حفظ المبيعة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 0;
    
    // Add first product on page load
    addProduct();
    
    // Get employees by branch
    $('#branch_id').change(function() {
        const branchId = $(this).val();
        const employeeSelect = $('#employee_id');
        
        // Don't load if employees are already present (branch user case)
        if (employeeSelect.find('option').length > 1) {
            return;
        }
        
        employeeSelect.html('<option value="">جاري التحميل...</option>');
        
        if (branchId) {
            $.get('{{ route("api.employees-by-branch") }}', { branch_id: branchId })
                .done(function(data) {
                    employeeSelect.html('<option value="">اختر الموظف</option>');
                    data.forEach(function(employee) {
                        employeeSelect.append(`<option value="${employee.id}">${employee.name}</option>`);
                    });
                })
                .fail(function() {
                    employeeSelect.html('<option value="">خطأ في التحميل</option>');
                });
        } else {
            employeeSelect.html('<option value="">اختر الموظف</option>');
        }
    });
    
    // Auto-load employees if branch is pre-selected
    @if(isset($selectedBranchId) && !$employees->count())
        $('#branch_id').trigger('change');
    @endif
    
    // Add Product
    function addProduct() {
        const template = document.getElementById('product-item-template');
        const clone = template.content.cloneNode(true);
        
        // Replace INDEX with actual index
        clone.querySelectorAll('[name*="INDEX"]').forEach(function(el) {
            el.name = el.name.replace('INDEX', productIndex);
        });
        
        // Set product number
        clone.querySelector('.product-number').textContent = productIndex + 1;
        
        // Add to container
        document.getElementById('products-container').appendChild(clone);
        
        // Attach event listeners to the new product
        attachProductEvents(productIndex);
        
        productIndex++;
        
        // Recalculate totals
        calculateTotals();
    }
    
    // Add product button
    $('#add-product').click(function() {
        addProduct();
    });
    
    // Attach events to product
    function attachProductEvents(index) {
        const container = $('#products-container');
        const productItem = container.find('.product-item').eq(index);
        
        // Remove product
        productItem.find('.remove-product').click(function() {
            if ($('.product-item').length > 1) {
                productItem.remove();
                updateProductNumbers();
                calculateTotals();
            } else {
                alert('يجب أن يكون هناك منتج واحد على الأقل');
            }
        });
        
        // Calculate on input
        productItem.find('.product-caliber, .product-amount').on('change input', function() {
            calculateProductTax(productItem);
            calculateTotals();
        });
        
        productItem.find('.product-weight').on('change input', function() {
            calculateTotals();
        });
    }
    
    // Calculate tax for individual product
    function calculateProductTax(productItem) {
        const amount = parseFloat(productItem.find('.product-amount').val()) || 0;
        const caliberSelect = productItem.find('.product-caliber');
        const taxRate = parseFloat(caliberSelect.find(':selected').data('tax-rate')) || 0;
        
        const tax = (amount * taxRate) / 100;
        const net = amount - tax;
        
        productItem.find('.product-tax').text(tax.toFixed(2));
        productItem.find('.product-net').text(net.toFixed(2));
    }
    
    // Calculate totals for all products
    function calculateTotals() {
        let totalWeight = 0;
        let totalAmount = 0;
        let totalTax = 0;
        let totalNet = 0;
        
        $('.product-item').each(function() {
            const weight = parseFloat($(this).find('.product-weight').val()) || 0;
            const amount = parseFloat($(this).find('.product-amount').val()) || 0;
            const tax = parseFloat($(this).find('.product-tax').text()) || 0;
            const net = parseFloat($(this).find('.product-net').text()) || 0;
            
            totalWeight += weight;
            totalAmount += amount;
            totalTax += tax;
            totalNet += net;
        });
        
        $('#total_weight_display').text(totalWeight.toFixed(3) + ' جم');
        $('#total_amount_display').text(totalAmount.toFixed(2) + ' جنيه');
        $('#tax_amount_display').text(totalTax.toFixed(2) + ' جنيه');
        $('#net_amount_display').text(totalNet.toFixed(2) + ' جنيه');
        
        // Store in hidden inputs
        $('#final_total_amount').val(totalAmount.toFixed(2));
        $('#final_total_weight').val(totalWeight.toFixed(3));
        
        // Update payment fields
        updatePaymentAmounts(totalAmount);
    }
    
    // Update product numbers after removal
    function updateProductNumbers() {
        $('.product-item').each(function(index) {
            $(this).find('.product-number').text(index + 1);
        });
    }
    
    // Update payment amounts based on total
    function updatePaymentAmounts(total) {
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        
        if (paymentMethod === 'cash') {
            $('#cash_amount').val(total.toFixed(2));
            $('#network_amount').val('0');
        } else if (paymentMethod === 'network') {
            $('#network_amount').val(total.toFixed(2));
            $('#cash_amount').val('0');
        } else if (paymentMethod === 'mixed') {
            // For mixed, keep current values or clear
            if (!$('#cash_amount').val() && !$('#network_amount').val()) {
                $('#cash_amount, #network_amount').val('');
            }
        }
    }
    
    // Handle payment method changes with card styling
    $('input[name="payment_method"]').change(function() {
        const method = $(this).val();
        const total = parseFloat($('#final_total_amount').val()) || 0;
        
        // Update card styling
        $('.payment-card').removeClass('active');
        $(this).closest('.payment-card').addClass('active');
        
        // Hide all payment fields first
        $('#cash_amount_field, #network_amount_field, #network_reference_field').hide().removeClass('fade-in');
        
        // Show relevant fields based on payment method with animation
        if (method === 'cash') {
            $('#cash_amount_field').show().addClass('fade-in');
            $('#cash_amount').val(total.toFixed(2));
            $('#network_amount, #network_reference').val('');
        } else if (method === 'network') {
            $('#network_amount_field, #network_reference_field').show().addClass('fade-in');
            $('#network_amount').val(total.toFixed(2));
            $('#cash_amount').val('');
        } else if (method === 'mixed') {
            $('#cash_amount_field, #network_amount_field, #network_reference_field').show().addClass('fade-in');
            $('#cash_amount, #network_amount').val('');
        }
    });
    
    // Initialize active card on page load
    const checkedPayment = $('input[name="payment_method"]:checked');
    if (checkedPayment.length) {
        checkedPayment.closest('.payment-card').addClass('active');
    }
    
    // Validate mixed payment amounts
    $('#cash_amount, #network_amount').on('input', function() {
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        
        if (paymentMethod === 'mixed') {
            const cashAmount = parseFloat($('#cash_amount').val()) || 0;
            const networkAmount = parseFloat($('#network_amount').val()) || 0;
            const totalAmount = parseFloat($('#final_total_amount').val()) || 0;
            const sum = cashAmount + networkAmount;
            
            if (Math.abs(sum - totalAmount) > 0.01) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback">مجموع المبالغ يجب أن يساوي الإجمالي</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        }
    });
    
    // Trigger payment method change if there's an old value
    @if(old('payment_method'))
        $('input[name="payment_method"][value="{{ old('payment_method') }}"]').trigger('change');
    @endif
});
</script>
@endsection