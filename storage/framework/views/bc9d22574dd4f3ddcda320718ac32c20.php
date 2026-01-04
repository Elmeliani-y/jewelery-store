<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $__env->make('layouts.partials/title-meta', ['title' => $title], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('layouts.partials/head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body>


    <!-- Begin page -->
    <div class="account-page">
        <div class="container-fluid p-0">
            <div class="row align-items-center g-0 px-3 py-3 vh-100">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>

    <!-- Admin Only Error Modal -->
    <?php if(session('admin_only_error')): ?>
        <div class="modal fade show" id="adminOnlyErrorModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">تنبيه</h5>
                    </div>
                    <div class="modal-body text-center">
                        <span class="fw-bold"><?php echo e(session('admin_only_error')); ?></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="document.getElementById('adminOnlyErrorModal').style.display='none'; location.href='/'">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.body.classList.add('modal-open');
        </script>
    <?php endif; ?>

    <?php echo $__env->make('layouts.partials/vendor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/layouts/auth.blade.php ENDPATH**/ ?>