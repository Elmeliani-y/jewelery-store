<?php $__env->startSection('title'); ?> العيارات <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .caliber-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .caliber-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    [data-bs-theme="dark"] .caliber-card {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: #1a1d21;
    }
    
    [data-bs-theme="dark"] .caliber-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .tax-badge {
        font-size: 1.25rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .action-buttons .btn {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 2rem;
    }
    
    [data-bs-theme="dark"] .page-header {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-1">
                    <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-4 me-2"></iconify-icon>
                    إدارة العيارات
                </h3>
                <p class="mb-0 opacity-75">إدارة عيارات الذهب ونسب الضرائب الخاصة بكل عيار</p>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('n6o1p4q9.create')); ?>" class="btn btn-light">
                    <iconify-icon icon="solar:add-circle-bold" class="fs-5 me-1"></iconify-icon>
                    إضافة عيار جديد
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <iconify-icon icon="solar:check-circle-bold" class="fs-5 me-2"></iconify-icon>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <iconify-icon icon="solar:danger-circle-bold" class="fs-5 me-2"></iconify-icon>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Calibers Grid -->
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $calibers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caliber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card caliber-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1"><?php echo e($caliber->name); ?></h4>
                                <span class="status-badge <?php echo e($caliber->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?>">
                                    <?php echo e($caliber->is_active ? 'مفعل' : 'معطل'); ?>

                                </span>
                            </div>
                            <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-2 text-warning"></iconify-icon>
                        </div>

                        <div class="text-center my-4">
                            <div class="tax-badge bg-primary-subtle text-primary">
                                <?php echo e($caliber->tax_rate); ?>%
                            </div>
                            <small class="text-muted d-block mt-2">نسبة الضريبة</small>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">عدد المبيعات:</small>
                                <strong><?php echo e($caliber->products_sold_count); ?></strong>
                            </div>
                        </div>

                        <div class="action-buttons mt-3 d-flex gap-2">
                            <a href="<?php echo e(route('n6o1p4q9.edit', $caliber)); ?>" class="btn btn-sm btn-primary flex-fill">
                                <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                تعديل
                            </a>
                            
                            <form action="<?php echo e(route('n6o1p4q9.r2s8t3u7', $caliber)); ?>" method="POST" class="flex-fill">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-<?php echo e($caliber->is_active ? 'warning' : 'success'); ?> w-100">
                                    <iconify-icon icon="solar:<?php echo e($caliber->is_active ? 'eye-closed' : 'eye'); ?>-bold"></iconify-icon>
                                    <?php echo e($caliber->is_active ? 'تعطيل' : 'تفعيل'); ?>

                                </button>
                            </form>
                            
                            <?php if($caliber->products_sold_count == 0): ?>
                                <form action="<?php echo e(route('n6o1p4q9.destroy', $caliber)); ?>" method="POST" class="delete-form">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                        <h5 class="text-muted">لا توجد عيارات</h5>
                        <p class="text-muted mb-3">قم بإضافة عيار جديد للبدء</p>
                        <a href="<?php echo e(route('n6o1p4q9.create')); ?>" class="btn btn-primary">
                            <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon>
                            إضافة عيار
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    // Confirm delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('هل أنت متأكد من حذف هذا العيار؟')) {
                e.preventDefault();
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'إدارة العيارات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/calibers/index.blade.php ENDPATH**/ ?>