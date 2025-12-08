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
                <h4 class="page-title"><i class="mdi mdi-chart-line me-1"></i> التقرير المقارن</h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.comparative') }}" class="card mb-4 no-print">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="branch1" class="form-label">الفرع الأول (للمقارنة)</label>
                    <select name="branch1" id="branch1" class="form-select">
                        <option value="">اختر فرع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch1') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="branch2" class="form-label">الفرع الثاني (للمقارنة)</label>
                    <select name="branch2" id="branch2" class="form-select">
                        <option value="">اختر فرع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch2') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from" class="form-label">من تاريخ</label>
                    <input type="date" name="from" id="from" value="{{ request('from', $filters['date_from'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" id="to" value="{{ request('to', $filters['date_to'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="mdi mdi-filter me-1"></i> مقارنة
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
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="mdi mdi-compare me-1"></i> مقارنة تفصيلية: {{ $twoBranchComparison['branch1']['name'] }} vs {{ $twoBranchComparison['branch2']['name'] }}</h5>
                </div>
                <div class="card-body">
                    <!-- Summary Comparison -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary-subtle">
                                <div class="card-body">
                                    <h6 class="text-primary">{{ $twoBranchComparison['branch1']['name'] }}</h6>
                                    <div class="d-flex justify-content-between mt-3">
                                        <div>
                                            <small class="text-muted">المبيعات</small>
                                            <h4>{{ number_format($twoBranchComparison['branch1']['sales'], 0) }} ريال</h4>
                                        </div>
                                        <div>
                                            <small class="text-muted">المصروفات</small>
                                            <h4>{{ number_format($twoBranchComparison['branch1']['expenses'], 0) }} ريال</h4>
                                        </div>
                                        <div>
                                            <small class="text-muted">الربح</small>
                                            <h4 class="text-success">{{ number_format($twoBranchComparison['branch1']['profit'], 0) }} ريال</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info-subtle">
                                <div class="card-body">
                                    <h6 class="text-info">{{ $twoBranchComparison['branch2']['name'] }}</h6>
                                    <div class="d-flex justify-content-between mt-3">
                                        <div>
                                            <small class="text-muted">المبيعات</small>
                                            <h4>{{ number_format($twoBranchComparison['branch2']['sales'], 0) }} ريال</h4>
                                        </div>
                                        <div>
                                            <small class="text-muted">المصروفات</small>
                                            <h4>{{ number_format($twoBranchComparison['branch2']['expenses'], 0) }} ريال</h4>
                                        </div>
                                        <div>
                                            <small class="text-muted">الربح</small>
                                            <h4 class="text-success">{{ number_format($twoBranchComparison['branch2']['profit'], 0) }} ريال</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Comparison -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">مقارنة المبيعات والمصروفات</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="twoBranchMainChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">مقارنة الوزن وعدد المبيعات</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="twoBranchStatsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employees Comparison -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">مبيعات الموظفين</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 350px;">
                                        <canvas id="employeesSalesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">سعر الجرام للفروع</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 350px;">
                                        <canvas id="branchesPricePerGramChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!$showTwoBranchComparison)
    <!-- Branches Comparison -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-storefront-outline me-1"></i> مقارنة الفروع</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="branchesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إحصائيات الفروع</h5>
                </div>
                <div class="card-body">
                    @foreach($branchesComparison as $branch)
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">{{ $branch['name'] }}</div>
                                <div class="stat-value">{{ number_format($branch['sales'], 0) }} ريال</div>
                                <small class="text-muted">{{ $branch['count'] }} مبيعة</small>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-success-subtle text-success">
                                    ربح: {{ number_format($branch['profit'], 0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Comparison -->
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="mdi mdi-account-group me-1"></i> مقارنة الموظفين (أفضل 10)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="employeesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="mdi mdi-tag-multiple me-1"></i> مقارنة الفئات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calibers and Payment Methods -->
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="mdi mdi-diamond-stone me-1"></i> مقارنة العيارات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="calibersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="mdi mdi-cash-multiple me-1"></i> طرق الدفع</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

    // Branches Chart
    const branchesData = @json($branchesComparison->values());
    new Chart(document.getElementById('branchesChart'), {
        type: 'bar',
        data: {
            labels: branchesData.map(b => b.name),
            datasets: [
                {
                    label: 'المبيعات',
                    data: branchesData.map(b => b.sales),
                    backgroundColor: colors.primary,
                    borderRadius: 5,
                },
                {
                    label: 'المصروفات',
                    data: branchesData.map(b => b.expenses),
                    backgroundColor: colors.danger,
                    borderRadius: 5,
                },
                {
                    label: 'الربح',
                    data: branchesData.map(b => b.profit),
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

    // Employees Chart
    const employeesData = @json($employeesComparison->values());
    new Chart(document.getElementById('employeesChart'), {
        type: 'horizontalBar',
        data: {
            labels: employeesData.map(e => e.name),
            datasets: [{
                label: 'المبيعات (ريال)',
                data: employeesData.map(e => e.sales),
                backgroundColor: chartColors,
                borderRadius: 5,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Categories Chart
    const categoriesData = @json($categoriesComparison->values());
    new Chart(document.getElementById('categoriesChart'), {
        type: 'doughnut',
        data: {
            labels: categoriesData.map(c => c.name),
            datasets: [{
                data: categoriesData.map(c => c.sales),
                backgroundColor: chartColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Calibers Chart
    const calibersData = @json($calibersComparison->values());
    new Chart(document.getElementById('calibersChart'), {
        type: 'pie',
        data: {
            labels: calibersData.map(c => c.name),
            datasets: [{
                data: calibersData.map(c => c.sales),
                backgroundColor: chartColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentData = @json($paymentMethodsComparison->values());
    new Chart(document.getElementById('paymentMethodsChart'), {
        type: 'bar',
        data: {
            labels: paymentData.map(p => p.name),
            datasets: [{
                label: 'المبيعات (ريال)',
                data: paymentData.map(p => p.sales),
                backgroundColor: [colors.success, colors.info, colors.warning],
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
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
