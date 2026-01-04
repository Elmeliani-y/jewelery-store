<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('components.form-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تفاصيل المصروف</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('c5d9f2h7')); ?>">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('l7m2n6o1.index')); ?>">المصروفات</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">المعلومات الأساسية</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th>رقم المصروف</th>
                                    <td><?php echo e($expense->id); ?></td>
                                </tr>
                                <tr>
                                    <th>الفرع</th>
                                    <td><?php echo e($expense->branch->name); ?></td>
                                </tr>
                                <tr>
                                    <th>نوع المصروف</th>
                                    <td><?php echo e($expense->expenseType->name); ?></td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td><?php echo e($expense->description); ?></td>
                                </tr>
                                <tr>
                                    <th>المبلغ</th>
                                    <td dir="ltr"><?php echo e(number_format($expense->amount, 2)); ?> ريال</td>
                                </tr>
                                <tr>
                                    <th>تاريخ المصروف</th>
                                    <td><?php echo e($expense->expense_date->format('Y-m-d')); ?></td>
                                </tr>
                                <?php if($expense->notes): ?>
                                <tr>
                                    <th>ملاحظات</th>
                                    <td><?php echo e($expense->notes); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الإجراءات</h5>
                    <div class="d-grid gap-2">
                        <?php if(!auth()->user() || !auth()->user()->isBranch()): ?>
                            <a href="<?php echo e(route('l7m2n6o1.edit', $expense)); ?>" class="btn btn-warning">
                                <i class="mdi mdi-pencil-outline me-1"></i> تعديل
                            </a>
                            <form action="<?php echo e(route('l7m2n6o1.destroy', $expense)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger">
                                    <i class="mdi mdi-delete-outline me-1"></i> حذف
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if(auth()->user() && auth()->user()->isBranch()): ?>
                            <a href="<?php echo e(route('r8s3t7u1.p4q9r5s2')); ?>" class="btn btn-light">عودة إلى المصروفات اليومية</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('l7m2n6o1.index')); ?>" class="btn btn-light">عودة إلى المصروفات</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.vertical', ['title' => 'تفاصيل المصروف'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/expenses/show.blade.php ENDPATH**/ ?>