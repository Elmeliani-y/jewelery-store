<?php $__env->startSection('title'); ?>
    تسجيل مصروف جديد
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php echo $__env->make('components.form-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تسجيل مصروف جديد</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('c5d9f2h7')); ?>">الرئيسية</a></li>
                        
                        <?php if(!auth()->user() || !auth()->user()->isBranch()): ?>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('l7m2n6o1.index')); ?>">المصروفات</a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">تسجيل جديد</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <iconify-icon icon="solar:danger-circle-bold" class="fs-5 me-2"></iconify-icon>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form id="expense-create-form" action="<?php echo e(route('l7m2n6o1.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Main Information -->
                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                        معلومات المصروف
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">
                                الفرع <span class="text-danger">*</span>
                            </label>
                            <select name="branch_id" 
                                    id="branch_id" 
                                    class="form-select <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    <?php if(isset($selectedBranchId)): ?> disabled <?php endif; ?>
                                    required>
                                <option value="">اختر الفرع</option>
                                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($branch->id); ?>" 
                                            <?php if(old('branch_id', $selectedBranchId ?? null) == $branch->id): ?> selected <?php endif; ?>>
                                        <?php echo e($branch->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if(isset($selectedBranchId)): ?>
                                <input type="hidden" name="branch_id" value="<?php echo e($selectedBranchId); ?>">
                            <?php endif; ?>
                            <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expense_type_id" class="form-label">
                                نوع المصروف <span class="text-danger">*</span>
                            </label>
                            <select name="expense_type_id" 
                                    id="expense_type_id" 
                                    class="form-select <?php $__errorArgs = ['expense_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <option value="">اختر نوع المصروف</option>
                                <?php $__currentLoopData = $expenseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" <?php if(old('expense_type_id') == $type->id): ?> selected <?php endif; ?>>
                                        <?php echo e($type->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['expense_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                المبلغ (ريال) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="amount" 
                                   id="amount" 
                                   class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('amount')); ?>"
                                   step="0.01"
                                   min="0.01"
                                   placeholder="0.00"
                                   inputmode="decimal"
                                   required>
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expense_date" class="form-label">
                                تاريخ المصروف <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="expense_date" 
                                   id="expense_date" 
                                   class="form-control <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('expense_date', date('Y-m-d'))); ?>"
                                   required>
                            <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">
                                الوصف <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="description" 
                                   id="description" 
                                   class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('description')); ?>"
                                   placeholder="وصف المصروف"
                                   required>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      rows="3"
                                      placeholder="أي ملاحظات إضافية"><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Action Buttons -->
                <div class="form-section">
                    <h5 class="section-header">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                        الإجراءات
                    </h5>

                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <iconify-icon icon="solar:check-circle-bold" class="fs-5 me-2"></iconify-icon>
                            حفظ المصروف
                        </button>

                        <?php if(!auth()->user() || !auth()->user()->isBranch()): ?>
                            <a href="<?php echo e(route('l7m2n6o1.index')); ?>" class="btn btn-light btn-lg">
                                <iconify-icon icon="solar:close-circle-bold" class="fs-5 me-2"></iconify-icon>
                                إلغاء
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-3">
                    <iconify-icon icon="solar:info-circle-bold" class="fs-5 me-2"></iconify-icon>
                    <strong>تنبيه:</strong> تأكد من إدخال جميع البيانات بدقة. سيتم تسجيل المصروف مباشرة في النظام.
                </div>
            </div>
        </div>
    </form>

    <!-- Success Modal -->
    <div class="modal fade" id="expenseSuccessModal" tabindex="-1" aria-labelledby="expenseSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="expenseSuccessModalLabel">
                        <iconify-icon icon="solar:check-circle-bold" class="text-success fs-4 me-2"></iconify-icon>
                        تم الحفظ بنجاح
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    تم تسجيل المصروف بنجاح.<br>
                    رقم المصروف: <strong id="expenseSuccessId">—</strong><br>
                    يمكنك إضافة مصروف آخر أو إغلاق النافذة.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="addAnotherExpenseBtn">إضافة مصروف آخر</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert (dynamic) -->
    <div class="alert alert-danger d-none mt-3" id="expenseErrorAlert" role="alert"></div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    (function() {
        const form = document.getElementById('expense-create-form');
        const errorAlert = document.getElementById('expenseErrorAlert');
        const addAnotherBtn = document.getElementById('addAnotherExpenseBtn');

        function clearErrors() {
            errorAlert.classList.add('d-none');
            errorAlert.textContent = '';
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }

        function showFieldErrors(errors) {
            clearErrors();
            let hasFieldErrors = false;
            Object.keys(errors || {}).forEach(name => {
                const field = form.querySelector(`[name="${name}"]`);
                if (field) {
                    field.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = Array.isArray(errors[name]) ? errors[name][0] : errors[name];
                    field.parentElement.appendChild(feedback);
                    hasFieldErrors = true;
                }
            });
            if (!hasFieldErrors && errors) {
                errorAlert.textContent = 'حدثت أخطاء أثناء الإرسال.';
                errorAlert.classList.remove('d-none');
            }
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const url = form.getAttribute('action');
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const formData = new FormData(form);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.status === 422) {
                    const data = await response.json();
                    showFieldErrors(data.errors || {});
                    return;
                }

                const data = await response.json();

                if (data && data.success) {
                    // Clear form fields after successful save
                    form.reset();
                    // Restore default date to today
                    const dateInput = document.getElementById('expense_date');
                    if (dateInput) {
                        const today = new Date();
                        const yyyy = today.getFullYear();
                        const mm = String(today.getMonth() + 1).padStart(2, '0');
                        const dd = String(today.getDate()).padStart(2, '0');
                        dateInput.value = `${yyyy}-${mm}-${dd}`;
                    }
                    // Restore default branch if needed
                    const branchInput = document.getElementById('branch_id');
                    if (branchInput && branchInput.hasAttribute('disabled')) {
                        // If branch is fixed, set it back to selectedBranchId
                        const hiddenBranch = form.querySelector('input[name="branch_id"][type="hidden"]');
                        if (hiddenBranch) {
                            branchInput.value = hiddenBranch.value;
                        }
                    }
                    // Show success modal without redirect
                    const modalEl = document.getElementById('expenseSuccessModal');
                    const idEl = document.getElementById('expenseSuccessId');
                    if (idEl && data.data?.id) {
                        idEl.textContent = data.data.id;
                    }
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                } else {
                    errorAlert.textContent = (data && data.message) ? data.message : 'حدث خطأ غير متوقع.';
                    errorAlert.classList.remove('d-none');
                }
            } catch (err) {
                errorAlert.textContent = 'تعذر الاتصال بالخادم. حاول مرة أخرى.';
                errorAlert.classList.remove('d-none');
            }
        });

        addAnotherBtn?.addEventListener('click', function() {
            // Reset form for another entry
            form.reset();
            clearErrors();
            const modalEl = document.getElementById('expenseSuccessModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal?.hide();
            // set default date to today again
            const dateInput = document.getElementById('expense_date');
            if (dateInput) {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                dateInput.value = `${yyyy}-${mm}-${dd}`;
            }
        });


    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'تسجيل مصروف جديد'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/expenses/create.blade.php ENDPATH**/ ?>