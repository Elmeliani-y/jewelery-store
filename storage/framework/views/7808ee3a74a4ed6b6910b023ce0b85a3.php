<?php $__env->startSection('title','التقرير المقارن'); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('reports.partials.print-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<!-- ...existing code... -->
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>تقرير المقارنة</h2>
        <p>التاريخ: <?php echo e($filters['date_from'] ?? '-'); ?> - <?php echo e($filters['date_to'] ?? '-'); ?></p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right no-print">
                    <a href="<?php echo e(route('t3u8v1w4.b1c5d8e3')); ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> عودة للتقارير
                    </a>
                    <button class="btn btn-info" onclick="exportToCSV()">
                        <i class="mdi mdi-file-delimited me-1"></i> تصدير CSV
                    </button>
                    <button class="btn btn-outline-primary" onclick="window.exportPageToPdf()">
                        <i class="mdi mdi-file-pdf me-1"></i> تصدير PDF
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
    <form method="GET" action="<?php echo e(route('t3u8v1w4.l3m8n2o6')); ?>" class="card mb-4 no-print">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="from" class="form-label">من تاريخ</label>
                    <input type="date" name="from" id="from" value="<?php echo e(request('from', $filters['date_from'] ?? date('Y-m-d'))); ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="to" class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" id="to" value="<?php echo e(request('to', $filters['date_to'] ?? date('Y-m-d'))); ?>" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="mdi mdi-filter me-1"></i> عرض التقرير
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php
        $showTwoBranchComparison = request('branch1') && request('branch2') && request('branch1') != request('branch2');
    ?>

    <?php if($showTwoBranchComparison && isset($twoBranchComparison) && $twoBranchComparison): ?>
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
                                    <h6 class="mb-0"><?php echo e($twoBranchComparison['branch1']['name']); ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المبيعات:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch1']['sales'], 2)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المصروفات:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch1']['expenses'], 2)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">الوزن:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch1']['weight'], 2)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">عدد المبيعات:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch1']['count'], 0)); ?></strong>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-success">الربح:</strong>
                                        <strong class="text-success"><?php echo e(number_format($twoBranchComparison['branch1']['profit'], 2)); ?></strong>
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
                                        <small>من: <?php echo e(request('from', 'البداية')); ?></small><br>
                                        <small>إلى: <?php echo e(request('to', 'النهاية')); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column 3: Branch 2 -->
                        <div class="col-md-4">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white text-center">
                                    <h6 class="mb-0"><?php echo e($twoBranchComparison['branch2']['name']); ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المبيعات:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch2']['sales'], 2)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">المصروفات:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch2']['expenses'], 2)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">الوزن:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch2']['weight'], 2)); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">عدد المبيعات:</small>
                                            <strong><?php echo e(number_format($twoBranchComparison['branch2']['count'], 0)); ?></strong>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-success">الربح:</strong>
                                        <strong class="text-success"><?php echo e(number_format($twoBranchComparison['branch2']['profit'], 2)); ?></strong>
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
    <?php endif; ?>

    <?php if(!$showTwoBranchComparison && count($branchesComparison) > 1): ?>
        <!-- Combined Sales and Expenses Chart for All Branches (screen only) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="mdi mdi-chart-bar me-1"></i> إجمالي المبيعات والمصروفات لكل فرع</h5>
                            <small class="d-block text-light opacity-75">من: <?php echo e(request('from', 'البداية')); ?> &nbsp;|&nbsp; إلى: <?php echo e(request('to', 'النهاية')); ?></small>
                    </div>
                    <div class="card-body">
                        <canvas id="branchesSalesExpensesChart" style="height: 350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const branchesData = <?php echo json_encode($branchesComparison, 15, 512) ?>;
                const branchNames = branchesData.map(b => b.branch_name);
                const salesData = branchesData.map(b => b.total_sales);
                const expensesData = branchesData.map(b => b.total_expenses);
                const branchCount = branchesData.length;

                // Narrow bars when many branches so all columns fit on print/export
                const barWidth = branchCount > 10 ? 0.35 : branchCount > 6 ? 0.45 : branchCount > 3 ? 0.6 : 0.75;
                if (branchesData.length > 1) {
                    new Chart(document.getElementById('branchesSalesExpensesChart'), {
                        type: 'bar',
                        data: {
                            labels: branchNames,
                            datasets: [
                                {
                                    label: 'المبيعات',
                                    data: salesData,
                                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                    borderRadius: 6,
                                },
                                {
                                    label: 'المصروفات',
                                    data: expensesData,
                                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                    borderRadius: 6,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    barPercentage: barWidth,
                                    categoryPercentage: barWidth,
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45,
                                    },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString();
                                                },
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
        </script>
    <!-- Branch-wise Grouped Data and Charts -->
    <?php $__currentLoopData = $branchesComparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $branchId = $branch['branch_id'] ?? 0;
        $branchEmployees = $employeesComparison->where('branch_id', $branchId)->sortByDesc('total_sales');
        $branchCategories = $categoriesComparison->where('branch_id', $branchId);
        $branchCalibers = $calibersComparison->where('branch_id', $branchId);
    ?>
    <fieldset class="border rounded-3 p-3 mb-4">
        <legend class="float-none w-auto px-3 fs-5 fw-bold text-primary" style="cursor: pointer;" onclick="toggleBranchSection(<?php echo e($index); ?>)">
            <i class="mdi mdi-chevron-down" id="branchIcon<?php echo e($index); ?>"></i> <?php echo e($branch['branch_name']); ?>

        </legend>
        
        <div id="branchContent<?php echo e($index); ?>">
        <!-- Branch Summary Stats -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">المبيعات</small>
                        <div class="fs-5 fw-bold text-success"><?php echo e(number_format($branch['total_sales'] ?? 0, 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">المصروفات</small>
                        <div class="fs-5 fw-bold text-danger"><?php echo e(number_format($branch['expenses'] ?? $branch['total_expenses'] ?? 0, 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-secondary h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">الوزن</small>
                        <div class="fs-5 fw-bold"><?php echo e(number_format($branch['total_weight'] ?? 0, 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">عدد المبيعات</small>
                        <div class="fs-5 fw-bold text-info"><?php echo e($branch['sales_count'] ?? 0); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">معدل سعر الجرام</small>
                        <div class="fs-5 fw-bold text-warning">
                            <?php if(($branch['total_weight'] ?? 0) > 0 && ($branch['total_sales'] ?? 0) > 0): ?>
                                <?php echo e(number_format($branch['total_sales'] / $branch['total_weight'], 2)); ?>

                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">معدل الجرام للموظف</small>
                        <div class="fs-5 fw-bold text-info">
                            <?php if($branchEmployees->count() > 0 && ($branch['total_weight'] ?? 0) > 0): ?>
                                <?php echo e(number_format($branch['total_weight'] / $branchEmployees->count(), 2)); ?>

                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">الربح</small>
                        <div class="fs-5 fw-bold text-primary"><?php echo e(number_format($branch['profit'] ?? 0, 2)); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branch Sales & Expenses Chart -->
        <div class="row g-3">
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
                                    <?php $__empty_1 = true; $__currentLoopData = $branchEmployees->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($employee['employee_name']); ?></td>
                                        <td class="text-end"><?php echo e(number_format($employee['total_sales'], 2)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($employee['total_weight'] ?? 0, 2)); ?></td>
                                        <td class="text-end text-warning fw-bold">
                                            <?php if(($employee['total_weight'] ?? 0) > 0): ?>
                                                <?php echo e(number_format($employee['total_sales'] / $employee['total_weight'], 2)); ?>

                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                    <?php endif; ?>
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
                                    <?php $__empty_1 = true; $__currentLoopData = $branchCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($category['category_name']); ?></td>
                                        <td class="text-end"><?php echo e(number_format($category['total_sales'] ?? 0, 2)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($category['total_weight'] ?? 0, 2)); ?></td>
                                        <td class="text-end"><?php echo e($category['items_count'] ?? 0); ?></td>
                                        <td class="text-end text-warning fw-bold">
                                            <?php if(($category['total_weight'] ?? 0) > 0 && ($category['total_sales'] ?? 0) > 0): ?>
                                                <?php echo e(number_format($category['total_sales'] / $category['total_weight'], 2)); ?>

                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div><!-- End branchContent -->
    </fieldset>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const branchesData = <?php echo json_encode($branchesComparison, 15, 512) ?>;
        branchesData.forEach((branch, index) => {
            const canvas = document.getElementById('branchSalesExpensesChart' + index);
            if (canvas) {
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: ['المبيعات', 'المصروفات'],
                        datasets: [
                            {
                                label: branch.branch_name,
                                data: [branch.total_sales || 0, branch.total_expenses || 0],
                                backgroundColor: ['rgba(40, 167, 69, 0.7)', 'rgba(220, 53, 69, 0.7)'],
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed.y.toLocaleString();
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
    });

    // Define colors for charts
    const chartColors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(199, 199, 199, 0.7)',
        'rgba(83, 102, 255, 0.7)',
        'rgba(255, 99, 255, 0.7)',
        'rgba(99, 255, 132, 0.7)'
    ];

    // Two Branch Main Comparison Chart
    <?php if(isset($twoBranchComparison) && $showTwoBranchComparison): ?>
    (function() {
    const colors = {
        primary: 'rgba(54, 162, 235, 0.7)',
        danger: 'rgba(220, 53, 69, 0.7)',
        success: 'rgba(40, 167, 69, 0.7)',
        warning: 'rgba(255, 193, 7, 0.7)',
        info: 'rgba(23, 162, 184, 0.7)'
    };
    
    const branch1Data = {
        name: <?php echo json_encode($twoBranchComparison["branch1"]["name"], 15, 512) ?>,
        sales: <?php echo json_encode($twoBranchComparison['branch1']['sales'], 15, 512) ?>,
        expenses: <?php echo json_encode($twoBranchComparison['branch1']['expenses'], 15, 512) ?>,
        profit: <?php echo json_encode($twoBranchComparison['branch1']['profit'], 15, 512) ?>,
        weight: <?php echo json_encode($twoBranchComparison['branch1']['weight'], 15, 512) ?>,
        count: <?php echo json_encode($twoBranchComparison['branch1']['count'], 15, 512) ?>
    };

    const branch2Data = {
        name: <?php echo json_encode($twoBranchComparison["branch2"]["name"], 15, 512) ?>,
        sales: <?php echo json_encode($twoBranchComparison['branch2']['sales'], 15, 512) ?>,
        expenses: <?php echo json_encode($twoBranchComparison['branch2']['expenses'], 15, 512) ?>,
        profit: <?php echo json_encode($twoBranchComparison['branch2']['profit'], 15, 512) ?>,
        weight: <?php echo json_encode($twoBranchComparison['branch2']['weight'], 15, 512) ?>,
        count: <?php echo json_encode($twoBranchComparison['branch2']['count'], 15, 512) ?>
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
    const employeesData = <?php echo json_encode($employeesComparison, 15, 512) ?>;
    const branchesData = <?php echo json_encode($branchesComparison, 15, 512) ?>;
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
                        backgroundColor: branchEmployees.map(() => chartColors[0]),
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
        }
    });

    // Categories Comparison Chart
    const categoriesData = <?php echo json_encode($categoriesComparison, 15, 512) ?>;
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
    const calibersData = <?php echo json_encode($calibersComparison, 15, 512) ?>;
    const branch1Calibers = calibersData.filter(cal => cal.branch_id == branch1Data.id);
    const branch2Calibers = calibersData.filter(cal => cal.branch_id == branch2Data.id);
    // Get all unique caliber names
    const caliberNames = [...new Set(calibersData.map(c => c.name))];
    // Prepare datasets for each branch
    const branchesData = <?php echo json_encode($branchesComparison, 15, 512) ?>;
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

    })(); // End of IIFE for two-branch comparison
    <?php endif; ?>

    // CSV Export Function
    function exportToCSV() {
        let csvContent = '\uFEFF'; // UTF-8 BOM for Arabic support
        const separator = ',';
        
        <?php if($showTwoBranchComparison && isset($twoBranchComparison)): ?>
            // Two Branch Comparison Export
            const branch1Data = {
                name: <?php echo json_encode($twoBranchComparison["branch1"]["name"], 15, 512) ?>,
                sales: <?php echo json_encode($twoBranchComparison['branch1']['sales'], 15, 512) ?>,
                expenses: <?php echo json_encode($twoBranchComparison['branch1']['expenses'], 15, 512) ?>,
                profit: <?php echo json_encode($twoBranchComparison['branch1']['profit'], 15, 512) ?>,
                weight: <?php echo json_encode($twoBranchComparison['branch1']['weight'], 15, 512) ?>,
                count: <?php echo json_encode($twoBranchComparison['branch1']['count'], 15, 512) ?>
            };
            const branch2Data = {
                name: <?php echo json_encode($twoBranchComparison["branch2"]["name"], 15, 512) ?>,
                sales: <?php echo json_encode($twoBranchComparison['branch2']['sales'], 15, 512) ?>,
                expenses: <?php echo json_encode($twoBranchComparison['branch2']['expenses'], 15, 512) ?>,
                profit: <?php echo json_encode($twoBranchComparison['branch2']['profit'], 15, 512) ?>,
                weight: <?php echo json_encode($twoBranchComparison['branch2']['weight'], 15, 512) ?>,
                count: <?php echo json_encode($twoBranchComparison['branch2']['count'], 15, 512) ?>
            };
            const branch1Employees = <?php echo json_encode($twoBranchComparison['branch1']['employees'] ?? [], 15, 512) ?>;
            const branch2Employees = <?php echo json_encode($twoBranchComparison['branch2']['employees'] ?? [], 15, 512) ?>;
            const branch1PricePerGram = branch1Data.weight > 0 ? branch1Data.sales / branch1Data.weight : 0;
            const branch2PricePerGram = branch2Data.weight > 0 ? branch2Data.sales / branch2Data.weight : 0;
            
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
            <?php if(isset($twoBranchComparison)): ?>
            const branch1PricePerGram = <?php echo e($twoBranchComparison['branch1']['weight'] > 0 ? $twoBranchComparison['branch1']['sales'] / $twoBranchComparison['branch1']['weight'] : 0); ?>;
            const branch2PricePerGram = <?php echo e($twoBranchComparison['branch2']['weight'] > 0 ? $twoBranchComparison['branch2']['sales'] / $twoBranchComparison['branch2']['weight'] : 0); ?>;
            const branch1Employees = <?php echo json_encode($twoBranchComparison['branch1']['employees'] ?? [], 15, 512) ?>;
            const branch2Employees = <?php echo json_encode($twoBranchComparison['branch2']['employees'] ?? [], 15, 512) ?>;
            csvContent += `"${branch1Data.name}","${branch1PricePerGram}"\n`;
            csvContent += `"${branch2Data.name}","${branch2PricePerGram}"\n`;
            <?php endif; ?>
            
        <?php else: ?>
            // General Comparison Export
            csvContent += 'التقرير المقارن\n\n';
            
            // Branches Comparison
            csvContent += 'مقارنة الفروع\n';
            csvContent += 'الفرع,المبيعات,المصروفات,الربح\n';
            <?php $__currentLoopData = $branchesComparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                csvContent += `"<?php echo e($branch['branch_name']); ?>","<?php echo e($branch['total_sales'] ?? 0); ?>","<?php echo e($branch['total_expenses'] ?? 0); ?>","<?php echo e(($branch['total_sales'] ?? 0) - ($branch['total_expenses'] ?? 0)); ?>"\n`;
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            csvContent += '\nمقارنة الموظفين\n';
            csvContent += 'الموظف,المبيعات,عدد المبيعات\n';
            <?php $__currentLoopData = $employeesComparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                csvContent += `"<?php echo e($employee['employee_name']); ?>","<?php echo e($employee['total_sales'] ?? 0); ?>","<?php echo e($employee['sales_count'] ?? ($employee['count'] ?? 0)); ?>"\n`;
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            csvContent += '\nمقارنة الفئات\n';
            csvContent += 'الفئة,المبيعات,عدد المبيعات\n';
            <?php $__currentLoopData = $categoriesComparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                csvContent += `"<?php echo e($category['category_name']); ?>","<?php echo e($category['total_sales'] ?? 0); ?>","<?php echo e($category['items_count'] ?? ($category['count'] ?? 0)); ?>"\n`;
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            csvContent += '\nمقارنة العيارات\n';
            csvContent += 'العيار,المبيعات,عدد المبيعات\n';
            <?php $__currentLoopData = $calibersComparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caliber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                csvContent += `"<?php echo e($caliber['name'] ?? ''); ?>","<?php echo e($caliber['total_sales'] ?? ($caliber['sales'] ?? 0)); ?>","<?php echo e($caliber['items_count'] ?? ($caliber['count'] ?? 0)); ?>"\n`;
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            csvContent += '\nمقارنة طرق الدفع\n';
            csvContent += 'طريقة الدفع,المبلغ,عدد المبيعات\n';
            <?php $__currentLoopData = $paymentMethodsComparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                csvContent += `"<?php echo e($method['name']); ?>","<?php echo e($method['amount']); ?>","<?php echo e($method['count']); ?>"\n`;
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'التقرير المقارن'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/reports/comparative.blade.php ENDPATH**/ ?>