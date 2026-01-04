<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                
                <!-- User Dropdown moved to left side -->
                <li class="dropdown notification-list topbar-dropdown ms-2">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <img src="/images/users/avatar-1.png" alt="user-image" class="rounded-circle" />
                        <span class="ms-2 d-none d-md-inline-block"><?php echo e(auth()->user()->username); ?></span>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">مرحباً <?php echo e(auth()->user()->username); ?>!</h6>
                            <?php if(auth()->user()->branch): ?>
                                <small class="text-muted"><?php echo e(auth()->user()->branch->name); ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- item-->
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item notify-item rounded-2">
                                <iconify-icon icon="solar:login-3-outline" class="fs-18 align-middle"></iconify-icon>
                                <span>تسجيل خروج</span>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                <!-- Light/Dark Mode Button Themes -->
                <li>
                    <button type="button" class="btn nav-link" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-outline" class="fs-24 align-middle dark-mode"></iconify-icon>
                        <iconify-icon icon="solar:sun-2-outline" class="fs-24 align-middle light-mode"></iconify-icon>
                    </button>
                </li>

                
            </ul>
        </div>
    </div>
</div>
<!-- end Topbar --><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/layouts/partials/topbar.blade.php ENDPATH**/ ?>