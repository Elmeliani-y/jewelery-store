@extends('layouts.vertical', ['title' => 'لوحة التحكم'])

@section('content')
@section('css')
<style>
/* Category bar chart theme variables */
:root {
                @media (max-width: 600px) {
                    #sales-overtime {
                        min-width: 320px !important;
                        width: 100vw !important;
                        overflow-x: auto !important;
                    }
                    #sales-overtime .apexcharts-canvas {
                        min-width: 320px !important;
                        width: 100vw !important;
                        max-width: 100vw !important;
                    }
                }
            @media (max-width: 600px) {
                #sales-overtime {
                    min-width: 320px !important;
                    width: 100vw !important;
                    overflow-x: auto !important;
                }
                #sales-overtime .apexcharts-canvas {
                    min-width: 320px !important;
                    width: 100vw !important;
                    max-width: 100vw !important;
                }
            }
        /* Responsive tweaks for mobile bar chart */
        @media (max-width: 600px) {
            #categories_chart {
                min-width: 320px !important;
                min-height: 120px !important;
                width: 100vw !important;
                overflow-x: auto !important;
            }
            #categories_chart > div {
                min-width: 320px !important;
                width: 100vw !important;
                overflow-x: auto !important;
            }
            #categoriesBarChart {
                min-width: 320px !important;
                width: 100vw !important;
                max-width: 100vw !important;
                display: block;
            }
        }
    --cat-bar-1: #f59e42;
    --cat-bar-2: #3b82f6;
    --cat-bar-3: #a16207;
    --cat-bar-4: #be185d;
    --cat-bar-5: #059669;
    --cat-bar-6: #f43f5e;
    --cat-bar-7: #6366f1;
    --cat-bar-8: #eab308;
    --cat-bar-text: #7168EE;
    --cat-bar-border: #ccc;
    --cat-bar-category-color: #222;
}
[data-theme="dark"] {
    --cat-bar-text: #7168EE;
    --cat-bar-border: #444;
    --cat-bar-category-color: #fff;
}
@media print { 
    @page { size: A4 portrait; margin: 12mm; } 
    .topbar-custom,.app-sidebar-menu,.footer, .btn, form[action*="dashboard"], .apex-charts { display:none !important; } 
    .card{box-shadow:none !important;border:0 !important; } 
}
/* Filter bar polish */
.dash-filter{border:1px solid var(--bs-border-color);border-radius:12px;padding:.75rem 1rem;background:var(--bs-body-bg)}
.dash-filter .form-label{margin-bottom:.25rem;font-size:.8rem;color:var(--bs-secondary-color)}
.dash-filter .form-select,.dash-filter .form-control{border-radius:10px}
.dash-filter .btn{border-radius:10px}
</style>
<style>
@media (max-width: 600px) {
    #categories_chart {
        min-width: 220px !important;
        min-height: 120px !important;
        width: 100vw !important;
        overflow-x: auto !important;
    }
    #categories_chart .apexcharts-canvas {
        min-width: 220px !important;
        min-height: 120px !important;
        width: 100vw !important;
    }
    #categories_chart div, #categories_chart span {
        font-size: clamp(0.8em, 3vw, 1em) !important;
    }
}
</style>
<style>
@media (max-width: 600px) {
    #categories_chart > div {
        flex-direction: column !important;
        margin-bottom: 10px !important;
    }
    #categories_chart > div > div {
        height: 1.2em !important;
        min-width: 0 !important;
        max-width: 100% !important;
        font-size: 1em !important;
    }
}
</style>
@endsection

<!-- Start Content-->
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">
        <div class="grow">
            <h4 class="fs-18 fw-semibold m-0">لوحة التحكم</h4>
        </div>
        @if(auth()->check() && !auth()->user()->isBranch())
        <form method="GET" action="{{ route('c5d9f2h7') }}" class="dash-filter d-flex flex-wrap gap-3 align-items-end">
            <div>
                <label class="form-label mb-1">الفترة</label>
                <select name="period" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                    <option value="daily" {{ ($period ?? 'monthly')==='daily' ? 'selected' : '' }}>اليوم</option>
                    <option value="weekly" {{ ($period ?? 'monthly')==='weekly' ? 'selected' : '' }}>الأسبوع</option>
                    <option value="monthly" {{ ($period ?? 'monthly')==='monthly' ? 'selected' : '' }}>الشهر</option>
                    <option value="custom" {{ ($period ?? 'monthly')==='custom' ? 'selected' : '' }}>مخصص</option>
                </select>
            </div>
            <div id="customDates" class="d-flex gap-2" style="{{ ($period ?? 'monthly')==='custom' ? '' : 'display:none;' }}">
                <div>
                    <label class="form-label mb-1">من</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                </div>
                <div>
                    <label class="form-label mb-1">إلى</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                </div>
            </div>
            <div>
                <label class="form-label mb-1">الفرع</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">كل الفروع</option>
                    @foreach(($branches ?? []) as $b)
                        <option value="{{ $b->id }}" {{ (string)($branchId ?? '') === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2 align-items-end ms-auto">
                <button type="submit" class="btn btn-primary btn-sm"><iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon>تطبيق</button>
                <a href="{{ route('c5d9f2h7.p3q7r9s2', request()->query()) }}" class="btn btn-outline-primary btn-sm" target="_blank"><iconify-icon icon="solar:printer-bold" class="me-1"></iconify-icon>تقرير للطباعة</a>
            </div>
        </form>
        @endif
    </div>

    <!-- Start Main Widgets -->
    <div class="row">
        <div class="col-md-6 col-lg-4 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="widget-first">
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 text-dark fs-16 fw-medium text-center w-100">إجمالي المبيعات</p>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <h3 class="mb-0 fs-24 text-dark text-center w-100" dir="ltr">{{ number_format($metrics['total_sales'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success-subtle text-success fs-13">{{ number_format($metrics['sales_count'], 0, ',', '.') }} فاتورة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="widget-first">
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 text-dark fs-16 fw-medium">صافي المبيعات</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-0 fs-24 text-dark me-4" dir="ltr">{{ number_format($metrics['total_net_sales'], 0, ',', '.') }}</h3>
                            <!-- <div id="total_orders" class="apex-charts"></div> -->
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info-subtle text-info fs-13">بعد الضريبة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="widget-first">
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 text-dark fs-16 fw-medium">المصروفات</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-0 fs-24 text-dark me-4" dir="ltr">{{ number_format($metrics['total_expenses'], 0, ',', '.') }}</h3>
                            <!-- <div id="new_customers" class="apex-charts"></div> -->
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger-subtle text-danger fs-13">{{ number_format($metrics['expenses_count'], 0, ',', '.') }} مصروف</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-6 col-lg-4 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="widget-first">
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 text-dark fs-16 fw-medium">المرتجعات</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-0 fs-24 text-dark me-4" dir="ltr">{{ number_format($metrics['returned_sales_total'], 0, ',', '.') }}</h3>
                            <!-- <div id="returned_sales" class="apex-charts"></div> -->
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning-subtle text-warning fs-13">{{ number_format($metrics['returned_sales_count'], 0, ',', '.') }} فاتورة مرتجعة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="widget-first">
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 text-dark fs-16 fw-medium">الوزن الإجمالي</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-0 fs-24 text-dark me-4" dir="ltr">{{ number_format($metrics['total_weight'], 1, ',', '.') }} جرام</h3>
                            <!-- <div id="total_returns" class="apex-charts"></div> -->
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning-subtle text-warning fs-13">وزن الذهب المباع</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-6 col-xl">
            <div class="card">
                <div class="card-body">
                    <div class="widget-first">
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 text-dark fs-16 fw-medium">معدل سعر الجرام</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-0 fs-24 text-dark me-4" dir="ltr">{{ number_format($metrics['price_per_gram'], 2, ',', '.') }}</h3>
                            <!-- <div class="apex-charts"></div> -->
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info-subtle text-info fs-13">المبيعات ÷ الوزن</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Widgets -->

    <!-- Start Row -->
    <div class="row">
        <!-- Start Sales By Category -->
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title text-dark mb-0">مبيعات حسب صنف</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div style="width:100%; max-height:420px; min-height:220px; overflow-y:auto; overflow-x:visible; direction:rtl; padding-left:8px; padding-right:8px;">
                        <div id="categories_chart" style="width:100%;"></div>
                        <noscript><div class="text-danger">الرجاء تفعيل الجافاسكريبت لعرض الرسم البياني.</div></noscript>
                        @if(empty($chartsData['sales_by_category']) || count($chartsData['sales_by_category']) === 0)
                        <div class="text-center text-muted py-3">لا توجد بيانات لعرض الرسم البياني.</div>
                        @endif
                    </div>
                    <!-- device-view removed -->
                        <div class="text-center">
                            <p class="text-uppercase mb-1 fw-medium text-muted">إجمالي المبيعات</p>
                            <h3 class="mb-0 text-dark fw-semibold" dir="ltr">{{ number_format($metrics['total_sales'], 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sales By Category -->

        <!-- Start Sales Overtime -->
        <div class="col-md-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title text-dark mb-0">المبيعات حسب المدة</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="sales-overtime" class="apex-charts"></div>
                </div>
            </div> 
        </div>
        <!-- End Sales Overtime -->
    </div>
    <!-- End Row -->

    <!-- Start Row -->
    <div class="row">
        <!-- Start Top Branches -->
        <div class="col-md-6 col-xxl-4 col-xl-6">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title text-dark mb-0">أفضل الفروع</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topPerformers['branches'] as $branch)
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="grow align-content-center">
                                    <div class="row">
                                        <div class="col-1 d-flex align-items-center justify-content-center">
                                            <span class="badge bg-primary-subtle text-primary fs-13">{{ $branch['rank'] ?? $loop->iteration }}</span>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-1 text-dark fs-15">{{ $branch['branch']['name'] }}</h6>
                                            <span class="fs-14 text-muted">{{ $branch['count'] }} فاتورة</span>
                                        </div>
                                        <div class="col-5 text-end">
                                            <h6 class="mb-1 text-success fs-14" dir="ltr">{{ number_format($branch['amount'], 0, ',', '.') }}</h6>
                                            <span class="fs-13 text-muted" dir="ltr">{{ number_format($branch['weight'], 1, ',', '.') }} جم</span>
                                            @if($branch['weight'] > 0)
                                            <div class="badge bg-warning-subtle text-warning fs-12 mt-1">{{ number_format($branch['amount'] / $branch['weight'], 2) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">لا توجد بيانات للفروع</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Top Branches -->

        <!-- Start Top Employees -->
        <div class="col-md-6 col-xxl-4 col-xl-6">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title text-dark mb-0">أفضل الموظفين</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topPerformers['employees'] as $sale)
                        <li class="list-group-item">
                            <div class="d-flex">
                                <div class="grow align-content-center">
                                    <div class="row">
                                        <div class="col-7">
                                            <h6 class="mb-1 text-dark fs-15">{{ $sale['employee']['name'] }}</h6>
                                            <span class="fs-14 text-muted">{{ $sale['employee']['branch']['name'] }}</span>
                                        </div>
                                        <div class="col-5 text-end">
                                            <h6 class="mb-1 text-success fs-14" dir="ltr">{{ number_format($sale['amount'], 0, ',', '.') }}</h6>
                                            <span class="fs-13 text-muted" dir="ltr">{{ number_format($sale['weight'], 1, ',', '.') }} جم</span>
                                            @if($sale['weight'] > 0)
                                            <div class="badge bg-warning-subtle text-warning fs-12 mt-1">{{ number_format($sale['amount'] / $sale['weight'], 2) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">لا توجد بيانات للموظفين</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Top Employees -->

        <!-- Start Revenue Statistics -->
        <div class="col-md-12 col-xxl-4 col-xl-12">
            <div class="card overflow-hidden">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title text-dark mb-0">إحصائيات الإيرادات</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="revenueCharts" class="apex-charts"></div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="p-2 rounded-2" style="background:linear-gradient(90deg, rgba(0,0,0,0.02), rgba(0,0,0,0.00));">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div class="text-center flex-fill">
                                        <div class="fs-13 text-muted">عدد الفواتير</div>
                                        <div class="fw-bold fs-16 text-dark" dir="ltr">{{ number_format($metrics['sales_count'], 0, ',', '.') }}</div>
                                    </div>
                                    <div class="text-center flex-fill">
                                        <div class="fs-13 text-muted">إجمالي المبيعات</div>
                                        <div class="fw-bold fs-16 text-success" dir="ltr">{{ number_format($metrics['total_sales'], 2, ',', '.') }}</div>
                                    </div>
                                    <div class="text-center flex-fill">
                                        <div class="fs-13 text-muted">إجمالي المصروفات</div>
                                        <div class="fw-bold fs-16 text-danger" dir="ltr">{{ number_format($metrics['total_expenses'], 2, ',', '.') }}</div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Revenue Statistics -->
    </div>
    <!-- End Row -->
</div> 
<!-- container-fluid -->
@endsection

@section('script-bottom')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
<script>
function toggleCustomDates(value){
    const el=document.getElementById('customDates');
    if(!el) return; el.style.display = (value==='custom')? 'flex':'none';
}
// Pass data from Laravel to JavaScript
const categoriesData = @json($chartsData['sales_by_category']);
const dailySalesData = @json($chartsData['daily_sales']);
const monthlyRevenueData = @json($chartsData['monthly_revenue']);
const salesAmount = {{ $metrics['total_sales'] }};
const expensesAmount = {{ $metrics['total_expenses'] }};
</script>
<script src="{{ asset('js/crm-dashboard-custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="{{ asset('js/category-bars.js') }}?v={{ time() }}"></script>
@endsection