@extends('layouts.vertical', ['title' => 'تسجيل مصروف جديد'])
@section('title')
    تسجيل مصروف جديد
@endsection

@section('css')
    @include('components.form-styles')
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
                        {{-- رابط قائمة المصروفات مخفي لحساب الفرع بعد تقييد الوصول --}}
                        @if(!auth()->user() || !auth()->user()->isBranch())
                            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">المصروفات</a></li>
                        @endif
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

    <form id="expense-create-form" action="{{ route('expenses.store') }}" method="POST">
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
                                <option value="add_new" style="color: #0d6efd; font-weight: bold;">+ إضافة نوع جديد</option>
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
                                المبلغ (ريال) <span class="text-danger">*</span>
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

                        @if(!auth()->user() || !auth()->user()->isBranch())
                            <a href="{{ route('expenses.index') }}" class="btn btn-light btn-lg">
                                <iconify-icon icon="solar:close-circle-bold" class="fs-5 me-2"></iconify-icon>
                                إلغاء
                            </a>
                        @endif
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

    <!-- Success Modal -->
    <div class="modal fade" id="expenseSuccessModal" tabindex="-1" aria-labelledby="expenseSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="expenseSuccessModalLabel">
                        <iconify-icon icon="solar:check-circle-bold" class="text-success fs-4 me-2"></iconify-icon>
                        تم الحفظ بنجاح
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    تم تسجيل المصروف بنجاح.<br>
                    رقم المصروف: <strong id="expenseSuccessId">—</strong><br>
                    يمكنك إضافة مصروف آخر أو إغلاق النافذة.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="addAnotherExpenseBtn">إضافة مصروف آخر</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert (dynamic) -->
    <div class="alert alert-danger d-none mt-3" id="expenseErrorAlert" role="alert"></div>

</div>
@endsection

@section('script')
<script>
    (function() {
        const form = document.getElementById('expense-create-form');
        const errorAlert = document.getElementById('expenseErrorAlert');
        const addAnotherBtn = document.getElementById('addAnotherExpenseBtn');

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
                    // Show success modal without redirect
                    const modalEl = document.getElementById('expenseSuccessModal');
                    const idEl = document.getElementById('expenseSuccessId');
                    if (idEl && data.data?.id) {
                        idEl.textContent = data.data.id;
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

        addAnotherBtn?.addEventListener('click', function() {
            // Reset form for another entry
            form.reset();
            clearErrors();
            const modalEl = document.getElementById('expenseSuccessModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal?.hide();
            // set default date to today again
            const dateInput = document.getElementById('expense_date');
            if (dateInput) {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                dateInput.value = `${yyyy}-${mm}-${dd}`;
            }
        });

        // Handle expense type dropdown change for "add new"
        const expenseTypeSelect = document.getElementById('expense_type_id');
        expenseTypeSelect?.addEventListener('change', function() {
            if (this.value === 'add_new') {
                const typeName = prompt('أدخل اسم نوع المصروف الجديد:');
                if (typeName && typeName.trim()) {
                    // Send AJAX request to create expense type
                    fetch('{{ route("expense-types.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: typeName.trim(),
                            is_active: true
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.id) {
                            // Add new option to select
                            const newOption = new Option(data.name, data.id, true, true);
                            this.add(newOption, this.options[2]); // Add after "add_new" option
                            this.value = data.id;
                            alert('تم إضافة نوع المصروف بنجاح');
                        }
                    })
                    .catch(error => {
                        alert('حدث خطأ في إضافة نوع المصروف');
                        this.value = '';
                    });
                } else {
                    this.value = '';
                }
            }
        });
    })();
</script>
@endsection
