@extends('layouts.vertical', ['title' => 'تقرير الكسر'])
@section('title', 'تقرير الكسر')

@section('css')
@include('reports.partials.print-css')
<style>
    .kasr-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        padding: 12px 8px;
        border: 1px solid #dee2e6;
    }
    .kasr-table td {
        text-align: center;
        padding: 10px 8px;
        border: 1px solid #dee2e6;
    }
    .kasr-table {
        border-collapse: collapse;
        width: 100%;
    }
    .total-row {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .caliber-label {
        font-weight: 600;
        color: #495057;
    }
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right no-print">
                    <a href="{{ route('reports.all') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> عودة
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> طباعة
                    </button>
                </div>
                <h4 class="page-title"><i class="mdi mdi-chart-box me-1"></i> تقرير الكسر</h4>
            </div>
        </div>
    </div>

    <!-- Input Form -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="POST" action="{{ route('reports.kasr') }}" id="kasrForm">
                @csrf
                
                <!-- Filters Row -->
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <input type="hidden" name="auto_refresh" id="auto_refresh" value="0">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" id="date_from" 
                               value="{{ request('date_from', $filters['date_from'] ?? date('Y-m-01')) }}" 
                               class="form-control"
                               onchange="submitKasrFilters()">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" id="date_to" 
                               value="{{ request('date_to', $filters['date_to'] ?? date('Y-m-d')) }}" 
                               class="form-control"
                               onchange="submitKasrFilters()">
                    </div>
                    <div class="col-md-4">
                        <label for="branch_id" class="form-label">الفرع</label>
                        <select name="branch_id" id="branch_id" class="form-select" onchange="submitKasrFilters()">
                            <option value="">اختر الفرع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" 
                                    {{ request('branch_id', $filters['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100 text-muted small text-center">يتم التحديث تلقائياً عند تغيير الفرع أو التاريخ</div>
                    </div>
                </div>

                <!-- Calibers Input Table -->
                <h5 class="mb-3">أدخل بيانات العيارات</h5>
                <div class="table-responsive">
                    <table class="kasr-table table">
                        <thead>
                            <tr>
                                <th style="width: 25%">النوع</th>
                                <th style="width: 35%">الوزن</th>
                                <th style="width: 40%">سعر الجرام</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($calibers as $caliber)
                            <tr>
                                <td class="caliber-label text-end">{{ $caliber->name }}</td>
                                <td>
                                     <input type="number" name="weight_{{ $caliber->id }}" id="weight_{{ $caliber->id }}"
                                         value="{{ $weights[$caliber->id] ?? 0 }}"
                                         step="0.01" class="form-control caliber-input" data-caliber="{{ $caliber->id }}" disabled>
                                </td>
                                <td>
                                    <input type="number" name="price_{{ $caliber->id }}" id="price_{{ $caliber->id }}"
                                           value="{{ old('price_' . $caliber->id, request('price_' . $caliber->id, 0)) }}"
                                           step="0.01" class="form-control caliber-input" data-caliber="{{ $caliber->id }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Expenses and Salaries (Auto-loaded from database) -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label for="expenses" class="form-label fw-semibold">المصروفات الكاملة للفرع خلال المدة</label>
                        <input type="text" id="expenses_display" 
                               value="{{ number_format($expenses ?? 0, 2) }}" 
                               class="form-control bg-light" readonly>
                        <input type="hidden" name="expenses" value="{{ $expenses ?? 0 }}">
                        <small class="text-muted">إجمالي كل المصروفات المسجلة للفرع المختار خلال الفترة.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="salaries" class="form-label fw-semibold">الرواتب من شاشة الموظفين</label>
                        <input type="text" id="salaries_display" 
                               value="{{ number_format($salaries ?? 0, 2) }}" 
                               class="form-control bg-light" readonly>
                        <input type="hidden" name="salaries" value="{{ $salaries ?? 0 }}">
                        <small class="text-muted">مجموع رواتب موظفي الفرع (من شاشة الموظفين) دون ربط بفترة.</small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="mdi mdi-calculator me-1"></i> احسب التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
// Inline to avoid missing stack includes; auto-submit on branch/date change.
(function() {
    const form = document.getElementById('kasrForm');
    const autoRefresh = document.getElementById('auto_refresh');
    window.submitKasrFilters = function() {
        if (!form) return;
        if (autoRefresh) autoRefresh.value = '1';
        // slight delay to ensure value is set before submit
        setTimeout(() => {
            if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }, 0);
    };
    ['date_from','date_to','branch_id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', submitKasrFilters);
            el.addEventListener('input', submitKasrFilters);
        }
    });
})();
</script>

    @if(isset($reportData))
    <!-- Report Results -->
    <div class="card">
        <div class="card-body">
            <!-- Report Header -->
            <div class="text-center mb-4">
                    <h3>نتائج تقرير الكسر</h3>
                    @if(request()->isMethod('post') && request('calculate_report') && isset($reportData))
                    <p class="text-muted mb-1">الفرع: <strong>{{ $selectedBranch->name }}</strong></p>
                @endif
                <p class="text-muted">من {{ $filters['date_from'] }} إلى {{ $filters['date_to'] }}</p>
            </div>

            <!-- Main Results Table -->
            <div class="table-responsive">
                <table class="kasr-table table">
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>الوزن</th>
                            <th>سعر الجرام</th>
                            <th>المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['calibers'] as $caliber)
                        <tr>
                            <td class="caliber-label text-end">{{ $caliber['name'] }}</td>
                            <td dir="ltr">{{ number_format($caliber['weight'], 2) }}</td>
                            <td dir="ltr">{{ number_format($caliber['price_per_gram'], 2) }}</td>
                            <td dir="ltr">{{ number_format($caliber['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        
                        <tr class="total-row">
                            <td class="text-end" colspan="3">المبلغ الإجمالي</td>
                            <td dir="ltr">{{ number_format($reportData['total_amount'], 2) }}</td>
                        </tr>
                        
                        <tr>
                            <td class="text-end" colspan="3">الأجور (المصروفات)</td>
                            <td dir="ltr">{{ number_format($reportData['expenses'], 2) }}</td>
                        </tr>
                        
                        <tr>
                            <td class="text-end" colspan="3">الرواتب</td>
                            <td dir="ltr">{{ number_format($reportData['salaries'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title mb-3 text-center">الملخص النهائي</h5>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="text-end fw-semibold">إجمالي الوزن:</td>
                                    <td dir="ltr" class="fw-bold">{{ number_format($reportData['total_weight'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold">إجمالي المبلغ:</td>
                                    <td dir="ltr" class="fw-bold">{{ number_format($reportData['total_amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold">متوسط سعر الجرام:</td>
                                    <td dir="ltr" class="fw-bold">{{ number_format($reportData['avg_price_per_gram'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold">إجمالي المصروفات والرواتب:</td>
                                    <td dir="ltr" class="fw-bold text-danger">{{ number_format($reportData['total_expenses'], 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-end fw-semibold">الفائدة (قبل خصم الرواتب):</td>
                                    <td dir="ltr" class="fw-bold text-primary">{{ number_format($reportData['profit'], 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td class="text-end fw-semibold fs-5">صافي الربح (بعد كل المصروفات):</td>
                                    <td dir="ltr" class="fw-bold text-success fs-5">{{ number_format($reportData['net_profit'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    // Auto-print functionality if needed
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('print') === '1') {
        window.onload = function() {
            window.print();
        };
    }
</script>
@endsection
