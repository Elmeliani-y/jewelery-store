<?php $__env->startSection('css'); ?>
<style>
    .expenses-table {
        font-size: 0.9rem;
    }
    .expenses-table th {
        background-color: var(--bs-danger);
        color: white;
        font-weight: 600;
        white-space: nowrap;
    }
    .expenses-table td {
        vertical-align: middle;
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
        .expenses-table {
            font-size: 0.75rem;
        }
        .expenses-table th, .expenses-table td {
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
                        مصروفات اليوم - <?php echo e(today()->format('Y-m-d')); ?>

                    </h4>
                </div>
                <div class="text-end">
                    <a href="<?php echo e(route('l7m2n6o1.create')); ?>" class="btn btn-danger">
                        <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                        تسجيل مصروف جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-danger);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-1 text-danger"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي المصروفات</h6>
                            <h4 class="mb-0"><?php echo e(number_format($totalExpenses, 2)); ?> <small>ريال</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card summary-card" style="border-left-color: var(--bs-info);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-1 text-info"></iconify-icon>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">عدد المصروفات</h6>
                            <h4 class="mb-0"><?php echo e($expenses->count()); ?> <small>مصروف</small></h4>
                        </div>
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
                    <form method="GET" action="<?php echo e(route('r8s3t7u1.p4q9r5s2')); ?>" class="row g-3 align-items-end arabic-text">
                        <div class="col-md-6">
                            <label for="expense_type_id" class="form-label">تصفية حسب نوع المصروف</label>
                            <select name="expense_type_id" id="expense_type_id" class="form-select">
                                <option value="">جميع الأنواع</option>
                                <?php $__currentLoopData = $expenseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" <?php echo e(request('expense_type_id') == $type->id ? 'selected' : ''); ?>>
                                        <?php echo e($type->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-danger w-100">
                                <iconify-icon icon="solar:filter-bold"></iconify-icon>
                                تطبيق الفلتر
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 arabic-text">
                        <iconify-icon icon="solar:list-bold-duotone" class="me-2"></iconify-icon>
                        المصروفات (<?php echo e($expenses->count()); ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if($expenses->isEmpty()): ?>
                        <div class="text-center py-5">
                            <iconify-icon icon="solar:wallet-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                            <p class="text-muted">لا توجد مصروفات اليوم</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover expenses-table mb-0 arabic-text">
                                <thead>
                                    <tr>
                                        <th>نوع المصروف</th>
                                        <th>الوصف</th>
                                        <th>المبلغ</th>
                                        <th>التاريخ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <iconify-icon icon="solar:tag-bold-duotone" class="text-danger me-1"></iconify-icon>
                                                <?php echo e($expense->expenseType->name); ?>

                                            </td>
                                            <td><?php echo e($expense->description ?? 'بدون وصف'); ?></td>
                                            <td class="fw-semibold text-danger"><?php echo e(number_format($expense->amount, 2)); ?> ر.س</td>
                                            <td><?php echo e($expense->expense_date->format('Y-m-d H:i')); ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <?php if(!auth()->user() || !auth()->user()->isBranch()): ?>
                                                        <a href="<?php echo e(route('l7m2n6o1.edit', $expense)); ?>" class="btn btn-sm btn-primary edit-btn" title="تعديل">
                                                            <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo e(route('l7m2n6o1.show', $expense)); ?>" class="btn btn-sm btn-info edit-btn" title="عرض">
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
    const expenseTypeSelect = document.getElementById('expense_type_id');
    
    if (expenseTypeSelect) {
        expenseTypeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'المصروفات اليومية'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/expenses/daily.blade.php ENDPATH**/ ?>