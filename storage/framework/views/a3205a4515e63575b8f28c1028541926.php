<?php $__env->startSection('css'); ?><style>.form-card{border-radius:14px;border:1px solid var(--bs-border-color);}</style><?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header mb-4 d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <a href="<?php echo e(route('x9y4z1a6.show',$branch)); ?>" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
        <h5 class="mb-0"><iconify-icon icon="solar:pen-bold-duotone" class="fs-4 me-1"></iconify-icon> تعديل الفرع: <?php echo e($branch->name); ?></h5>
    </div>

    <form action="<?php echo e(route('x9y4z1a6.update',$branch)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon> بيانات الفرع</h6>
                    <div class="mb-3">
                        <label class="form-label">اسم الفرع <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name',$branch->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('address',$branch->address)); ?>">
                        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('phone',$branch->phone)); ?>">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active',$branch->is_active)?'checked':''); ?>>
                        <label class="form-check-label" for="is_active">مفعل</label>
                    </div>
                </div></div>
            </div>
            <div class="col-lg-4">
                <div class="card form-card mb-4"><div class="card-body">
                    <h6 class="mb-3"><iconify-icon icon="solar:info-circle-bold" class="me-1"></iconify-icon> ملاحظات</h6>
                    <p class="text-muted small mb-0">تعطيل الفرع يجعله غير متاح للاختيار مستقبلاً دون حذف البيانات السابقة.</p>
                </div></div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" type="submit"><iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon> حفظ التعديلات</button>
                    <a href="<?php echo e(route('x9y4z1a6.show',$branch)); ?>" class="btn btn-light btn-lg"><iconify-icon icon="solar:close-circle-bold" class="me-1"></iconify-icon> إلغاء</a>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.vertical', ['title' => 'تعديل فرع'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/branches/edit.blade.php ENDPATH**/ ?>