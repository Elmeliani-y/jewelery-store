@extends('layouts.vertical', ['title' => 'التقرير المقارن'])
@section('title','التقرير المقارن')
@section('css')
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
<div class="container-fluid">
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
                                            <strong>{{ number_format($twoBranchComparison['branch1']['sales'], 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المصروفات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch1']['expenses'], 0) }}</strong>
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
                                        <strong class="text-success">{{ number_format($twoBranchComparison['branch1']['profit'], 0) }}</strong>
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
                                            <strong>{{ number_format($twoBranchComparison['branch2']['sales'], 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المصروفات:</small>
                                            <strong>{{ number_format($twoBranchComparison['branch2']['expenses'], 0) }}</strong>
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
                                        <strong class="text-success">{{ number_format($twoBranchComparison['branch2']['profit'], 0) }}</strong>
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
        $branchEmployees = $employeesComparison->where('branch_id', $branchId);
        $branchCategories = $categoriesComparison->where('branch_id', $branchId);
        $branchCalibers = $calibersComparison->where('branch_id', $branchId);
    @endphp
    <fieldset class="border rounded-3 p-3 mb-4">
        <legend class="float-none w-auto px-3 fs-5 fw-bold text-primary" style="cursor: pointer;" onclick="toggleBranchSection({{ $index }})">
            <i class="mdi mdi-chevron-down" id="branchIcon{{ $index }}"></i> {{ $branch['name'] }}
        </legend>
        
        <div id="branchContent{{ $index }}">
        <!-- Branch Summary Stats -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">المبيعات</small>
                        <div class="fs-5 fw-bold text-success">{{ number_format($branch['sales'] ?? 0, 0) }} د</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">المصروفات</small>
                        <div class="fs-5 fw-bold text-danger">{{ number_format($branch['expenses'] ?? 0, 0) }} د</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-secondary h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">الوزن</small>
                        <div class="fs-5 fw-bold">{{ number_format($branch['weight'] ?? 0, 2) }} جرام</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">عدد المبيعات</small>
                        <div class="fs-5 fw-bold text-info">{{ $branch['count'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">معدل سعر الجرام</small>
                        <div class="fs-5 fw-bold text-warning">
                            @if(($branch['weight'] ?? 0) > 0 && ($branch['sales'] ?? 0) > 0)
                                {{ number_format($branch['sales'] / $branch['weight'], 2) }} د/جرام
                            @else
                                0.00 د/جرام
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
                            @if($branchEmployees->count() > 0 && ($branch['weight'] ?? 0) > 0)
                                {{ number_format($branch['weight'] / $branchEmployees->count(), 2) }} جرام
                            @else
                                0.00 جرام
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">الربح</small>
                        <div class="fs-5 fw-bold text-primary">{{ number_format($branch['profit'] ?? 0, 0) }} د</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branch Charts -->
        <div class="row g-3">
            @if($branch['sales'] > 0)
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
            @endif
            @if($branch['expenses'] > 0)
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
            @endif
            @if($branchEmployees->count() > 0)
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
                                    @foreach($branchEmployees->take(10) as $employee)
                                    <tr>
                                        <td>{{ $employee['name'] }}</td>
                                        <td class="text-end">{{ number_format($employee['sales'], 0) }} د</td>
                                        <td class="text-end">{{ number_format($employee['weight'] ?? 0, 2) }} جرام</td>
                                        <td class="text-end text-warning fw-bold">
                                            @if(($employee['weight'] ?? 0) > 0)
                                                {{ number_format($employee['sales'] / $employee['weight'], 2) }} د/جرام
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if($branchCategories->count() > 0)
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
                                    @foreach($branchCategories as $category)
                                    <tr>
                                        <td>{{ $category['name'] }}</td>
                                        <td class="text-end">{{ number_format($category['sales'] ?? 0, 0) }} د</td>
                                        <td class="text-end">{{ number_format($category['weight'] ?? 0, 2) }} جرام</td>
                                        <td class="text-end">{{ $category['count'] ?? 0 }}</td>
                                        <td class="text-end text-warning fw-bold">
                                            @if(($category['weight'] ?? 0) > 0 && ($category['sales'] ?? 0) > 0)
                                                {{ number_format($category['sales'] / $category['weight'], 2) }} د/جرام
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
    // Toggle branch section
    function toggleBranchSection(index) {
        const content = document.getElementById('branchContent' + index);
        const icon = document.getElementById('branchIcon' + index);
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.classList.remove('mdi-chevron-right');
            icon.classList.add('mdi-chevron-down');
        } else {
            content.style.display = 'none';
            icon.classList.remove('mdi-chevron-down');
            icon.classList.add('mdi-chevron-right');
        }
    }
</script>
@if(!$showTwoBranchComparison)
<script>
    // Chart colors
    const colors = {
        primary: '#727cf5',
        success: '#0acf97',
        info: '#39afd1',
        warning: '#ffbc00',
        danger: '#fa5c7c',
        secondary: '#6c757d',
    };

    const chartColors = [
        colors.primary,
        colors.success,
        colors.info,
        colors.warning,
        colors.danger,
        colors.secondary,
        '#e83e8c',
        '#20c997',
        '#fd7e14',
        '#6f42c1',
    ];

    // Get all data
    const branchesData = @json($branchesComparison->values());
    const employeesData = @json($employeesComparison->values());
    const categoriesData = @json($categoriesComparison->values());

    // Generate charts for each branch
    branchesData.forEach((branch, index) => {
        const branchId = branch.branch_id || 0;
        
        // Branch Sales Chart (single bar showing total sales)
        const salesCanvas = document.getElementById('branchSalesChart' + index);
        if (salesCanvas && branch.sales > 0) {
            new Chart(salesCanvas, {
                type: 'bar',
                data: {
                    labels: ['المبيعات'],
                    datasets: [{
                        label: 'المبيعات',
                        data: [branch.sales],
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
                                    return 'المبيعات: ' + context.parsed.y.toLocaleString() + ' د';
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
        if (expensesCanvas && branch.expenses > 0) {
            new Chart(expensesCanvas, {
                type: 'bar',
                data: {
                    labels: ['المصروفات'],
                    datasets: [{
                        label: 'المصروفات',
                        data: [branch.expenses],
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
                                    return 'المصروفات: ' + context.parsed.y.toLocaleString() + ' د';
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
</script>
@endif

@if($showTwoBranchComparison && isset($twoBranchComparison) && $twoBranchComparison)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const colors = {
        primary: '#727cf5',
        success: '#0acf97',
        info: '#39afd1',
        warning: '#ffbc00',
        danger: '#fa5c7c',
    };

    // Two Branch Main Comparison Chart
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
                    label: 'الوزن (جرام)',
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
                        text: 'الوزن (جرام)'
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
    const branch1Employees = {!! json_encode($twoBranchComparison['branch1Employees']) !!};
    const branch2Employees = {!! json_encode($twoBranchComparison['branch2Employees']) !!};
    
    new Chart(document.getElementById('employeesSalesChart'), {
        type: 'bar',
        data: {
            labels: [
                ...branch1Employees.map(emp => emp.name + ' (' + branch1Data.name + ')'),
                ...branch2Employees.map(emp => emp.name + ' (' + branch2Data.name + ')')
            ],
            datasets: [{
                label: 'مبيعات الموظف (دينار)',
                data: [
                    ...branch1Employees.map(emp => emp.sales),
                    ...branch2Employees.map(emp => emp.sales)
                ],
                backgroundColor: [
                    ...branch1Employees.map(() => colors.primary),
                    ...branch2Employees.map(() => colors.warning)
                ],
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
                            const allEmployees = [...branch1Employees, ...branch2Employees];
                            const emp = allEmployees[context.dataIndex];
                            return [
                                'الوزن: ' + emp.weight.toFixed(2) + ' جرام',
                                'سعر الجرام: ' + emp.price_per_gram.toLocaleString() + ' دينار',
                                'عدد المبيعات: ' + emp.count
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
                        text: 'المبيعات (دينار)'
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
    const branch1PricePerGram = branch1Data.weight > 0 
        ? branch1Data.sales / branch1Data.weight 
        : 0;
    const branch2PricePerGram = branch2Data.weight > 0 
        ? branch2Data.sales / branch2Data.weight 
        : 0;
    
    console.log('Branch 1:', branch1Data.name, 'Price per gram:', branch1PricePerGram);
    console.log('Branch 2:', branch2Data.name, 'Price per gram:', branch2PricePerGram);
    
    new Chart(document.getElementById('branchesPricePerGramChart'), {
        type: 'bar',
        data: {
            labels: [branch1Data.name, branch2Data.name],
            datasets: [{
                label: 'سعر الجرام (دينار)',
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
                                'إجمالي المبيعات: ' + branchData.sales.toLocaleString() + ' دينار',
                                'إجمالي الوزن: ' + branchData.weight.toFixed(2) + ' جرام',
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
                        text: 'سعر الجرام (دينار)'
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
                csvContent += `"{{ $branch['name'] }}","{{ $branch['sales'] }}","{{ $branch['expenses'] }}","{{ $branch['profit'] }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة الموظفين\n';
            csvContent += 'الموظف,المبيعات,عدد المبيعات\n';
            @foreach($employeesComparison as $employee)
                csvContent += `"{{ $employee['name'] }}","{{ $employee['sales'] }}","{{ $employee['count'] }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة الفئات\n';
            csvContent += 'الفئة,المبيعات,عدد المبيعات\n';
            @foreach($categoriesComparison as $category)
                csvContent += `"{{ $category['name'] }}","{{ $category['sales'] }}","{{ $category['count'] }}"\n`;
            @endforeach
            
            csvContent += '\nمقارنة العيارات\n';
            csvContent += 'العيار,المبيعات,عدد المبيعات\n';
            @foreach($calibersComparison as $caliber)
                csvContent += `"{{ $caliber['name'] }}","{{ $caliber['sales'] }}","{{ $caliber['count'] }}"\n`;
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
@endif
@endsection
