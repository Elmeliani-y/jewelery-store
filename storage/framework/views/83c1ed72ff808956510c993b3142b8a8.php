<?php $__env->startSection('title'); ?> المصروفات <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .page-header { background: var(--bs-primary); padding:1.75rem 2rem; border-radius:.75rem; color:#fff; margin-bottom:1.75rem; }
    [data-bs-theme="dark"] .page-header { background: var(--bs-primary); }
    .expenses-table td, .expenses-table th { white-space: nowrap; }
    .expenses-table .badge { font-weight:500; }
    .table-card { padding:0; }
    .pagination .page-link { font-size:.75rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-1">
                    <i class="ri-wallet-3-line me-2"></i>
                    إدارة المصروفات
                </h3>
                <p class="mb-0 opacity-75">عرض ومتابعة جميع المصروفات المسجلة</p>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('l7m2n6o1.create')); ?>" class="btn btn-light">
                    <i class="ri-add-line me-1"></i>
                    إضافة مصروف جديد
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters: Branch, Dates, Expense ID -->
    <form method="GET" class="mb-3 row g-2 align-items-end">
        <div class="col-md-2">
            <label for="branch_id" class="form-label mb-1">الفرع</label>
            <select name="branch_id" id="branch_id" class="form-select">
                <option value="">كل الفروع</option>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="expense_type_id" class="form-label mb-1">نوع المصروف</label>
            <select name="expense_type_id" id="expense_type_id" class="form-select">
                <option value="">كل الأنواع</option>
                <?php $__currentLoopData = $expenseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->id); ?>" <?php echo e(request('expense_type_id') == $type->id ? 'selected' : ''); ?>><?php echo e($type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label mb-1">من تاريخ</label>
            <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
        </div>
        <div class="col-md-2">
            <label for="id" class="form-label mb-1">رقم المصروف</label>
            <input type="number" name="id" id="id" class="form-control" placeholder="بحث برقم المصروف" value="<?php echo e(request('id')); ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit" style="margin-top: 2px;">بحث</button>
        </div>
    </form>

    <!-- Expenses List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <?php if($expenses->count() > 0): ?>
                    <div class="table-responsive table-card expenses-table">
                        <table class="table table-borderless table-centered align-middle table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">رقم المصروف</th>
                                    <th scope="col">الفرع</th>
                                    <th scope="col">نوع المصروف</th>
                                    <th scope="col">الوصف</th>
                                    <th scope="col">المبلغ</th>
                                    <th scope="col">تاريخ المصروف</th>
                                    <th scope="col" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary"><?php echo e($expense->id); ?></span>
                                    </td>
                                    <td class="text-muted"><?php echo e($expense->branch->name); ?></td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info"><?php echo e($expense->expenseType->name); ?></span>
                                    </td>
                                    <td class="text-muted"><?php echo e($expense->description); ?></td>
                                    <td dir="ltr"><span class="fw-semibold text-dark"><?php echo e(number_format($expense->amount, 0, ',', '.')); ?> <small class="text-muted">ريال</small></span></td>
                                    <td class="text-muted"><?php echo e($expense->expense_date->format('Y-m-d')); ?></td>
                                    <td class="text-center">
                                        <?php if(!request()->filled('id')): ?>
                                            <a href="<?php echo e(route('l7m2n6o1.show', $expense)); ?>" class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip" data-bs-original-title="عرض">
                                                <i class="mdi mdi-eye-outline text-info fs-16"></i>
                                            </a>
                                            <a href="<?php echo e(route('l7m2n6o1.edit', $expense)); ?>" class="btn btn-icon btn-sm bg-warning-subtle" data-bs-toggle="tooltip" data-bs-original-title="تعديل">
                                                <i class="mdi mdi-pencil-outline text-warning fs-16"></i>
                                            </a>
                                            <form action="<?php echo e(route('l7m2n6o1.destroy', $expense)); ?>" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="حذف">
                                                    <i class="mdi mdi-delete-outline text-danger fs-16"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $ijmaliCount = $expenses->count();
                                    $ijmaliMabalegh = $expenses->sum('amount');
                                ?>
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td colspan="4">الإجماليات</td>
                                        <td class="text-end"><?php echo e(number_format($ijmaliMabalegh, 2)); ?> <small class="text-muted">ريال</small></td>
                                        <td></td>
                                        <td class="text-end">عدد المصروفات: <?php echo e($ijmaliCount); ?></td>
                                    </tr>
                                </tfoot
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                            <small class="text-muted">
                                عرض <?php echo e($expenses->firstItem()); ?>–<?php echo e($expenses->lastItem()); ?> من أصل <?php echo e($expenses->total()); ?>

                            </small>
                        </div>
                        <?php echo e($expenses->links('pagination::bootstrap-5')); ?>

                    </div>
                    <?php else: ?>
                    <div class="py-5 text-center">
                        <i class="mdi mdi-wallet-outline" style="font-size:3.5rem; color:#adb5bd;"></i>
                        <h5 class="text-muted mt-3">لا توجد مصروفات</h5>
                        <p class="text-muted">قم بإضافة مصروف جديد للبدء</p>
                        <?php if(!request()->filled('id')): ?>
                        <a href="<?php echo e(route('l7m2n6o1.create')); ?>" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            إضافة مصروف
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'المصروفات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/expenses/index.blade.php ENDPATH**/ ?>