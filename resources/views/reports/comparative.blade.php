@extends('layouts.vertical', ['title' => 'التقرير المقارن'])
@section('title','التقرير المقارن')
@section('css')
@include('reports.partials.print-css')
<style>
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 2rem;
    }
    .stat-card {
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        padding: 1.5rem;
        background: var(--bs-body-bg);
        transition: .25s;
        margin-bottom: 1rem;
    }
    .stat-card:hover {
        box-shadow: 0 .25rem .9rem rgba(0,0,0,.08);
        transform: translateY(-2px);
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--bs-primary);
    }
    .stat-label {
        font-size: 0.875rem;
        color: var(--bs-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
</style>
@endsection
@section('content')
<!-- Sales By Category Chart -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title text-dark mb-0">المبيعات حسب الفئة</h5>
                </div>
            </div>
            <div class="card-body">
                <div id="categories_chart" class="apex-charts"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title text-dark mb-0">المصروفات حسب الفئة</h5>
                </div>
            </div>
            <div class="card-body">
                <div id="expenses_categories_chart" class="apex-charts"></div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>تقرير المقارنة</h2>
        <p>التاريخ: {{ $filters['date_from'] ?? '-' }} - {{ $filters['date_to'] ?? '-' }}</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right no-print">
                    <a href="{{ route('reports.all') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> عودة للتقارير
                    </a>
                    <button class="btn btn-info" onclick="exportToCSV()">
                        <i class="mdi mdi-file-delimited me-1"></i> تصدير CSV
                    </button>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> طباعة
                    </button>
                </div>
                <h4 class="page-title text-dark"><i class="mdi mdi-chart-line me-1"></i> التقرير المقارن</h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.comparative') }}" class="card mb-4 no-print">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="from" class="form-label">من تاريخ</label>
                    <input type="date" name="from" id="from" value="{{ request('from', $filters['date_from'] ?? date('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="to" class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" id="to" value="{{ request('to', $filters['date_to'] ?? date('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="mdi mdi-filter me-1"></i> عرض التقرير
                    </button>
                </div>
            </div>
        </div>
    </form>

    @php
        $showTwoBranchComparison = request('branch1') && request('branch2') && request('branch1') != request('branch2');
    @endphp

    @if($showTwoBranchComparison && isset($twoBranchComparison) && $twoBranchComparison)
    <!-- Two Branch Detailed Comparison -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-compare me-1"></i> مقارنة تفصيلية بين الفروع</h5>
                </div>
                <div class="card-body">
                    <!-- Top Level Comparison (3 columns) -->
                    <div class="row g-3 mb-4">
                        <!-- Column 1: Branch 1 -->
                        <div class="col-md-4">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white text-center">
                                    <h6 class="mb-0">{{ $twoBranchComparison['branch1']['name'] }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المبيعات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch1']['sales'], 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المصروفات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch1']['expenses'], 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">الوزن:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch1']['weight'], 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">عدد المبيعات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch1']['count'], 0) }}</strong>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-success">الربح:</strong>
                                        <strong class="text-success">{{ number_format($twoBranchComparison['branch1']['profit'], 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column 2: Comparison Type -->
                        <div class="col-md-4">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-dark text-center">
                                    <h6 class="mb-0">المقارنة</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-4">
                                        <i class="mdi mdi-chart-bar" style="font-size: 48px; color: #6c757d;"></i>
                                    </div>
                                    <h6 class="mb-3">مقارنة شاملة</h6>
                                    <div class="text-muted">
                                        <small>من: {{ request('from', 'البداية') }}</small><br>
                                        <small>إلى: {{ request('to', 'النهاية') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column 3: Branch 2 -->
                        <div class="col-md-4">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white text-center">
                                    <h6 class="mb-0">{{ $twoBranchComparison['branch2']['name'] }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المبيعات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch2']['sales'], 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المصروفات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch2']['expenses'], 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">الوزن:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch2']['weight'], 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">عدد المبيعات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch2']['count'], 0) }}</strong>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-success">الربح:</strong>
                                        <strong class="text-success">{{ number_format($twoBranchComparison['branch2']['profit'], 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Comparisons (3 columns like sketch) -->
                    <div class="row g-3 mb-4">
                        <!-- Column 1: المبيعات (Sales Details) -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">المبيعات</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesComparisonChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Column 2: المصروفات (Expenses Details) -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">المصروفات</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="expensesComparisonChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Column 3: الموظفين (Employees Details) -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">الموظفين</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="employeesComparisonChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row of Details (3 columns) -->
                    <div class="row g-3">
                        <!-- Column 1: الفئات (Categories) -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">الفئات</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoriesComparisonChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Column 2: العيارات (Calibers) -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">العيارات</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="calibersComparisonChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Column 3: سعر الجرام (Price per Gram) -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">سعر الجرام</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="pricePerGramChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!$showTwoBranchComparison && count($branchesComparison) > 0)
    <!-- Branch-wise Grouped Data and Charts -->
    @foreach($branchesComparison as $index => $branch)
    @php
        $branchId = $branch['branch_id'] ?? 0;
        $branchEmployees = $employeesComparison->where('branch_id', $branchId)->sortByDesc('total_sales');
        $branchCategories = $categoriesComparison->where('branch_id', $branchId);
        $branchCalibers = $calibersComparison->where('branch_id', $branchId);
    @endphp
    <fieldset class="border rounded-3 p-3 mb-4">
        <legend class="float-none w-auto px-3 fs-5 fw-bold text-primary" style="cursor: pointer;" onclick="toggleBranchSection({{ $index }})">
            <i class="mdi mdi-chevron-down" id="branchIcon{{ $index }}"></i> {{ $branch['branch_name'] }}
        </legend>
        
        <div id="branchContent{{ $index }}">
        <!-- Branch Summary Stats -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">المبيعات</small>
                        <div class="fs-5 fw-bold text-success">{{ number_format($branch['total_sales'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">المصروفات</small>
                        <div class="fs-5 fw-bold text-danger">{{ number_format($branch['expenses'] ?? $branch['total_expenses'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-secondary h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">الوزن</small>
                        <div class="fs-5 fw-bold">{{ number_format($branch['total_weight'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">عدد المبيعات</small>
                        <div class="fs-5 fw-bold text-info">{{ $branch['sales_count'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">معدل سعر الجرام</small>
                        <div class="fs-5 fw-bold text-warning">
                            @if(($branch['total_weight'] ?? 0) > 0 && ($branch['total_sales'] ?? 0) > 0)
                                {{ number_format($branch['total_sales'] / $branch['total_weight'], 2) }}
                            @else
                                0.00
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">معدل الجرام للموظف</small>
                        <div class="fs-5 fw-bold text-info">
                            @if($branchEmployees->count() > 0 && ($branch['total_weight'] ?? 0) > 0)
                                {{ number_format($branch['total_weight'] / $branchEmployees->count(), 2) }}
                            @else
                                0.00
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">الربح</small>
                        <div class="fs-5 fw-bold text-primary">{{ number_format($branch['profit'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branch Charts -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="mdi mdi-chart-bar me-1"></i> المبيعات</h6>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 200px;">
                            <canvas id="branchSalesChart{{ $index }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="mdi mdi-chart-pie me-1"></i> المصروفات</h6>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 200px;">
                            <canvas id="branchExpensesChart{{ $index }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="mdi mdi-account-star me-1"></i> أفضل الموظفين</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th class="text-end">المبيعات</th>
                                        <th class="text-end">الوزن</th>
                                        <th class="text-end">سعر الجرام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branchEmployees->take(10) as $employee)
                                    <tr>
                                        <td>{{ $employee['employee_name'] }}</td>
                                        <td class="text-end">{{ number_format($employee['total_sales'], 2) }}</td>
                                        <td class="text-end">{{ number_format($employee['total_weight'] ?? 0, 2) }}</td>
                                        <td class="text-end text-warning fw-bold">
                                            @if(($employee['total_weight'] ?? 0) > 0)
                                                {{ number_format($employee['total_sales'] / $employee['total_weight'], 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="mdi mdi-shape me-1"></i> الأصناف</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>الصنف</th>
                                        <th class="text-end">المبيعات</th>
                                        <th class="text-end">الوزن</th>
                                        <th class="text-end">العدد</th>
                                        <th class="text-end">سعر الجرام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branchCategories as $category)
                                    <tr>
                                        <td>{{ $category['category_name'] }}</td>
                                        <td class="text-end">{{ number_format($category['total_sales'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($category['total_weight'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ $category['items_count'] ?? 0 }}</td>
                                        <td class="text-end text-warning fw-bold">
                                            @if(($category['total_weight'] ?? 0) > 0 && ($category['total_sales'] ?? 0) > 0)
                                                {{ number_format($category['total_sales'] / $category['total_weight'], 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div><!-- End branchContent -->
    </fieldset>
    @endforeach

    @endif
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
        console.log('branchesData:', branchesData);
    // Toggle branch section
    function toggleBranchSection(index) {
        const content = document.getElementById('branchContent' + index);
        const icon = document.getElementById('branchIcon' + index);
        // Generate charts for each branch
        branchesData.forEach((branch, index) => {
            // Branch Sales Chart (single bar showing total sales)
            const salesCanvas = document.getElementById('branchSalesChart' + index);
            if (salesCanvas) {
                new Chart(salesCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['المبيعات'],
                        datasets: [{
                            label: 'المبيعات',
                            data: [branch.total_sales || 0],
                            backgroundColor: colors.success,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'المبيعات: ' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
            // Branch Expenses Chart (single bar showing total expenses)
            const expensesCanvas = document.getElementById('branchExpensesChart' + index);
            if (expensesCanvas) {
                new Chart(expensesCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['المصروفات'],
                        datasets: [{
                            label: 'المصروفات',
                            data: [branch.total_expenses || 0],
                            backgroundColor: colors.danger,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'المصروفات: ' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
                                type: 'bar',
                                data: {
                                    labels: ['المصروفات'],
                                    datasets: [{
                                        label: 'المصروفات',
                                        data: [0],
                                        backgroundColor: colors.danger,
                                        borderRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'المصروفات: ' + context.parsed.y.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: { grid: { display: false } },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Branch Employees Chart (bar chart for top employees by sales)
                        const branchEmployees = employeesData.filter(e => e.branch_id === branchId);
                        const topEmployees = branchEmployees.sort((a, b) => (b.total_sales || 0) - (a.total_sales || 0)).slice(0, 10);
                        const employeesCanvas = document.getElementById('employeesComparisonChart' + index);
                        if (employeesCanvas) {
                            new Chart(employeesCanvas, {
                                type: 'bar',
                                data: {
                                    labels: topEmployees.map(e => e.employee_name),
                                    datasets: [{
                                        label: 'المبيعات للموظف',
                                        data: topEmployees.map(e => e.total_sales || 0),
                                        backgroundColor: chartColors,
                                        borderRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'المبيعات: ' + context.parsed.y.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: { grid: { display: false } },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Branch Categories Chart (bar chart for top categories by sales)
                        const branchCategories = categoriesData.filter(c => c.branch_id === branchId);
                        const topCategories = branchCategories.sort((a, b) => (b.total_sales || 0) - (a.total_sales || 0)).slice(0, 10);
                        const categoriesCanvas = document.getElementById('categoriesComparisonChart' + index);
                        if (categoriesCanvas) {
                            new Chart(categoriesCanvas, {
                                type: 'bar',
                                data: {
                                    labels: topCategories.map(c => c.category_name),
                                    datasets: [{
                                        label: 'المبيعات للصنف',
                                        data: topCategories.map(c => c.total_sales || 0),
                                        backgroundColor: chartColors,
                                        borderRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'المبيعات: ' + context.parsed.y.toLocaleString();
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: { grid: { display: false } },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });

    // Two Branch Main Comparison Chart
    @if(isset($twoBranchComparison) && $showTwoBranchComparison)
    const branch1Data = {
        name: '{{ $twoBranchComparison["branch1"]["name"] }}',
        sales: {{ $twoBranchComparison['branch1']['sales'] }},
        expenses: {{ $twoBranchComparison['branch1']['expenses'] }},
        profit: {{ $twoBranchComparison['branch1']['profit'] }},
        weight: {{ $twoBranchComparison['branch1']['weight'] }},
        count: {{ $twoBranchComparison['branch1']['count'] }}
    };

    const branch2Data = {
        name: '{{ $twoBranchComparison["branch2"]["name"] }}',
        sales: {{ $twoBranchComparison['branch2']['sales'] }},
        expenses: {{ $twoBranchComparison['branch2']['expenses'] }},
        profit: {{ $twoBranchComparison['branch2']['profit'] }},
        weight: {{ $twoBranchComparison['branch2']['weight'] }},
        count: {{ $twoBranchComparison['branch2']['count'] }}
    };
    @endif

    // Main comparison chart
    new Chart(document.getElementById('twoBranchMainChart'), {
        type: 'bar',
        data: {
            labels: [branch1Data.name, branch2Data.name],
            datasets: [
                {
                    label: 'المبيعات',
                    data: [branch1Data.sales, branch2Data.sales],
                    backgroundColor: colors.primary,
                    borderRadius: 5,
                },
                {
                    label: 'المصروفات',
                    data: [branch1Data.expenses, branch2Data.expenses],
                    backgroundColor: colors.danger,
                    borderRadius: 5,
                },
                {
                    label: 'الربح',
                    data: [branch1Data.profit, branch2Data.profit],
                    backgroundColor: colors.success,
                    borderRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Stats comparison chart
    new Chart(document.getElementById('twoBranchStatsChart'), {
        type: 'bar',
        data: {
            labels: [branch1Data.name, branch2Data.name],
            datasets: [
                {
                    label: 'الوزن',
                    data: [branch1Data.weight, branch2Data.weight],
                    backgroundColor: colors.warning,
                    borderRadius: 5,
                    yAxisID: 'y',
                },
                {
                    label: 'عدد المبيعات',
                    data: [branch1Data.count, branch2Data.count],
                    backgroundColor: colors.info,
                    borderRadius: 5,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'الوزن'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'عدد المبيعات'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Employees Sales Comparison Chart (all employees from both branches)
    // Employees Sales Comparison Chart (all branches)
    const employeesData = @json($employeesComparison);
    const branchesData = @json($branchesComparison);
    branchesData.forEach((branch, index) => {
        const branchId = branch.branch_id || 0;
        const branchEmployees = employeesData.filter(e => e.branch_id === branchId);
        const employeesCanvas = document.getElementById('employeesSalesChart' + index);
        if (employeesCanvas) {
            new Chart(employeesCanvas, {
                type: 'bar',
                data: {
                    labels: branchEmployees.map(emp => emp.employee_name + ' (' + branch.branch_name + ')'),
                    datasets: [{
                        label: 'مبيعات الموظف',
                        data: branchEmployees.map(emp => emp.total_sales || 0),
                        backgroundColor: branchEmployees.map(() => colors.primary),
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const emp = branchEmployees[context.dataIndex];
                                    return [
                                        'الوزن: ' + (emp.total_weight ?? 0).toFixed(2),
                                        'سعر الجرام: ' + ((emp.total_weight ?? 0) > 0 ? (emp.total_sales / emp.total_weight).toLocaleString() : '0'),
                                        'عدد المبيعات: ' + (emp.sales_count ?? 0)
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'المبيعات'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Categories Comparison Chart
    const branch1Categories = categoriesData.filter(cat => cat.branch_id == branch1Data.id);
    const branch2Categories = categoriesData.filter(cat => cat.branch_id == branch2Data.id);
    
    // Get unique category names
    const categoryNames = [...new Set([...branch1Categories.map(c => c.name), ...branch2Categories.map(c => c.name)])];
    
    new Chart(document.getElementById('categoriesComparisonChart'), {
        type: 'bar',
        data: {
            labels: categoryNames,
            datasets: [
                {
                    label: branch1Data.name,
                    data: categoryNames.map(name => {
                        const cat = branch1Categories.find(c => c.name === name);
                        return cat ? cat.sales : 0;
                    }),
                    backgroundColor: colors.primary,
                    borderRadius: 5
                },
                {
                    label: branch2Data.name,
                    data: categoryNames.map(name => {
                        const cat = branch2Categories.find(c => c.name === name);
                        return cat ? cat.sales : 0;
                    }),
                    backgroundColor: colors.warning,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const dataIndex = context.dataIndex;
                            const datasetIndex = context.datasetIndex;
                            const categories = datasetIndex === 0 ? branch1Categories : branch2Categories;
                            const category = categories.find(c => c.name === categoryNames[dataIndex]);
                            if (category) {
                                return [
                                    'الوزن: ' + (category.weight || 0).toFixed(2),
                                    'عدد المبيعات: ' + (category.count || 0)
                                ];
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Calibers Comparison Chart
    const calibersData = @json($calibersComparison);
    // Get all unique caliber names
    const caliberNames = [...new Set(calibersData.map(c => c.name))];
    // Prepare datasets for each branch
    const branchesData = @json($branchesComparison);
    const caliberDatasets = branchesData.map((branch, idx) => {
        const branchCalibers = calibersData.filter(c => c.branch_id === branch.branch_id);
        return {
            label: branch.branch_name,
            data: caliberNames.map(name => {
                const cal = branchCalibers.find(c => c.name === name);
                return cal ? cal.sales : 0;
            }),
            backgroundColor: chartColors[idx % chartColors.length],
            borderRadius: 5
        };
    });
    new Chart(document.getElementById('calibersComparisonChart'), {
        type: 'bar',
        data: {
            labels: caliberNames,
            datasets: [
                {
                    label: branch1Data.name,
                    data: caliberNames.map(name => {
                        const cal = branch1Calibers.find(c => c.name === name);
                        return cal ? cal.sales : 0;
                    }),
                    backgroundColor: colors.primary,
                    borderRadius: 5
                },
                {
                    label: branch2Data.name,
                    data: caliberNames.map(name => {
                        const cal = branch2Calibers.find(c => c.name === name);
                        return cal ? cal.sales : 0;
                    }),
                    backgroundColor: colors.warning,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const dataIndex = context.dataIndex;
                            const datasetIndex = context.datasetIndex;
                            const calibers = datasetIndex === 0 ? branch1Calibers : branch2Calibers;
                            const caliber = calibers.find(c => c.name === caliberNames[dataIndex]);
                            if (caliber) {
                                return [
                                    'الوزن: ' + (caliber.weight || 0).toFixed(2),
                                    'عدد المبيعات: ' + (caliber.count || 0)
                                ];
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Price Per Gram Chart
    const branch1PricePerGram = branch1Data.weight > 0 
        ? branch1Data.sales / branch1Data.weight 
        : 0;
    const branch2PricePerGram = branch2Data.weight > 0 
        ? branch2Data.sales / branch2Data.weight 
        : 0;
    
    new Chart(document.getElementById('pricePerGramChart'), {
        type: 'bar',
        data: {
            labels: [branch1Data.name, branch2Data.name],
            datasets: [{
                label: 'سعر الجرام',
                data: [branch1PricePerGram, branch2PricePerGram],
                backgroundColor: [colors.primary, colors.warning],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const branchData = context.dataIndex === 0 ? branch1Data : branch2Data;
                            return [
                                'إجمالي المبيعات: ' + branchData.sales.toLocaleString(),
                                'إجمالي الوزن: ' + branchData.weight.toFixed(2),
                                'عدد المبيعات: ' + branchData.count
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'سعر الجرام'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Branches Price Per Gram Chart
    console.log('Branch 1:', branch1Data.name, 'Price per gram:', branch1PricePerGram);
    console.log('Branch 2:', branch2Data.name, 'Price per gram:', branch2PricePerGram);
    
    new Chart(document.getElementById('branchesPricePerGramChart'), {
        type: 'bar',
        data: {
            labels: [branch1Data.name, branch2Data.name],
            datasets: [{
                label: 'سعر الجرام',
                data: [branch1PricePerGram, branch2PricePerGram],
                backgroundColor: [colors.primary, colors.warning],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const branchData = context.dataIndex === 0 ? branch1Data : branch2Data;
                            return [
                                'إجمالي المبيعات: ' + branchData.sales.toLocaleString(),
                                'إجمالي الوزن: ' + branchData.weight.toFixed(2),
                                'عدد المبيعات: ' + branchData.count
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'سعر الجرام'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // CSV Export Function
    function exportToCSV() {
        let csvContent = '\uFEFF'; // UTF-8 BOM for Arabic support
        const separator = ',';
        
        @if($showTwoBranchComparison && isset($twoBranchComparison))
            // Two Branch Comparison Export
            csvContent += 'التقرير المقارن بين فرعين\n\n';
            
            // Summary Section
            csvContent += 'الفرع,المبيعات,المصروفات,صافي الربح,الوزن,عدد المبيعات\n';
            csvContent += `"${branch1Data.name}","${branch1Data.sales}","${branch1Data.expenses}","${branch1Data.profit}","${branch1Data.weight}","${branch1Data.count}"\n`;
            csvContent += `"${branch2Data.name}","${branch2Data.sales}","${branch2Data.expenses}","${branch2Data.profit}","${branch2Data.weight}","${branch2Data.count}"\n\n`;
            
            // Employees Section
            csvContent += 'موظفو الفرع الأول\n';
            csvContent += 'اسم الموظف,المبيعات,الوزن,سعر الجرام,عدد المبيعات\n';
            branch1Employees.forEach(emp => {
                csvContent += `"${emp.name}","${emp.sales}","${emp.weight}","${emp.price_per_gram}","${emp.count}"\n`;
            });
            
            csvContent += '\nموظفو الفرع الثاني\n';
            csvContent += 'اسم الموظف,المبيعات,الوزن,سعر الجرام,عدد المبيعات\n';
            branch2Employees.forEach(emp => {
                csvContent += `"${emp.name}","${emp.sales}","${emp.weight}","${emp.price_per_gram}","${emp.count}"\n`;
            });
            
            // Price Per Gram Section
            csvContent += '\nسعر الجرام للفروع\n';
            csvContent += 'الفرع,سعر الجرام\n';
            csvContent += `"${branch1Data.name}","${branch1PricePerGram}"\n`;
            csvContent += `"${branch2Data.name}","${branch2PricePerGram}"\n`;
            
        @else
            // General Comparison Export
            csvContent += 'التقرير المقارن\n\n';
            
            // Branches Comparison
            csvContent += 'مقارنة الفروع\n';
            csvContent += 'الفرع,المبيعات,المصروفات,الربح\n';
            @foreach($branchesComparison as $branch)
                csvContent += `"{{ $branch['branch_name'] }}","{{ $branch['total_sales'] ?? 0 }}","{{ $branch['total_expenses'] ?? 0 }}","{{ ($branch['total_sales'] ?? 0) - ($branch['total_expenses'] ?? 0) }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة الموظفين\n';
            csvContent += 'الموظف,المبيعات,عدد المبيعات\n';
            @foreach($employeesComparison as $employee)
                csvContent += `"{{ $employee['employee_name'] }}","{{ $employee['total_sales'] ?? 0 }}","{{ $employee['sales_count'] ?? ($employee['count'] ?? 0) }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة الفئات\n';
            csvContent += 'الفئة,المبيعات,عدد المبيعات\n';
            @foreach($categoriesComparison as $category)
                csvContent += `"{{ $category['category_name'] }}","{{ $category['total_sales'] ?? 0 }}","{{ $category['items_count'] ?? ($category['count'] ?? 0) }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة العيارات\n';
            csvContent += 'العيار,المبيعات,عدد المبيعات\n';
            @foreach($calibersComparison as $caliber)
                csvContent += `"{{ $caliber['name'] ?? '' }}","{{ $caliber['total_sales'] ?? ($caliber['sales'] ?? 0) }}","{{ $caliber['items_count'] ?? ($caliber['count'] ?? 0) }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة طرق الدفع\n';
            csvContent += 'طريقة الدفع,المبلغ,عدد المبيعات\n';
            @foreach($paymentMethodsComparison as $method)
                csvContent += `"{{ $method['name'] }}","{{ $method['amount'] }}","{{ $method['count'] }}"\n`;
            @endforeach
        @endif
        
        // Create download link
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'comparative_report_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
