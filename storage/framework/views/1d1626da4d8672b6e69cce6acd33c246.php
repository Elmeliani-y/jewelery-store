<?php ($success = session('success')); ?>
<?php ($error = session('error')); ?>
<?php ($hasValidationErrors = $errors->any()); ?>

<?php if(auth()->check() && auth()->user()->isBranch()): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100">
        <?php if($success): ?>
        <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="mdi mdi-check-circle-outline me-1"></i> <?php echo e($success); ?>

                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
        <?php if($error): ?>
        <div class="toast align-items-center text-bg-danger border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> <?php echo e($error); ?>

                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
        <?php if(!$error && !$success && $hasValidationErrors): ?>
        <div class="toast align-items-center text-bg-danger border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="9000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="mdi mdi-alert-outline me-1"></i>
                    <strong>تحقق من المدخلات:</strong>
                    <ul class="mb-0 small ps-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($err); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($success || $error || $hasValidationErrors): ?>
    <script>
        (function(){
            const toastEls=[].slice.call(document.querySelectorAll('.toast'));
            toastEls.forEach(function(el){
                const t=new bootstrap.Toast(el); t.show();
            });
        })();
    </script>
    <?php endif; ?>
<?php endif; ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/layouts/partials/flash.blade.php ENDPATH**/ ?>