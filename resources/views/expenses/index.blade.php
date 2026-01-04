@extends('layouts.vertical', ['title' => 'المصروفات'])
@section('title') المصروفات @endsection

@section('css')
<style>
    .page-header { background: var(--bs-primary); padding:1.75rem 2rem; border-radius:.75rem; color:#fff; margin-bottom:1.75rem; }
    [data-bs-theme="dark"] .page-header { background: var(--bs-primary); }
    .expenses-table td, .expenses-table th { white-space: nowrap; }
    .expenses-table .badge { font-weight:500; }
    .table-card { padding:0; }
    .pagination .page-link { font-size:.75rem; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-1">
                    <i class="ri-wallet-3-line me-2"></i>
                    إدارة المصروفات
                </h3>
                <p class="mb-0 opacity-75">عرض ومتابعة جميع المصروفات المسجلة</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('l7m2n6o1.create') }}" class="btn btn-light">
                    <i class="ri-add-line me-1"></i>
                    إضافة مصروف جديد
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters: Branch, Dates, Expense ID -->
    <form method="GET" class="mb-3 row g-2 align-items-end">
        <div class="col-md-2">
            <label for="branch_id" class="form-label mb-1">الفرع</label>
            <select name="branch_id" id="branch_id" class="form-select">
                <option value="">كل الفروع</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="expense_type_id" class="form-label mb-1">نوع المصروف</label>
            <select name="expense_type_id" id="expense_type_id" class="form-select">
                <option value="">كل الأنواع</option>
                @foreach($expenseTypes as $type)
                    <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label mb-1">من تاريخ</label>
            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <label for="id" class="form-label mb-1">رقم المصروف</label>
            <input type="number" name="id" id="id" class="form-control" placeholder="بحث برقم المصروف" value="{{ request('id') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit" style="margin-top: 2px;">بحث</button>
        </div>
    </form>

    <!-- Expenses List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($expenses->count() > 0)
                    <div class="table-responsive table-card expenses-table">
                        <table class="table table-borderless table-centered align-middle table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">رقم المصروف</th>
                                    <th scope="col">الفرع</th>
                                    <th scope="col">نوع المصروف</th>
                                    <th scope="col">الوصف</th>
                                    <th scope="col">المبلغ</th>
                                    <th scope="col">تاريخ المصروف</th>
                                    <th scope="col" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">{{ $expense->id }}</span>
                                    </td>
                                    <td class="text-muted">{{ $expense->branch->name }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $expense->expenseType->name }}</span>
                                    </td>
                                    <td class="text-muted">{{ $expense->description }}</td>
                                    <td dir="ltr"><span class="fw-semibold text-dark">{{ number_format($expense->amount, 0, ',', '.') }} <small class="text-muted">ريال</small></span></td>
                                    <td class="text-muted">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        @if(!request()->filled('id'))
                                            <a href="{{ route('l7m2n6o1.show', $expense) }}" class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip" data-bs-original-title="عرض">
                                                <i class="mdi mdi-eye-outline text-info fs-16"></i>
                                            </a>
                                            <a href="{{ route('l7m2n6o1.edit', $expense) }}" class="btn btn-icon btn-sm bg-warning-subtle" data-bs-toggle="tooltip" data-bs-original-title="تعديل">
                                                <i class="mdi mdi-pencil-outline text-warning fs-16"></i>
                                            </a>
                                            <form action="{{ route('l7m2n6o1.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="حذف">
                                                    <i class="mdi mdi-delete-outline text-danger fs-16"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @php
                                    $ijmaliCount = $expenses->count();
                                    $ijmaliMabalegh = $expenses->sum('amount');
                                @endphp
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td colspan="4">الإجماليات</td>
                                        <td class="text-end">{{ number_format($ijmaliMabalegh, 2) }} <small class="text-muted">ريال</small></td>
                                        <td></td>
                                        <td class="text-end">عدد المصروفات: {{ $ijmaliCount }}</td>
                                    </tr>
                                </tfoot
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                            <small class="text-muted">
                                عرض {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} من أصل {{ $expenses->total() }}
                            </small>
                        </div>
                        {{ $expenses->links('pagination::bootstrap-5') }}
                    </div>
                    @else
                    <div class="py-5 text-center">
                        <i class="mdi mdi-wallet-outline" style="font-size:3.5rem; color:#adb5bd;"></i>
                        <h5 class="text-muted mt-3">لا توجد مصروفات</h5>
                        <p class="text-muted">قم بإضافة مصروف جديد للبدء</p>
                        @if(!request()->filled('id'))
                        <a href="{{ route('l7m2n6o1.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            إضافة مصروف
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
