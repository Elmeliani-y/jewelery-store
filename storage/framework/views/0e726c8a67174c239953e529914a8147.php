
<?php $__env->startSection('title', 'تقرير الحسابات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">تقرير الحسابات</h4>
            </div>
        </div>
    </div>
    <form method="GET" action="<?php echo e(route('t3u8v1w4.a3b7c1d5')); ?>" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="branch_id" class="form-label">الفرع</label>
                <select name="branch_id" id="branch_id" class="form-select">
                    <option value="">اختر الفرع</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from', date('Y-m-01'))); ?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to', date('Y-m-d'))); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">عرض التقرير</button>
            </div>
        </div>
    </form>
    <?php if(isset($summary)): ?>
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">ملخص الحسابات</h5>
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="alert alert-info">المبلغ شبكة: <b><?php echo e(number_format($summary['network'], 2)); ?></b></div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">المبلغ نقدي: <b><?php echo e(number_format($summary['cash'], 2)); ?></b></div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">المبلغ تحويل: <b><?php echo e(number_format($summary['transfer'], 2)); ?></b></div>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <?php $__env->stopPush(); ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'تقرير الحسابات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/reports/accounts.blade.php ENDPATH**/ ?>