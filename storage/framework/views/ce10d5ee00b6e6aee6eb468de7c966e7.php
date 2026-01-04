<?php $__env->startSection('title'); ?> تعديل المبيعة <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .edit-header {background: linear-gradient(115deg,var(--bs-warning) 0%,#ffb547 100%); color:#212529; border-radius:.85rem; padding:1.3rem 1.6rem; margin-bottom:1.4rem; position:relative; overflow:hidden;}
    .edit-header:before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 88% 18%, rgba(255,255,255,.35), transparent 70%);}    
    .edit-header h4 {font-weight:600;}
    .badge-meta {font-weight:500;}
    .form-section {background:var(--bs-body-bg); border:1px solid var(--bs-border-color); border-radius:.75rem; padding:1.25rem 1.25rem; margin-bottom:1rem;}
    .form-section h6 {font-size:.8rem; letter-spacing:.07em; text-transform:uppercase; font-weight:600; opacity:.7; margin-bottom:.75rem;}
    .required:after {content:'*'; color:var(--bs-danger); margin-right:4px;}
    .payment-group {display:none;}
    .payment-visible {display:block !important;}
    .text-mono {font-family:ui-monospace,SFMono-Regular,Menlo,monospace; direction:ltr;}
    #cash_group { display: none; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function updatePaymentFields() {
    let sum = 0;
    document.querySelectorAll('input[name^="products"][name$="[amount]"]')
        .forEach(function(input) {
            let val = parseFloat(input.value);
            if (!isNaN(val)) sum += val;
        });
    let pm = document.getElementById('payment_method')?.value;
    let cashInput = document.getElementById('cash_amount_input');
    let netInput = document.getElementById('network_amount_input');
    let transferInput = document.getElementById('transfer_amount_input');
    if (pm === 'network') {
        if (netInput) netInput.value = sum.toFixed(2);
        if (cashInput) cashInput.value = '0';
        if (transferInput) transferInput.value = '0';
    } else if (pm === 'cash') {
        if (cashInput) cashInput.value = sum.toFixed(2);
        if (netInput) netInput.value = '0';
        if (transferInput) transferInput.value = '0';
    } else if (pm === 'transfer') {
        if (transferInput) transferInput.value = sum.toFixed(2);
        if (cashInput) cashInput.value = '0';
        if (netInput) netInput.value = '0';
    } else if (pm === 'mixed') {
        if (cashInput && netInput) {
            cashInput.value = (sum / 2).toFixed(2);
            netInput.value = (sum / 2).toFixed(2);
        }
        if (transferInput) transferInput.value = '0';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name^="products"][name$="[amount]"]')
        .forEach(function(input) {
            input.addEventListener('input', updatePaymentFields);
        });
    let pmSelect = document.getElementById('payment_method');
    if (pmSelect) {
        pmSelect.addEventListener('change', updatePaymentFields);
    }
    updatePaymentFields();
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Decode products JSON string to array for use throughout the view
    $productsArray = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
    if (!is_array($productsArray)) {
        $productsArray = [];
    }
?>
<div class="container-fluid">
    <div class="edit-header d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div>
            <h4 class="mb-1"><i class="mdi mdi-pencil-outline me-1"></i> تعديل المبيعة <span class="text-mono"><?php echo e($sale->invoice_number); ?></span></h4>
            <small class="opacity-75">قم بتحديث بيانات الفاتورة ثم احفظ التغييرات</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge badge-meta bg-primary-subtle text-primary"><i class="mdi mdi-storefront-outline me-1"></i><?php echo e($sale->branch->name); ?></span>
            <span class="badge badge-meta bg-info-subtle text-info"><i class="mdi mdi-account-outline me-1"></i><?php echo e($sale->employee->name); ?></span>
            <?php if(count($productsArray) > 0): ?>
            <span class="badge badge-meta bg-success-subtle text-success"><i class="mdi mdi-package-variant-closed me-1"></i><?php echo e(count($productsArray)); ?> منتج</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('t6u1v5w8.update', $sale)); ?>" novalidate id="sale-edit-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

    <?php if(count($productsArray) > 0): ?>
    <div class="card mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0"><i class="mdi mdi-package-variant-closed me-1"></i> المنتجات (<?php echo e(count($productsArray)); ?>)</h6>
            <span class="badge bg-warning-subtle text-warning">قابل للتعديل</span>
        </div>
        <div class="card-body">
            <?php $__currentLoopData = $productsArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row g-3 mb-3 pb-3 border-bottom">
                <div class="col-12">
                    <h6 class="text-muted">منتج <?php echo e($index + 1); ?></h6>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الفئة <span class="text-danger">*</span></label>
                    <select name="products[<?php echo e($index); ?>][category_id]" class="form-select" required>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e($product['category_id'] == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">العيار <span class="text-danger">*</span></label>
                    <select name="products[<?php echo e($index); ?>][caliber_id]" class="form-select" required>
                        <?php $__currentLoopData = $calibers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caliber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($caliber->id); ?>" <?php echo e($product['caliber_id'] == $caliber->id ? 'selected' : ''); ?>>
                                <?php echo e($caliber->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الوزن (جم) <span class="text-danger">*</span></label>
                    <input type="number" step="0.001" name="products[<?php echo e($index); ?>][weight]" 
                           value="<?php echo e(old('products.'.$index.'.weight', $product['weight'])); ?>" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">المبلغ (ريال) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="products[<?php echo e($index); ?>][amount]" 
                           value="<?php echo e($product['amount']); ?>" class="form-control" required>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="form-section">
                    <h6>البيانات الأساسية</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الفرع</label>
                            <select name="branch_id" class="form-select <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="branch_id">
                                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($branch->id); ?>" <?php echo e(old('branch_id', $sale->branch_id)==$branch->id?'selected':''); ?>><?php echo e($branch->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">الموظف</label>
                            <select name="employee_id" class="form-select <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="employee_id">
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>" data-is-snap="<?php echo e($employee->is_snap ? 1 : 0); ?>" <?php echo e(old('employee_id', $sale->employee_id)==$employee->id?'selected':''); ?>><?php echo e($employee->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" rows="3" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="إضافة ملاحظات .."><?php echo e(old('notes', $sale->notes)); ?></textarea>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input" type="checkbox" id="customer_received" 
                                       name="customer_received" value="1" 
                                       <?php echo e(old('customer_received', $sale->customer_received) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="customer_received">
                                    <strong>هل استلم العميل الفاتورة؟</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h6>طريقة الدفع والمبالغ</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required">طريقة الدفع</label>
                            <select name="payment_method" id="payment_method" class="form-select <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php ($pm = old('payment_method', $sale->payment_method)); ?>
                                <option value="cash" <?php echo e($pm=='cash'?'selected':''); ?>>نقداً</option>
                                <option value="network" <?php echo e($pm=='network'?'selected':''); ?>>شبكة</option>
                                <option value="mixed" <?php echo e($pm=='mixed'?'selected':''); ?>>مختلط (نقدي + شبكة)</option>
                                <option value="transfer" <?php echo e($pm=='transfer'?'selected':''); ?> id="transfer-option">تحويل (Snap)</option>
                            </select>
                            <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <!-- Current Payment Summary -->
                        <div class="col-12" id="payment_summary">
                            <div class="alert alert-primary bg-primary-subtle border-0" role="alert">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        <span class="fw-semibold">المبلغ الإجمالي للفاتورة:</span>
                                        <span class="text-primary fw-bold fs-5 ms-2"><?php echo e(number_format($sale->total_amount, 2)); ?></span> ريال
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">المبلغ المدفوع حالياً:</span>
                                    <div class="text-end">
                                        <?php ($pm = old('payment_method', $sale->payment_method)); ?>
                                        <?php if($pm === 'mixed'): ?>
                                            <div><small class="text-muted">نقدي:</small> <span class="text-success fw-bold"><?php echo e(number_format($sale->cash_amount, 2)); ?></span> ريال</div>
                                            <div><small class="text-muted">شبكة:</small> <span class="text-info fw-bold"><?php echo e(number_format($sale->network_amount, 2)); ?></span> ريال</div>
                                        <?php elseif($pm === 'cash'): ?>
                                            <span class="text-success fw-bold"><?php echo e(number_format($sale->cash_amount, 2)); ?></span> ريال <small class="text-muted">(نقدي)</small>
                                        <?php elseif($pm === 'transfer'): ?>
                                            <span class="text-primary fw-bold"><?php echo e(number_format($sale->transfer_amount, 2)); ?></span> ريال <small class="text-muted">(تحويل)</small>
                                        <?php elseif($pm === 'network'): ?>
                                            <span class="text-info fw-bold"><?php echo e(number_format($sale->network_amount, 2)); ?></span> ريال <small class="text-muted">(شبكة)</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 payment-group" id="network_group">
                            <label class="form-label">مبلغ الشبكة <span class="text-danger payment-required">*</span></label>
                            <input type="number" step="0.01" name="network_amount" id="network_amount_input" value="<?php echo e(old('network_amount', array_sum(array_column($productsArray, 'amount')) ?: ($sale->network_amount ?? ''))); ?>" class="form-control <?php $__errorArgs = ['network_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="0.00">
                            <?php $__errorArgs = ['network_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">
                                <i class="mdi mdi-calculator-variant me-1"></i>سيتم حساب المبلغ النقدي تلقائياً
                            </small>
                        </div>
                        <div class="col-md-6 payment-group" id="transfer_group" style="display:none;">
                            <label class="form-label">مبلغ التحويل <span class="text-danger payment-required">*</span></label>
                            <input type="number" step="0.01" name="transfer_amount" id="transfer_amount_input" value="<?php echo e(old('transfer_amount', array_sum(array_column($productsArray, 'amount')) ?: ($sale->transfer_amount ?? ''))); ?>" class="form-control <?php $__errorArgs = ['transfer_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="0.00">
                            <?php $__errorArgs = ['transfer_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">
                                <i class="mdi mdi-bank-transfer me-1"></i>أدخل مبلغ التحويل إذا كان الدفع عبر سناب
                            </small>
                        </div>
                        <div class="col-md-6 payment-group" id="cash_group" style="display:none;">
                            <label class="form-label">المبلغ النقدي</label>
                            <input type="number" step="0.01" name="cash_amount" id="cash_amount_input" value="<?php echo e(array_sum(array_column($productsArray, 'amount')) ?: ($sale->cash_amount ?? '')); ?>" class="form-control" placeholder="0.00">
                        </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        function toggleCashInput() {
                            var paymentMethod = document.getElementById('payment_method');
                            var cashGroup = document.getElementById('cash_group');
                            if (paymentMethod && cashGroup) {
                                if (['mixed', 'network', 'transfer'].includes(paymentMethod.value)) {
                                    cashGroup.style.display = '';
                                } else {
                                    cashGroup.style.display = 'none';
                                }
                            }
                        }
                        function autofillCashNetworkTransfer() {
                            var cashInput = document.getElementById('cash_amount_input');
                            var networkInput = document.getElementById('network_amount_input');
                            var transferInput = document.getElementById('transfer_amount_input');
                            var productInputs = document.querySelectorAll('input[name^="products"][name$="[amount]"]');
                            var sum = 0;
                            productInputs.forEach(function(input) {
                                var val = parseFloat(input.value);
                                if (!isNaN(val)) sum += val;
                            });
                            if (cashInput) cashInput.value = sum.toFixed(2);
                            if (networkInput) networkInput.value = sum.toFixed(2);
                            if (transferInput) transferInput.value = sum.toFixed(2);
                        }
                        var paymentMethod = document.getElementById('payment_method');
                        if (paymentMethod) {
                            paymentMethod.addEventListener('change', function() {
                                toggleCashInput();
                                autofillCash();
                            });
                            toggleCashInput();
                        }
                        document.querySelectorAll('input[name^="products"][name$="[amount]"]')
                            .forEach(function(input) {
                                input.addEventListener('input', autofillCashNetworkTransfer);
                            });
                        autofillCashNetworkTransfer();
                    });
                    </script>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="form-section">
                    <h6>ملخص الفاتورة</h6>
                    <div class="mb-3">
                        <label class="form-label">الوزن الكلي</label>
                        <div class="form-control bg-light text-mono" readonly><?php echo e(number_format($sale->weight,2)); ?> جرام</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ الإجمالي</label>
                        <div class="form-control bg-light text-mono fw-bold text-success" id="live-total-amount" readonly><?php echo e(number_format($sale->total_amount,2)); ?> ريال</div>
                    </div>
                    <!-- total-mismatch-warning removed -->
                    <div class="mb-3">
                        <label class="form-label">الضريبة</label>
                        <div class="form-control bg-light text-mono" readonly><?php echo e(number_format($sale->tax_amount,2)); ?> ريال</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصافي</label>
                        <div class="form-control bg-light text-mono" readonly><?php echo e(number_format($sale->net_amount,2)); ?> ريال</div>
                    </div>
                    <div class="alert alert-info mb-3" role="alert">
                        <small><i class="mdi mdi-information-outline me-1"></i>يمكنك تعديل المبالغ المدفوعة حسب طريقة الدفع</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-warning"><i class="mdi mdi-content-save-outline me-1"></i> حفظ التعديلات</button>
                        <a href="<?php echo e(route('t6u1v5w8.show', $sale)); ?>" class="btn btn-secondary"><i class="mdi mdi-arrow-left me-1"></i> إلغاء</a>
                    </div>
                </div>
                <div class="form-section">
                    <h6>بيانات إضافية</h6>
                    <p class="mb-1 small text-muted">تاريخ الإنشاء: <?php echo e($sale->created_at->format('Y-m-d H:i')); ?></p>
                    <p class="mb-0 small text-muted">آخر تحديث: <?php echo e($sale->updated_at->format('Y-m-d H:i')); ?></p>
                </div>
            </div>
        </div>
    </form>

    <?php if(!$sale->is_returned): ?>
    <form action="<?php echo e(route('t6u1v5w8.x2y7z3a9', $sale)); ?>" method="POST" class="d-inline">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-danger mt-3" onclick="return confirm('هل أنت متأكد من استرجاع هذه الفاتورة؟');">
            <i class="mdi mdi-backup-restore me-1"></i> تعيين كمرتجع
        </button>
    </form>
    <?php else: ?>
    <div class="alert alert-danger mt-3"><i class="mdi mdi-backup-restore me-1"></i> هذه الفاتورة مرتجع</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
function getProductsTotal() {
    let total = 0;
    document.querySelectorAll('input[name^="products"][name$="[amount]"]').forEach(function(input) {
        let val = parseFloat(input.value);
        if (!isNaN(val)) total += val;
    });
    return total;
}

function getOriginalTotal() {
    let originalTotalInput = document.querySelector('input[name="total_amount"]');
    let originalTotal = originalTotalInput ? parseFloat(originalTotalInput.value) : null;
    if (originalTotal === null || isNaN(originalTotal)) {
        originalTotal = parseFloat(<?php echo e($sale->total_amount ?? 0); ?>);
    }
    return originalTotal;
}

function updateLiveTotalAmount() {
    let total = getProductsTotal();
    document.getElementById('live-total-amount').innerText = total.toFixed(2) + ' ريال';
    let originalTotal = getOriginalTotal();
    // let warning = document.getElementById('total-mismatch-warning');

            // Show transfer input only if payment method is 'transfer'
            function toggleTransferInput() {
                var paymentMethod = document.getElementById('payment_method');
                var transferGroup = document.getElementById('transfer_group');
                if (paymentMethod && transferGroup) {
                    if (paymentMethod.value === 'transfer') {
                        transferGroup.style.display = '';
                    } else {
                        transferGroup.style.display = 'none';
                    }
                }
            }
            var paymentMethod = document.getElementById('payment_method');
            if (paymentMethod) {
                paymentMethod.addEventListener('change', toggleTransferInput);
                toggleTransferInput();
            }
    if (originalTotal !== null && Math.abs(total - originalTotal) > 0.01) {
        warning.style.display = '';
    } else {
        warning.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name^="products"][name$="[amount]"]').forEach(function(input) {
        input.addEventListener('input', updateLiveTotalAmount);
    });
    let originalTotalInput = document.querySelector('input[name="total_amount"]');
    if (originalTotalInput) {
        originalTotalInput.addEventListener('input', updateLiveTotalAmount);
    }
    updateLiveTotalAmount();

    // Prevent form submit if mismatch (always, not just button click)
    var form = document.getElementById('sale-edit-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let total = getProductsTotal();
            let originalTotal = getOriginalTotal();
            if (originalTotal !== null && total !== originalTotal) {
                e.preventDefault();
                // Optionally, scroll to the warning
                // let warning = document.getElementById('total-mismatch-warning');
                if (warning) {
                    warning.style.display = '';
                    warning.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script-bottom'); ?>
<script>
    const totalAmount = <?php echo e($sale->total_amount); ?>;
    let isAutoCalculating = false; // Flag to prevent infinite loops
    
    function togglePaymentGroups(){
        const pm = document.getElementById('payment_method').value;
        const cash = document.getElementById('cash_group');
        const net = document.getElementById('network_group');
        const transfer = document.getElementById('transfer_group');
        const cashInput = document.getElementById('cash_amount_input');
        const netInput = document.getElementById('network_amount_input');
        const transferInput = document.getElementById('transfer_amount_input');

        cash.classList.remove('payment-visible');
        net.classList.remove('payment-visible');
        if(transfer) transfer.classList.remove('payment-visible');

        // Reset required attributes
        cashInput.removeAttribute('required');
        netInput.removeAttribute('required');
        if(transferInput) transferInput.removeAttribute('required');

        if(pm === 'cash'){ 
            cash.classList.add('payment-visible');
            cashInput.setAttribute('required', 'required');
            netInput.value = '0';
            if(transferInput) transferInput.value = '0';
        }
        else if(pm === 'network'){ 
            net.classList.add('payment-visible');
            netInput.setAttribute('required', 'required');
            cashInput.value = '0';
            if(transferInput) transferInput.value = '0';
        }
        else if(pm === 'mixed'){ 
            cash.classList.add('payment-visible');
            net.classList.add('payment-visible');
            cashInput.setAttribute('required', 'required');
            netInput.setAttribute('required', 'required');
            if(transferInput) transferInput.value = '0';
        }
        else if(pm === 'transfer'){
            if(transfer) transfer.classList.add('payment-visible');
            if(transferInput) transferInput.setAttribute('required', 'required');
            cashInput.value = '0';
            netInput.value = '0';
        }
    }

    document.getElementById('payment_method').addEventListener('change', togglePaymentGroups);
    // On page load
    togglePaymentGroups();

    document.addEventListener('DOMContentLoaded', function() {
        function updatePaymentSummary() {
            var paymentMethod = document.getElementById('payment_method');
            var summaryDiv = document.getElementById('payment_summary');
            if (!paymentMethod || !summaryDiv) return;
            var cash = parseFloat(document.getElementById('cash_amount_input')?.value || 0).toFixed(2);
            var network = parseFloat(document.getElementById('network_amount_input')?.value || 0).toFixed(2);
            var transfer = parseFloat(document.getElementById('transfer_amount_input')?.value || 0).toFixed(2);
            var html = '';
            if (paymentMethod.value === 'mixed') {
                html += '<div><small class="text-muted">نقدي:</small> <span class="text-success fw-bold">' + cash + '</span> ريال</div>';
                html += '<div><small class="text-muted">شبكة:</small> <span class="text-info fw-bold">' + network + '</span> ريال</div>';
            } else if (paymentMethod.value === 'cash') {
                html += '<span class="text-success fw-bold">' + cash + '</span> ريال <small class="text-muted">(نقدي)</small>';
            } else if (paymentMethod.value === 'transfer') {
                html += '<span class="text-primary fw-bold">' + transfer + '</span> ريال <small class="text-muted">(تحويل)</small>';
            } else if (paymentMethod.value === 'network') {
                html += '<span class="text-info fw-bold">' + network + '</span> ريال <small class="text-muted">(شبكة)</small>';
            }
            var target = summaryDiv.querySelector('.text-end');
            if (target) target.innerHTML = html;
        }
        var paymentMethod = document.getElementById('payment_method');
        if (paymentMethod) {
            paymentMethod.addEventListener('change', updatePaymentSummary);
        }
        // Also update on input changes
        ['cash_amount_input','network_amount_input','transfer_amount_input'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', updatePaymentSummary);
        });
        updatePaymentSummary();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/sales/edit.blade.php ENDPATH**/ ?>