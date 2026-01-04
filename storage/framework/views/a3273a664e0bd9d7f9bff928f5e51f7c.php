<?php $__env->startSection('title','التقرير السريع'); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('reports.partials.print-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .metric-card {
        border-radius: 12px;
        border: 1px solid var(--bs-border-color);
        transition: all 0.2s;
        background: var(--bs-body-bg);
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }
    .metric-label {
        font-size: 0.875rem;
        opacity: 0.7;
        margin-top: 0.25rem;
    }
    .metric-icon {
        font-size: 2rem;
        opacity: 0.2;
    }
    .speed-table {
        font-size: 0.875rem;
    }
    .speed-table th {
        background: var(--bs-light);
        font-weight: 600;
        padding: 0.75rem;
    }
    .speed-table td {
        padding: 0.75rem;
    }
    .print-fab {
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        cursor: pointer;
        transition: all 0.3s;
        z-index: 1000;
    }
    .print-fab:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0,0,0,.2);
    }
    @media print {
        .no-print { display: none !important; }
        .metric-card { page-break-inside: avoid; }
        .print-fab { display: none !important; }
        body { background: white !important; }
        .card { border: 1px solid #ddd !important; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>التقرير السريع</h2>
        <p>التاريخ: <?php echo e(request('date_from') ?? date('Y-m-d')); ?> - <?php echo e(request('date_to') ?? date('Y-m-d')); ?></p>
        <?php if(request('branch_id')): ?>
            <p>الفرع: <?php echo e($branches->firstWhere('id', request('branch_id'))->name ?? 'جميع الفروع'); ?></p>
        <?php endif; ?>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="mb-1"><i class="mdi mdi-speedometer text-primary"></i> التقرير السريع</h4>
            <p class="text-muted mb-0">عرض سريع للمقاييس الرئيسية</p>
        </div>
        <div>
            <a href="<?php echo e(route('t3u8v1w4.b1c5d8e3')); ?>" class="btn btn-light me-2">
                <i class="mdi mdi-arrow-right"></i> التقرير الشامل
            </a>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-download"></i> تصدير
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo e(route('t3u8v1w4.f4g9h2i7', array_merge(request()->query(), ['format'=>'pdf']))); ?>">PDF</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('t3u8v1w4.f4g9h2i7', array_merge(request()->query(), ['format'=>'excel']))); ?>">Excel</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('t3u8v1w4.f4g9h2i7', array_merge(request()->query(), ['format'=>'csv']))); ?>">CSV</a></li>
                </ul>
            </div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="mdi mdi-printer"></i> طباعة
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('t3u8v1w4.f4g9h2i7')); ?>" class="row g-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">التاريخ</label>
                    <input type="date" name="date" id="date" value="<?php echo e($date); ?>" class="form-control" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label for="branch_id" class="form-label">الفرع</label>
                    <select name="branch_id" id="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">جميع الفروع</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php echo e($branchId == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="<?php echo e(route('t3u8v1w4.f4g9h2i7')); ?>" class="btn btn-secondary">
                        <i class="mdi mdi-refresh"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3 position-relative overflow-hidden">
                <i class="mdi mdi-cash-multiple metric-icon position-absolute end-0 bottom-0 me-2"></i>
                <div class="position-relative">
                    <p class="metric-value text-primary"><?php echo e(number_format($metrics['sales_total'], 0)); ?></p>
                    <p class="metric-label mb-0">إجمالي المبيعات (د.ل)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3 position-relative overflow-hidden">
                <i class="mdi mdi-receipt metric-icon position-absolute end-0 bottom-0 me-2"></i>
                <div class="position-relative">
                    <p class="metric-value text-info"><?php echo e($metrics['sales_count']); ?></p>
                    <p class="metric-label mb-0">عدد الفواتير</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3 position-relative overflow-hidden">
                <i class="mdi mdi-wallet metric-icon position-absolute end-0 bottom-0 me-2"></i>
                <div class="position-relative">
                    <p class="metric-value text-danger"><?php echo e(number_format($metrics['expenses_total'], 0)); ?></p>
                    <p class="metric-label mb-0">إجمالي المصروفات (د.ل)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3 position-relative overflow-hidden">
                <i class="mdi mdi-chart-line metric-icon position-absolute end-0 bottom-0 me-2"></i>
                <div class="position-relative">
                    <p class="metric-value text-success"><?php echo e(number_format($metrics['profit'], 0)); ?></p>
                    <p class="metric-label mb-0">صافي الربح (د.ل)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3">
                <p class="metric-value text-secondary" style="font-size: 1.5rem;"><?php echo e(number_format($metrics['sales_weight'], 2)); ?></p>
                <p class="metric-label mb-0">إجمالي الوزن (جم)</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3">
                <p class="metric-value text-warning" style="font-size: 1.5rem;"><?php echo e(number_format($metrics['price_per_gram'], 2)); ?></p>
                <p class="metric-label mb-0">متوسط سعر الجرام (د.ل)</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3">
                <p class="metric-value text-success" style="font-size: 1.5rem;"><?php echo e(number_format($metrics['cash_amount'], 0)); ?></p>
                <p class="metric-label mb-0">مبلغ نقدي (د.ل)</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="metric-card p-3">
                <p class="metric-value text-info" style="font-size: 1.5rem;"><?php echo e(number_format($metrics['network_amount'], 0)); ?></p>
                <p class="metric-label mb-0">مبلغ شبكة (د.ل)</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Top Employees -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="mdi mdi-account-star text-primary"></i> أفضل 5 موظفين</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm speed-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th class="text-center">العدد</th>
                                    <th class="text-end">المبيعات (د.ل)</th>
                                    <th class="text-end">الوزن (جم)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $topEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><strong><?php echo e($emp->name); ?></strong></td>
                                    <td class="text-center"><?php echo e($emp->sales_count); ?></td>
                                    <td class="text-end"><?php echo e(number_format($emp->total_sales, 0)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($emp->total_weight, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">لا توجد بيانات</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                                <?php $empRows = $topEmployees instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($topEmployees->items()) : collect($topEmployees); ?>
                                <?php if($empRows->count()): ?>
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td colspan="2">الإجماليات</td>
                                        <td class="text-center"><?php echo e($empRows->sum('sales_count')); ?></td>
                                        <td class="text-end"><?php echo e(number_format($empRows->sum('total_sales'), 0)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($empRows->sum('total_weight'), 2)); ?></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales by Caliber -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="mdi mdi-gold text-warning"></i> المبيعات حسب العيار</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm speed-table mb-0">
                            <thead>
                                <tr>
                                    <th>العيار</th>
                                    <th class="text-center">العدد</th>
                                    <th class="text-end">المبلغ (د.ل)</th>
                                    <th class="text-end">الوزن (جم)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $salesByCaliber; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caliber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?php echo e($caliber->name); ?></span></td>
                                    <td class="text-center"><?php echo e($caliber->count); ?></td>
                                    <td class="text-end"><?php echo e(number_format($caliber->amount, 0)); ?></td>
                                    <td class="text-end"><?php echo e(number_format($caliber->weight, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">لا توجد بيانات</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                                <?php $caliberRows = collect($salesByCaliber); ?>
                                <?php if($caliberRows->count()): ?>
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td>الإجماليات</td>
                                        <td class="text-center"><?php echo e($caliberRows->sum('count')); ?></td>
                                        <td class="text-end"><?php echo e(number_format($caliberRows->sum('amount'), 0)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($caliberRows->sum('weight'), 2)); ?></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="mdi mdi-credit-card text-success"></i> طرق الدفع</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm speed-table mb-0">
                            <thead>
                                <tr>
                                    <th>الطريقة</th>
                                    <th class="text-center">العدد</th>
                                    <th class="text-end">المبلغ (د.ل)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php if($method->payment_method == 'cash'): ?>
                                            <span class="badge bg-success">نقدي</span>
                                        <?php elseif($method->payment_method == 'network'): ?>
                                            <span class="badge bg-info">شبكة</span>
                                        <?php elseif($method->payment_method == 'transfer' || $method->payment_method == 'snap'): ?>
                                            <span class="badge bg-primary">تحويل</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">مختلط</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e($method->count); ?></td>
                                    <td class="text-end"><?php echo e(number_format($method->amount, 0)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">لا توجد بيانات</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                                <?php $methodRows = collect($paymentMethods); ?>
                                <?php if($methodRows->count()): ?>
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td>الإجماليات</td>
                                        <td class="text-center"><?php echo e($methodRows->sum('count')); ?></td>
                                        <td class="text-end"><?php echo e(number_format($methodRows->sum('amount'), 0)); ?></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Expense Types -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="mdi mdi-currency-usd-off text-danger"></i> أعلى المصروفات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm speed-table mb-0">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th class="text-center">العدد</th>
                                    <th class="text-end">المبلغ (د.ل)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $topExpenseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($type->name); ?></td>
                                    <td class="text-center"><?php echo e($type->count); ?></td>
                                    <td class="text-end text-danger fw-bold"><?php echo e(number_format($type->total, 0)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">لا توجد مصروفات</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                                <?php $expenseRows = collect($topExpenseTypes); ?>
                                <?php if($expenseRows->count()): ?>
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td>الإجماليات</td>
                                        <td class="text-center"><?php echo e($expenseRows->sum('count')); ?></td>
                                        <td class="text-end text-danger fw-bold"><?php echo e(number_format($expenseRows->sum('total'), 0)); ?></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Print Button -->
    <button class="print-fab no-print" onclick="window.print()" title="طباعة التقرير">
        <i class="mdi mdi-printer" style="font-size: 1.5rem;"></i>
    </button>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'التقرير السريع'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/reports/speed.blade.php ENDPATH**/ ?>