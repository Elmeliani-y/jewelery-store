
<?php $__env->startSection('content'); ?>
<?php if(auth()->user() && auth()->user()->isAdmin()): ?>
<div class="container">
    <h2>إدارة الأجهزة</h2>
    <?php if(session('user_link')): ?>
        <div class="alert alert-info">
            رابط دخول مستخدم (بدون جهاز):
            <a href="<?php echo e(session('user_link')); ?>" target="_blank" id="user-link"><?php echo e(session('user_link')); ?></a>
            <button class="btn btn-sm btn-secondary ms-2" onclick="navigator.clipboard.writeText(document.getElementById('user-link').href)">نسخ الرابط</button>
        </div>
    <?php endif; ?>
    <?php if(session('device_link')): ?>
        <div class="alert alert-success">
            رابط الجهاز الجديد:
            <a href="<?php echo e(session('device_link')); ?>" target="_blank" id="device-link"><?php echo e(session('device_link')); ?></a>
            <button class="btn btn-sm btn-secondary ms-2" onclick="navigator.clipboard.writeText(document.getElementById('device-link').href)">نسخ الرابط</button>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('q3r8s1t6.u4v9w2x7.y5z1a8b3')); ?>" class="mb-3">
        <?php echo csrf_field(); ?>
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="اسم الجهاز" required>
            <button type="submit" class="btn btn-primary">توليد رابط دخول مستخدم (بدون جهاز)</button>
        </div>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="text-danger mt-2"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </form>
    <hr>
    <h4>الأجهزة المسجلة</h4>
    <table class="table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>تاريخ آخر دخول</th>
                <th>رمز الجهاز</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($device->name); ?></td>
                <td><?php echo e($device->last_login_at ? $device->last_login_at : '--'); ?></td>
                <td style="font-size: 0.8em;"><?php echo e($device->token); ?></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDeviceModal<?php echo e($device->id); ?>">حذف</button>
                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteDeviceModal<?php echo e($device->id); ?>" tabindex="-1" aria-labelledby="deleteDeviceLabel<?php echo e($device->id); ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteDeviceLabel<?php echo e($device->id); ?>">تأكيد حذف الجهاز</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    هل أنت متأكد أنك تريد حذف هذا الجهاز؟ لا يمكن التراجع عن هذا الإجراء.
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="<?php echo e(route('h4i8j3k7.l2m6n9o4.delete', $device->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php if(session('success')): ?>
    <script>
        window.onload = function() {
            var successModal = new bootstrap.Modal(document.getElementById('successDeleteModal'));
            successModal.show();
        }
    </script>
    <div class="modal fade" id="successDeleteModal" tabindex="-1" aria-labelledby="successDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successDeleteLabel">تم الحذف بنجاح</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo e(session('success')); ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">موافق</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="container"><div class="alert alert-danger mt-4">غير مصرح لك بعرض هذه الصفحة.</div></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'إدارة الأجهزة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/settings/devices.blade.php ENDPATH**/ ?>