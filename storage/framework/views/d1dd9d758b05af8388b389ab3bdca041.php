

<?php $__env->startSection('css'); ?>
<style>
    .blocked-ip-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        transition: all 0.3s ease;
        background: var(--bs-card-bg);
    }
    .blocked-ip-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .page-header {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        padding: 1.5rem;
        border-radius: 14px;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    [data-bs-theme="dark"] .page-header {
        background: linear-gradient(135deg, #c82333, #e8590c);
    }
    .ip-badge {
        background: var(--bs-danger-bg-subtle);
        color: var(--bs-danger);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-family: monospace;
        font-size: 1rem;
        font-weight: 600;
    }
    .stats-box {
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        background: var(--bs-body-bg);
    }
    .stats-box h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: var(--bs-danger);
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">
                <i class="ri-shield-cross-line me-2"></i>
                إدارة عناوين IP المحظورة
            </h4>
            <p class="mb-0 opacity-75">عرض وإدارة عناوين IP المحظورة بسبب محاولات تسجيل دخول فاشلة</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-box">
                <h3><?php echo e($blockedIps->total()); ?></h3>
                <p class="text-muted mb-0">إجمالي السجلات</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box">
                <h3><?php echo e($blockedIps->where('blocked_at', '!=', null)->count()); ?></h3>
                <p class="text-muted mb-0">محظور حالياً</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box">
                <form action="<?php echo e(route('q8r2s6t0.clear')); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line me-1"></i>
                        حذف جميع السجلات
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Blocked IPs Table -->
    <div class="card blocked-ip-card">
        <div class="card-body">
            <?php if($blockedIps->isEmpty()): ?>
                <div class="text-center py-5">
                    <i class="ri-shield-check-line" style="font-size: 4rem; color: var(--bs-success);"></i>
                    <h5 class="mt-3">لا توجد عناوين IP محظورة</h5>
                    <p class="text-muted">النظام آمن حالياً</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>عنوان IP</th>
                                <th>محاولات فاشلة</th>
                                <th>آخر محاولة</th>
                                <th>تاريخ الحظر</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $blockedIps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="ip-badge"><?php echo e($ip->ip_address); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo e($ip->failed_attempts); ?></span>
                                    </td>
                                    <td>
                                        <?php if($ip->last_attempt_at): ?>
                                            <small class="text-muted">
                                                <?php echo e($ip->last_attempt_at->diffForHumans()); ?>

                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($ip->blocked_at): ?>
                                            <small class="text-muted">
                                                <?php echo e($ip->blocked_at->format('Y-m-d H:i')); ?>

                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($ip->blocked_at): ?>
                                            <span class="badge bg-danger">محظور</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">نشط</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($ip->blocked_at): ?>
                                            <form action="<?php echo e(route('q8r2s6t0.unblock')); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="ip_address" value="<?php echo e($ip->ip_address); ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="إلغاء الحظر">
                                                    <i class="ri-lock-unlock-line"></i>
                                                    إلغاء الحظر
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($blockedIps->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'إدارة عناوين IP المحظورة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/blocked-ips/index.blade.php ENDPATH**/ ?>