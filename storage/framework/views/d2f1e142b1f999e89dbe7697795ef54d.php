<?php $__env->startSection('css'); ?>
<style>
    .sales-table {
        font-size: 0.9rem;
    }
    .sales-table th {
        background-color: var(--bs-primary);
        color: white;
        font-weight: 600;
        white-space: nowrap;
    }
    .sales-table td {
        vertical-align: middle;
    }
    .badge-received {
        font-size: 0.75rem;
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
        .sales-table {
            font-size: 0.75rem;
        }
        .sales-table th, .sales-table td {
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0 arabic-text">
                        <iconify-icon icon="solar:calendar-bold-duotone" class="me-2"></iconify-icon>
                        مبيعات اليوم - <?php echo e(today()->format('Y-m-d')); ?>

                    </h4>
                </div>
                <div class="text-end">
                    <a href="<?php echo e(route('t6u1v5w8.create')); ?>" class="btn btn-primary">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                        تسجيل مبيعة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-primary);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:scale-bold-duotone" class="fs-1 text-primary"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">الوزن الكلي</h6>
                            <h4 class="mb-0"><?php echo e(number_format($totalWeight, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-success);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-1 text-success"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي المبيعات</h6>
                            <h4 class="mb-0"><?php echo e(number_format($totalAmount, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-info);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:cash-out-bold-duotone" class="fs-1 text-info"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">كاش فقط</h6>
                            <h4 class="mb-0"><?php echo e(number_format($cashOnlyTotal, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-danger);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:card-bold-duotone" class="fs-1 text-danger"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">شبكة فقط</h6>
                            <h4 class="mb-0"><?php echo e(number_format($networkOnlyTotal, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Snap (تحويل) summary card with white background removed as requested -->
    </div>
    
    <!-- Second Row for Average -->
    <div class="row mb-3">
        <div class="col-md-6 col-sm-12 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-warning);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:calculator-bold-duotone" class="fs-1 text-warning"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">متوسط سعر الجرام</h6>
                            <h4 class="mb-0"><?php echo e(number_format($averageRate, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <div class="card summary-card" style="border-left-color: #6f42c1;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:bank-bold-duotone" class="fs-1" style="color: #6f42c1;"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">مبيعات سناب (تحويل)</h6>
                            <h4 class="mb-0"><?php echo e(number_format($snapTotal, 2)); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Summary Cards (copied from expenses/daily.blade.php) -->
    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <div class="card summary-card" style="border-left-color: #0d6efd; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                <div class="card-body text-center">
                    <iconify-icon icon="solar:cart-bold-duotone" class="fs-1 text-primary mb-2"></iconify-icon>
                    <div class="sales-summary-label mb-1">إجمالي المبيعات (كل العمليات)</div>
                    <div class="sales-summary-value">
                        <?php echo e(isset($salesSumAll) ? number_format($salesSumAll, 2) : '0.00'); ?> <small>ريال</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card summary-card" style="border-left-color: #6610f2; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                <div class="card-body text-center">
                    <iconify-icon icon="solar:cart-large-bold-duotone" class="fs-1 text-purple mb-2"></iconify-icon>
                    <div class="sales-summary-label mb-1">إجمالي المبيعات (بدون تحويل)</div>
                    <div class="sales-summary-value">
                        <?php echo e(isset($salesSumWithoutTransfer) ? number_format($salesSumWithoutTransfer, 2) : '0.00'); ?> <small>ريال</small>
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
                    <form method="GET" action="<?php echo e(route('r8s3t7u1.v4w9x2y5')); ?>" class="row g-3 align-items-end arabic-text">
                        <div class="col-md-4">
                            <label for="employee_id" class="form-label">تصفية حسب الموظف</label>
                            <select name="employee_id" id="employee_id" class="form-select">
                                <option value="">جميع الموظفين</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>" <?php echo e(request('employee_id') == $employee->id ? 'selected' : ''); ?>>
                                        <?php echo e($employee->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="customer_received" class="form-label">حالة استلام العميل</label>
                            <select name="customer_received" id="customer_received" class="form-select">
                                <option value="">الكل</option>
                                <option value="yes" <?php echo e(request('customer_received') == 'yes' ? 'selected' : ''); ?>>استلم</option>
                                <option value="no" <?php echo e(request('customer_received') == 'no' ? 'selected' : ''); ?>>لم يستلم</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <iconify-icon icon="solar:filter-bold"></iconify-icon>
                                تطبيق الفلتر
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 arabic-text">
                        <iconify-icon icon="solar:list-bold-duotone" class="me-2"></iconify-icon>
                        الفواتير (<?php echo e($sales->count()); ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if($sales->isEmpty()): ?>
                        <div class="text-center py-5">
                            <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                            <p class="text-muted">لا توجد مبيعات اليوم</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover sales-table mb-0 arabic-text">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>الصنف</th>
                                        <th>العيار</th>
                                        <th>الوزن</th>
                                        <th>المبلغ</th>
                                        <th>سعر الجرام</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $firstProduct = $sale->products[0] ?? null;
                                        ?>
                                        <tr>
                                            <td>
                                                <iconify-icon icon="solar:user-bold-duotone" class="text-primary me-1"></iconify-icon>
                                                <?php echo e($sale->employee->name); ?>

                                            </td>
                                            <td><?php echo e($firstProduct['category_name'] ?? 'غير محدد'); ?></td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    <?php echo e($firstProduct['caliber_name'] ?? 'غير محدد'); ?>

                                                </span>
                                            </td>
                                            <td class="fw-semibold"><?php echo e(number_format($sale->weight, 2)); ?></td>
                                            <td class="fw-semibold text-success"><?php echo e(number_format($sale->total_amount, 2)); ?></td>
                                            <td><?php echo e(number_format($sale->weight > 0 ? $sale->total_amount / $sale->weight : 0, 2)); ?></td>
                                            <td>
                                                <?php if($sale->customer_received): ?>
                                                    <span class="badge bg-success-subtle text-success badge-received">
                                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                        استلم
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning badge-received">
                                                        <iconify-icon icon="solar:clock-circle-bold"></iconify-icon>
                                                        لم يستلم
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <?php if(!auth()->user()->isBranch()): ?>
                                                    <a href="<?php echo e(route('t6u1v5w8.edit', $sale)); ?>" class="btn btn-sm btn-primary edit-btn" title="تعديل">
                                                        <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                                    </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo e(route('t6u1v5w8.show', $sale)); ?>" class="btn btn-sm btn-info edit-btn" title="عرض">
                                                        <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const employeeSelect = document.getElementById('employee_id');
    const customerReceivedSelect = document.getElementById('customer_received');
    
    if (employeeSelect) {
        employeeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (customerReceivedSelect) {
        customerReceivedSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'المبيعات اليومية'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/sales/daily.blade.php ENDPATH**/ ?>