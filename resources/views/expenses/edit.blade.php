@extends('layouts.vertical', ['title' => 'تعديل مصروف'])
@section('title') تعديل مصروف @endsection
@section('css')
    @include('components.form-styles')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تعديل مصروف</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">المصروفات</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('expenses.update', $expense) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">معلومات المصروف</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="branch_id" class="form-label">الفرع <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $expense->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expense_type_id" class="form-label">
                                    نوع المصروف <span class="text-danger">*</span>
                                </label>
                                <select name="expense_type_id" id="expense_type_id" class="form-select @error('expense_type_id') is-invalid @enderror" required>
                                    <option value="add_new" style="color: #0d6efd; font-weight: bold;">+ إضافة نوع جديد</option>
                                    @foreach($expenseTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('expense_type_id', $expense->expense_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">المبلغ (ريال) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0.01" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expense_date" class="form-label">تاريخ المصروف <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" id="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', optional($expense->expense_date)->format('Y-m-d')) }}" required>
                                @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
                                <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $expense->description) }}" required>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">الإجراءات</h5>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-light">إلغاء</a>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="mdi mdi-information-outline me-1"></i>
                    قم بمراجعة البيانات قبل الحفظ.
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
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
                        this.add(newOption, this.options[1]); // Add after "add_new" option
                        this.value = data.id;
                        alert('تم إضافة نوع المصروف بنجاح');
                    }
                })
                .catch(error => {
                    alert('حدث خطأ في إضافة نوع المصروف');
                    this.value = '{{ $expense->expense_type_id }}';
                });
            } else {
                this.value = '{{ $expense->expense_type_id }}';
            }
        }
    });
</script>
@endsection