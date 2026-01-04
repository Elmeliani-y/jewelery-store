<?php $__env->startSection('css'); ?><style>.stats-box{border:1px solid var(--bs-border-color);border-radius:12px;padding:1rem;} .sales-chip{background:var(--bs-primary-bg-subtle);color:var(--bs-primary);padding:.25rem .6rem;border-radius:20px;font-size:.7rem;font-weight:600;}</style><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:linear-gradient(135deg,#198754,#20c997);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('f3g8h1i4.index')); ?>" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
            <h5 class="mb-0"><iconify-icon icon="solar:user-bold-duotone" class="fs-4 me-1"></iconify-icon> الموظف: <?php echo e($employee->name); ?></h5>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('f3g8h1i4.edit',$employee)); ?>" class="btn btn-primary btn-sm"><iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> تعديل</a>
            <form action="<?php echo e(route('f3g8h1i4.j9k5l2m7',$employee)); ?>" method="POST"><?php echo csrf_field(); ?> <button class="btn btn-<?php echo e($employee->is_active? 'warning':'success'); ?> btn-sm" type="submit"><iconify-icon icon="solar:<?php echo e($employee->is_active? 'eye-closed':'eye'); ?>-bold" class="me-1"></iconify-icon><?php echo e($employee->is_active? 'تعطيل':'تفعيل'); ?></button></form>
            <?php if(!$employee->sales()->exists()): ?>
            <form action="<?php echo e(route('f3g8h1i4.destroy',$employee)); ?>" method="POST" class="delete-form"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?> <button class="btn btn-danger btn-sm" type="submit"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button></form>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $__env->make('layouts.partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">البيانات الأساسية</h6>
                <p class="mb-1"><strong>الحالة:</strong> <span class="badge <?php echo e($employee->is_active? 'bg-success-subtle text-success':'bg-danger-subtle text-danger'); ?>"><?php echo e($employee->is_active? 'نشط':'معطل'); ?></span></p>
                <p class="mb-1"><strong>الفرع:</strong> <?php echo e($employee->branch->name); ?></p>
                <?php if($employee->phone): ?><p class="mb-1"><strong>الهاتف:</strong> <?php echo e($employee->phone); ?></p><?php endif; ?>
                <?php if($employee->email): ?><p class="mb-1"><strong>البريد:</strong> <?php echo e($employee->email); ?></p><?php endif; ?>
                <p class="mb-1"><strong>الراتب:</strong> <?php echo e(number_format($employee->salary,2)); ?> ريال</p>
                <p class="text-muted small mt-2 mb-0">تم الإنشاء في <?php echo e($employee->created_at->format('Y-m-d')); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">إحصائيات المبيعات</h6>
                <div class="d-flex justify-content-between mb-2"><span>عدد المبيعات:</span><strong><?php echo e($salesStats['sales_count']); ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>إجمالي الوزن:</span><strong><?php echo e(number_format($salesStats['total_weight'],2)); ?> جم</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>إجمالي المبيعات:</span><strong><?php echo e(number_format($salesStats['total_sales'],2)); ?> ريال</strong></div>
                <div class="d-flex justify-content-between"><span>مبيعات الشهر:</span><strong><?php echo e(number_format($salesStats['monthly_sales'],2)); ?> ريال</strong></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">آخر 5 مبيعات</h6>
                <?php ($recent=$employee->sales()->notReturned()->latest()->take(5)->get()); ?>
                <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>#<?php echo e($sale->invoice_number); ?></span>
                        <span class="sales-chip"><?php echo e(number_format($sale->total_amount,2)); ?> ر</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small mb-0">لا توجد مبيعات</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?><script>document.querySelectorAll('.delete-form').forEach(f=>{f.addEventListener('submit',e=>{if(!confirm('حذف الموظف؟')) e.preventDefault();});});</script><?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.vertical', ['title' => 'تفاصيل موظف'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/employees/show.blade.php ENDPATH**/ ?>