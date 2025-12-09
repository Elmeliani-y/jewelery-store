@extends('layouts.vertical', ['title' => 'المصروفات اليومية'])

@section('css')
<style>
    .expenses-table {
        font-size: 0.9rem;
    }
    .expenses-table th {
        background-color: var(--bs-danger);
        color: white;
        font-weight: 600;
        white-space: nowrap;
    }
    .expenses-table td {
        vertical-align: middle;
    }
    .summary-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-2px);
    }
    .edit-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .expenses-table {
            font-size: 0.75rem;
        }
        .expenses-table th, .expenses-table td {
            padding: 0.5rem 0.25rem;
        }
        .edit-btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
        .summary-card h4 {
            font-size: 1rem;
        }
        .summary-card p {
            font-size: 0.75rem;
        }
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
                    <h4 class="fs-18 fw-semibold m-0 arabic-text">
                        <iconify-icon icon="solar:calendar-bold-duotone" class="me-2"></iconify-icon>
                        مصروفات اليوم - {{ today()->format('Y-m-d') }}
                    </h4>
                </div>
                <div class="text-end">
                    <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                        تسجيل مصروف جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-danger);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-1 text-danger"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي المصروفات</h6>
                            <h4 class="mb-0">{{ number_format($totalAmount, 2) }} <small>ريال</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-info);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-1 text-info"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">عدد المصروفات</h6>
                            <h4 class="mb-0">{{ $expenses->count() }} <small>مصروف</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('branch.daily-expenses') }}" class="row g-3 align-items-end arabic-text">
                        <div class="col-md-6">
                            <label for="expense_type_id" class="form-label">تصفية حسب نوع المصروف</label>
                            <select name="expense_type_id" id="expense_type_id" class="form-select">
                                <option value="">جميع الأنواع</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-danger w-100">
                                <iconify-icon icon="solar:filter-bold"></iconify-icon>
                                تطبيق الفلتر
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 arabic-text">
                        <iconify-icon icon="solar:list-bold-duotone" class="me-2"></iconify-icon>
                        المصروفات ({{ $expenses->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($expenses->isEmpty())
                        <div class="text-center py-5">
                            <iconify-icon icon="solar:wallet-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                            <p class="text-muted">لا توجد مصروفات اليوم</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover expenses-table mb-0 arabic-text">
                                <thead>
                                    <tr>
                                        <th>نوع المصروف</th>
                                        <th>الوصف</th>
                                        <th>المبلغ</th>
                                        <th>التاريخ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>
                                                <iconify-icon icon="solar:tag-bold-duotone" class="text-danger me-1"></iconify-icon>
                                                {{ $expense->expenseType->name }}
                                            </td>
                                            <td>{{ $expense->description ?? 'بدون وصف' }}</td>
                                            <td class="fw-semibold text-danger">{{ number_format($expense->amount, 2) }} ر.س</td>
                                            <td>{{ $expense->expense_date->format('h:i A') }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary edit-btn" title="تعديل">
                                                        <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                                    </a>
                                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info edit-btn" title="عرض">
                                                        <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const expenseTypeSelect = document.getElementById('expense_type_id');
    
    if (expenseTypeSelect) {
        expenseTypeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endsection
