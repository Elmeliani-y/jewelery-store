    
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.edit-caliber'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .form-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    [data-bs-theme="dark"] .form-card {
        background-color: #1a1d21;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #344054;
    }
    
    [data-bs-theme="dark"] .form-label {
        color: #d1d5db;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #d0d5dd;
        padding: 0.625rem 0.875rem;
    }
    
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e5e7eb;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.5rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 2rem;
    }
    
    [data-bs-theme="dark"] .page-header {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    }
    
    .tax-preview {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        text-align: center;
    }
    
    [data-bs-theme="dark"] .tax-preview {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    }
    
    .tax-preview-value {
        font-size: 3rem;
        font-weight: 700;
        margin: 1rem 0;
    }
    
    .info-alert {
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex align-items-center">
            <a href="<?php echo e(route('n6o1p4q9.index')); ?>" class="btn btn-light btn-sm me-3">
                <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
            </a>
            <div>
                <h4 class="mb-0">
                    <iconify-icon icon="solar:pen-bold-duotone" class="fs-4 me-2"></iconify-icon>
                    تعديل العيار: <?php echo e($caliber->name); ?>

                </h4>
            </div>
        </div>
    </div>

    <?php if($caliber->products_sold_count > 0): ?>
        <div class="alert alert-info info-alert mb-4">
            <iconify-icon icon="solar:info-circle-bold" class="fs-5 me-2"></iconify-icon>
            <strong>تنبيه:</strong> هذا العيار مستخدم في <?php echo e($caliber->products_sold_count); ?> عملية بيع. تغيير نسبة الضريبة لن يؤثر على المبيعات السابقة.
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('n6o1p4q9.update', $caliber)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card form-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="me-2"></iconify-icon>
                            معلومات العيار
                        </h5>

                        <div class="mb-4">
                            <label for="name" class="form-label">
                                اسم العيار <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name', $caliber->name)); ?>"
                                   placeholder="مثال: عيار 24"
                                   required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">أدخل اسم العيار (مثال: عيار 24، عيار 21، عيار 18)</small>
                        </div>

                        <div class="mb-4">
                            <label for="tax_rate" class="form-label">
                                نسبة الضريبة (%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['tax_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="tax_rate" 
                                   name="tax_rate" 
                                   value="<?php echo e(old('tax_rate', $caliber->tax_rate)); ?>"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   placeholder="0.00"
                                   required>
                            <?php $__errorArgs = ['tax_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">أدخل نسبة الضريبة من 0 إلى 100</small>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       <?php echo e(old('is_active', $caliber->is_active) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_active">
                                    مفعل (متاح للاستخدام)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card form-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">
                            <iconify-icon icon="solar:calculator-bold-duotone" class="me-2"></iconify-icon>
                            معاينة الضريبة
                        </h5>

                        <div class="tax-preview">
                            <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-1 opacity-75"></iconify-icon>
                            <div class="tax-preview-value">
                                <span id="tax_display"><?php echo e($caliber->tax_rate); ?></span>%
                            </div>
                            <p class="mb-0 opacity-75">نسبة الضريبة</p>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded" style="border-radius: 8px;">
                            <small class="text-muted d-block mb-2">مثال على الحساب:</small>
                            <div class="d-flex justify-content-between mb-1">
                                <span>المبلغ:</span>
                                <strong>10,000 ريال</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>الضريبة:</span>
                                <strong class="text-danger" id="tax_example"><?php echo e(number_format(($caliber->tax_rate * 10000) / 100, 2)); ?> ريال</strong>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1">
                                <span>الصافي:</span>
                                <strong class="text-success" id="net_example"><?php echo e(number_format(10000 - (($caliber->tax_rate * 10000) / 100), 2)); ?> ريال</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <iconify-icon icon="solar:check-circle-bold" class="me-2"></iconify-icon>
                        حفظ التعديلات
                    </button>
                    <a href="<?php echo e(route('n6o1p4q9.index')); ?>" class="btn btn-light btn-lg">
                        <iconify-icon icon="solar:close-circle-bold" class="me-2"></iconify-icon>
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxRateInput = document.getElementById('tax_rate');
    const taxDisplay = document.getElementById('tax_display');
    const taxExample = document.getElementById('tax_example');
    const netExample = document.getElementById('net_example');

    function updateTaxPreview() {
        const taxRate = parseFloat(taxRateInput.value) || 0;
        const amount = 10000;
        const tax = (amount * taxRate) / 100;
        const net = amount - tax;

        taxDisplay.textContent = taxRate.toFixed(2);
        taxExample.textContent = tax.toFixed(2) + ' ريال';
        netExample.textContent = net.toFixed(2) + ' ريال';
    }

    taxRateInput.addEventListener('input', updateTaxPreview);
    updateTaxPreview();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'لوحة التحكم'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/calibers/edit.blade.php ENDPATH**/ ?>