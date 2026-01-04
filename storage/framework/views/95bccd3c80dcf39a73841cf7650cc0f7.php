<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $__env->make('layouts.partials/title-meta', ['title' => $title ?? 'Dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('layouts.partials/head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        /* Flip sidebar to the right */
        .app-sidebar-menu { right: 0; left: auto; }
        .logo-box { right: 0; left: auto; }
        .content-page { margin-right: 260px !important; margin-left: 0 !important; }
        .topbar-custom { right: 260px !important; left: auto !important; }
        .footer { right: 260px !important; left: auto !important; }

        /* Active indicator on right side */
        #sidebar-menu .menuitem-active .tp-link.active::before { right: 0; left: auto; }

        /* When sidebar is hidden */
        body[data-sidebar="hidden"] .content-page { margin-right: 0 !important; }
        body[data-sidebar="hidden"] .topbar-custom { right: 0 !important; }
        body[data-sidebar="hidden"] .footer { right: 0 !important; }

        /* Mobile/overlay mode: avoid pushing content */
        .sidebar-enable .content-page { margin-right: 0 !important; }
    </style>
</head>

<body data-menu-color="dark" data-sidebar="default">

    <div id="app-layout">

        <?php echo $__env->make('layouts.partials/topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('layouts.partials/sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('layouts.partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="content-page">

            <div class="content">

                <?php echo $__env->yieldContent('content'); ?>

            </div>
            
            

        </div>

    </div>

    <?php echo $__env->make('layouts.partials/vendor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/layouts/vertical.blade.php ENDPATH**/ ?>