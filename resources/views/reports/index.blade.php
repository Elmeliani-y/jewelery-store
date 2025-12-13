@extends('layouts.vertical', ['title' => 'التقارير'])
@section('title','التقارير')
@section('css')
<style>
.filters-card{border:1px solid var(--bs-border-color);border-radius:12px;background:var(--bs-body-bg)}
.report-card{border:1px solid var(--bs-border-color);border-radius:12px;transition:.2s;}
.report-card:hover{box-shadow:0 6px 18px rgba(0,0,0,.08);transform:translateY(-2px);}
.muted{opacity:.8}
.filter-actions .btn{border-radius:8px}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0"><iconify-icon icon="solar:chart-square-bold-duotone" class="fs-4 me-1"></iconify-icon> واجهة التقارير</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
    </div>

    <div class="card filters-card mb-4">
        <div class="card-body">
            <form id="reports-filter" class="row g-3" method="GET" action="{{ route('reports.index') }}">
                <div class="col-md-3">
                    <label class="form-label">الفرع</label>
                    <select name="branch_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id')==$b->id? 'selected':'' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الموظف</label>
                    <select name="employee_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" {{ request('employee_id')==$e->id? 'selected':'' }}>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الصنف</label>
                    <select name="category_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id')==$c->id? 'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">العيار</label>
                    <select name="caliber_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($calibers as $cl)
                            <option value="{{ $cl->id }}" {{ request('caliber_id')==$cl->id? 'selected':'' }}>{{ $cl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع المصروف</label>
                    <select name="expense_type_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($expenseTypes as $t)
                            <option value="{{ $t->id }}" {{ request('expense_type_id')==$t->id? 'selected':'' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">من</label>
                    <input type="date" name="date_from" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">إلى</label>
                    <input type="date" name="date_to" value="{{ request('date_to', now()->endOfMonth()->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end filter-actions gap-2">
                    <button class="btn btn-primary w-100" type="submit"><iconify-icon icon="solar:search-bold" class="me-1"></iconify-icon> تطبيق الفلاتر</button>
                    <a class="btn btn-light" href="{{ route('reports.index') }}">تفريغ</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير شامل</h6>
                        <iconify-icon icon="solar:clipboard-check-bold-duotone" class="text-primary fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">ملخص مبيعات ومصروفات وصافي ربح</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light btn-sm" href="{{ route('reports.comprehensive', request()->query()) }}">عرض</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.comprehensive', array_merge(request()->query(), ['format'=>'pdf'])) }}">PDF</a>
                        <a class="btn btn-outline-success btn-sm" href="{{ route('reports.comprehensive', array_merge(request()->query(), ['format'=>'excel'])) }}">Excel</a>
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('reports.comprehensive', array_merge(request()->query(), ['format'=>'csv'])) }}">CSV</a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">طباعة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير مفصل</h6>
                        <iconify-icon icon="solar:list-bold-duotone" class="text-primary fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">تفصيل المبيعات حسب فرع / موظف / صنف / عيار / تاريخ</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light btn-sm" href="{{ route('reports.detailed', request()->query()) }}">عرض</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.detailed', array_merge(request()->query(), ['format'=>'pdf'])) }}">PDF</a>
                        <a class="btn btn-outline-success btn-sm" href="{{ route('reports.detailed', array_merge(request()->query(), ['format'=>'excel'])) }}">Excel</a>
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('reports.detailed', array_merge(request()->query(), ['format'=>'csv'])) }}">CSV</a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">طباعة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير العيارات</h6>
                        <iconify-icon icon="solar:medal-star-bold-duotone" class="text-primary fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">مبيعات حسب العيار (يشمل 24)</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light btn-sm" href="{{ route('reports.calibers', request()->query()) }}">عرض</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.calibers', array_merge(request()->query(), ['format'=>'pdf'])) }}">PDF</a>
                        <a class="btn btn-outline-success btn-sm" href="{{ route('reports.calibers', array_merge(request()->query(), ['format'=>'excel'])) }}">Excel</a>
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('reports.calibers', array_merge(request()->query(), ['format'=>'csv'])) }}">CSV</a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">طباعة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير الأصناف</h6>
                        <iconify-icon icon="solar:bag-3-bold-duotone" class="text-primary fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">مبيعات حسب نوع الصنف</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light btn-sm" href="{{ route('reports.categories', request()->query()) }}">عرض</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.categories', array_merge(request()->query(), ['format'=>'pdf'])) }}">PDF</a>
                        <a class="btn btn-outline-success btn-sm" href="{{ route('reports.categories', array_merge(request()->query(), ['format'=>'excel'])) }}">Excel</a>
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('reports.categories', array_merge(request()->query(), ['format'=>'csv'])) }}">CSV</a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">طباعة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير الموظفين</h6>
                        <iconify-icon icon="solar:user-bold-duotone" class="text-primary fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">إحصائيات مبيعات ورواتب حسب الموظف</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light btn-sm" href="{{ route('reports.employees', request()->query()) }}">عرض</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.employees', array_merge(request()->query(), ['format'=>'pdf'])) }}">PDF</a>
                        <a class="btn btn-outline-success btn-sm" href="{{ route('reports.employees', array_merge(request()->query(), ['format'=>'excel'])) }}">Excel</a>
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('reports.employees', array_merge(request()->query(), ['format'=>'csv'])) }}">CSV</a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">طباعة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير صافي الربح</h6>
                        <iconify-icon icon="solar:chart-2-bold-duotone" class="text-primary fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">صافي بعد خصم المصروفات والرواتب</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light btn-sm" href="{{ route('reports.net-profit', request()->query()) }}">عرض</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.net-profit', array_merge(request()->query(), ['format'=>'pdf'])) }}">PDF</a>
                        <a class="btn btn-outline-success btn-sm" href="{{ route('reports.net-profit', array_merge(request()->query(), ['format'=>'excel'])) }}">Excel</a>
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('reports.net-profit', array_merge(request()->query(), ['format'=>'csv'])) }}">CSV</a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">طباعة</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">التقرير المقارن</h6>
                        <iconify-icon icon="solar:chart-square-bold-duotone" class="text-warning fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">مقارنة بالرسوم البيانية بين الفروع والموظفين والفئات</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-warning btn-sm text-white" href="{{ route('reports.comparative', request()->query()) }}">
                            <i class="mdi mdi-chart-line me-1"></i> عرض
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">تقرير صافي الربح</h6>
                        <iconify-icon icon="solar:calculator-bold-duotone" class="text-info fs-4"></iconify-icon>
                    </div>
                    <p class="text-muted small mt-2">تقرير مفصل للعيارات والأجور والرواتب حسب الفرع</p>
                    <div class="d-flex gap-2">
                        <a class="btn btn-info btn-sm text-white" href="{{ route('reports.kasr') }}">
                            <i class="mdi mdi-chart-box me-1"></i> عرض
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection