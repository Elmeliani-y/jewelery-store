<?php $__env->startSection('title','تفاصيل فرع'); ?>
<?php $__env->startSection('css'); ?><style>.stats-box{border:1px solid var(--bs-border-color);border-radius:12px;padding:1rem;}</style><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('x9y4z1a6.index')); ?>" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
            <h5 class="mb-0"><iconify-icon icon="solar:buildings-bold-duotone" class="fs-4 me-1"></iconify-icon> الفرع: <?php echo e($branch->name); ?></h5>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('x9y4z1a6.edit',$branch)); ?>" class="btn btn-primary btn-sm"><iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> تعديل</a>
            <form action="<?php echo e(route('x9y4z1a6.b2c7d5e8',$branch)); ?>" method="POST"><?php echo csrf_field(); ?> <button class="btn btn-<?php echo e($branch->is_active? 'warning':'success'); ?> btn-sm" type="submit"><iconify-icon icon="solar:<?php echo e($branch->is_active? 'eye-closed':'eye'); ?>-bold" class="me-1"></iconify-icon><?php echo e($branch->is_active? 'تعطيل':'تفعيل'); ?></button></form>
            <?php if(!$branch->employees_count && !$branch->sales_count): ?>
            <form action="<?php echo e(route('x9y4z1a6.destroy',$branch)); ?>" method="POST" class="delete-form"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?> <button class="btn btn-danger btn-sm" type="submit"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button></form>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $__env->make('layouts.partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">معلومات أساسية</h6>
                <p class="mb-1"><strong>الحالة:</strong> <span class="badge <?php echo e($branch->is_active? 'bg-success-subtle text-success':'bg-danger-subtle text-danger'); ?>"><?php echo e($branch->is_active? 'مفعل':'معطل'); ?></span></p>
                <?php if($branch->phone): ?><p class="mb-1"><strong>الهاتف:</strong> <?php echo e($branch->phone); ?></p><?php endif; ?>
                <?php if($branch->address): ?><p class="mb-1"><strong>العنوان:</strong> <?php echo e($branch->address); ?></p><?php endif; ?>
                <p class="text-muted small mt-2 mb-0">تم الإنشاء في <?php echo e($branch->created_at->format('Y-m-d')); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">إحصائيات</h6>
                <div class="d-flex justify-content-between mb-2"><span>عدد الموظفين:</span><strong><?php echo e($branch->employees_count); ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>عدد المبيعات:</span><strong><?php echo e($branch->sales_count); ?></strong></div>
                <div class="d-flex justify-content-between"><span>عدد المصروفات:</span><strong><?php echo e($branch->expenses_count); ?></strong></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">موظفون نشطون</h6>
                <?php $__empty_1 = true; $__currentLoopData = $branch->activeEmployees()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?php echo e($emp->name); ?></span>
                        <span class="text-muted"><?php echo e(number_format($emp->salary,2)); ?> ريال</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small mb-0">لا يوجد موظفون نشطون</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?><script>document.querySelectorAll('.delete-form').forEach(f=>{f.addEventListener('submit',e=>{if(!confirm('هل أنت متأكد من الحذف؟')) e.preventDefault();});});</script><?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.vertical', ['title' => 'تفاصيل فرع'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/branches/show.blade.php ENDPATH**/ ?>