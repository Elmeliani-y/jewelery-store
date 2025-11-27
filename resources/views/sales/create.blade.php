@extends('layouts.vertical', ['title' => 'تسجيل مبيعة جديدة'])

@section('css')
    @include('components.form-styles')
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
                        @unless(auth()->user()->isBranch())
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-primary">المبيعات</a></li>
                        @endunless
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
                    <form id="sale-create-form" action="{{ route('sales.store') }}" method="POST" class="arabic-text">
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
                                        <label class="form-label">المبلغ (ريال) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control product-amount" 
                                               name="products[INDEX][amount]" placeholder="0.00" required>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="d-flex gap-3 flex-wrap">
                                            <small class="text-muted">
                                                <strong>الضريبة:</strong> 
                                                <span class="product-tax">0.00</span> ريال
                                            </small>
                                            <small class="text-success">
                                                <strong>الصافي:</strong> 
                                                <span class="product-net">0.00</span> ريال
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
                                        <h3 class="calc-value text-warning" id="total_amount_display">0.00 ريال</h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="calc-card info">
                                        <iconify-icon icon="solar:bill-list-bold-duotone" class="calc-icon text-info"></iconify-icon>
                                        <div class="calc-label text-info">الضريبة</div>
                                        <h3 class="calc-value text-info" id="tax_amount_display">0.00 ريال</h3>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="calc-card success">
                                        <iconify-icon icon="solar:wallet-money-bold-duotone" class="calc-icon text-success"></iconify-icon>
                                        <div class="calc-label text-success">الصافي</div>
                                        <h3 class="calc-value text-success" id="net_amount_display">0.00 ريال</h3>
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
                    <!-- Error Alert (dynamic for AJAX) -->
                    <div class="alert alert-danger d-none mt-3" id="saleErrorAlert" role="alert"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX submit for sales create to show modal instead of redirect
    const form = document.getElementById('sale-create-form');
    const errorAlert = document.getElementById('saleErrorAlert');

    function clearErrors() {
        errorAlert.classList.add('d-none');
        errorAlert.textContent = '';
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }

    function showFieldErrors(errors) {
        clearErrors();
        let hasFieldErrors = false;
        Object.keys(errors || {}).forEach(name => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = Array.isArray(errors[name]) ? errors[name][0] : errors[name];
                field.parentElement.appendChild(feedback);
                hasFieldErrors = true;
            }
        });
        if (!hasFieldErrors && errors) {
            errorAlert.textContent = 'حدثت أخطاء أثناء الإرسال.';
            errorAlert.classList.remove('d-none');
        }
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const url = form.getAttribute('action');
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (response.status === 422) {
                const data = await response.json();
                showFieldErrors(data.errors || {});
                return;
            }

            const data = await response.json();

            if (data && data.success) {
                // Populate modal details and show
                const modalEl = document.getElementById('saleSuccessModal');
                const invoiceEl = document.getElementById('saleSuccessInvoice');
                if (invoiceEl && data.data?.invoice_number) {
                    invoiceEl.textContent = data.data.invoice_number;
                }
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                errorAlert.textContent = (data && data.message) ? data.message : 'حدث خطأ غير متوقع.';
                errorAlert.classList.remove('d-none');
            }
        } catch (err) {
            errorAlert.textContent = 'تعذر الاتصال بالخادم. حاول مرة أخرى.';
            errorAlert.classList.remove('d-none');
        }
    });

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
        $('#total_amount_display').text(totalAmount.toFixed(2) + ' ريال');
        $('#tax_amount_display').text(totalTax.toFixed(2) + ' ريال');
        $('#net_amount_display').text(totalNet.toFixed(2) + ' ريال');
        
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
<!-- Success Modal -->
<div class="modal fade" id="saleSuccessModal" tabindex="-1" aria-labelledby="saleSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="saleSuccessModalLabel">
                    <iconify-icon icon="solar:check-circle-bold" class="text-success fs-4 me-2"></iconify-icon>
                    تم حفظ المبيعة بنجاح
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                رقم الفاتورة: <strong id="saleSuccessInvoice">—</strong><br>
                يمكنك إضافة مبيعة أخرى أو إغلاق النافذة.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="addAnotherSaleBtn">إضافة مبيعة أخرى</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('addAnotherSaleBtn')?.addEventListener('click', function() {
            const form = document.getElementById('sale-create-form');
            if (form) {
                form.reset();
                // Clear dynamic products and add a fresh one
                const productsContainer = document.getElementById('products-container');
                productsContainer.innerHTML = '';
                // Reset counters in current scope
                window.location.reload(); // simplest way to reset dynamic state
            }
        });
    </script>
</div>
@endsection