<?php
    $user = auth()->user();
?>
<!-- Right Sidebar Start -->
<?php if($user): ?>
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <div class="logo-box text-center py-2">
                <?php
                    $settings = \App\Models\Setting::all()->pluck('value', 'key');
                    $logoWebPath = !empty($settings['logo_path']) ? $settings['logo_path'] : null;
                    $logoFullPath = $logoWebPath ? public_path($logoWebPath) : null;
                    $showCustomLogo = $logoFullPath && file_exists($logoFullPath);
                ?>
                <a href="<?php echo e($user->isBranch() ? route('r8s3t7u1.v4w9x2y5') : route('c5d9f2h7')); ?>" class="logo">
                    <?php if($showCustomLogo): ?>
                        <img src="<?php echo e(asset($logoWebPath)); ?>" alt="Logo" style="height:64px; max-width:90%; border-radius:10px; display:block; margin:0 auto 8px;">
                    <?php else: ?>
                        <img src="/images/logo-light.png" alt="Logo" class="logo-img logo-light" style="height:48px; max-width:90%; border-radius:10px; display:block; margin:0 auto 8px;">
                        <img src="/images/logo-dark.png" alt="Logo" class="logo-img logo-dark" style="height:48px; max-width:90%; border-radius:10px; display:block; margin:0 auto 8px; display:none;">
                        <style>
                            body[data-bs-theme="dark"] .logo-light { display: none !important; }
                            body[data-bs-theme="dark"] .logo-dark { display: block !important; }
                            body:not([data-bs-theme="dark"]) .logo-light { display: block !important; }
                            body:not([data-bs-theme="dark"]) .logo-dark { display: none !important; }
                        </style>
                    <?php endif; ?>
                </a>
            </div>
            <ul id="side-menu">
                <?php if($user->isAdmin()): ?>
                    <li class="menu-title mt-2">الإدارة (أدمن فقط)</li>
                    <li>
                        <a href="#sidebarManagement" data-bs-toggle="collapse" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-cog-outline"></i></span>
                            <span> إعدادات النظام </span>
                        </a>
                        <div class="collapse show" id="sidebarManagement">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="<?php echo e(route('h4i8j3k7.l2m6n9o4')); ?>" class="tp-link">إدارة الأجهزة</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('q8r2s6t0')); ?>" class="tp-link">عناوين IP المحظورة</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('d7e1f5g9.index')); ?>" class="tp-link">المستخدمين</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('h4i8j3k7')); ?>" class="tp-link">إعدادات النظام</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php elseif($user->isBranch()): ?>
                    <li class="menu-title mt-2">فرع</li>
                    <li>
                        <a href="<?php echo e(route('r8s3t7u1.v4w9x2y5')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-calendar-today"></i></span>
                            <span> المبيعات اليومية </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('t6u1v5w8.create')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-shopping-outline"></i></span>
                            <span> تسجيل مبيعة جديدة </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('r8s3t7u1.p4q9r5s2')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-calendar-month"></i></span>
                            <span> المصروفات اليومية </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('l7m2n6o1.create')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-wallet-outline"></i></span>
                            <span> تسجيل مصروف جديد </span>
                        </a>
                    </li>
                <?php elseif($user->isAccountant()): ?>
                    <li class="menu-title mt-2">محاسب</li>
                    <li>
                        <a href="<?php echo e(route('c5d9f2h7')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-home-outline"></i></span>
                            <span> الرئيسية </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('t6u1v5w8.index')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-shopping-outline"></i></span>
                            <span> قائمة المبيعات </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('l7m2n6o1.index')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-wallet-outline"></i></span>
                            <span> قائمة المصروفات </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('h1i5j9k3')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-backup-restore"></i></span>
                            <span> قائمة المرتجعات </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('x9y4z1a6.index')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-store"></i></span>
                            <span> الفروع </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('f3g8h1i4.index')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-account-group"></i></span>
                            <span> الموظفين </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('v5w9x4y1.index')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-tag-multiple"></i></span>
                            <span> الأصناف </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('n6o1p4q9.index')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-scale-balance"></i></span>
                            <span> العيارات والضرائب </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('h4i8j3k7')); ?>" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-cog-outline"></i></span>
                            <span> إعدادات النظام </span>
                        </a>
                    </li>
                    <li>
                        <a href="#sidebarReports" data-bs-toggle="collapse" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-chart-line"></i></span>
                            <span> جميع التقارير </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarReports">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="<?php echo e(route('t3u8v1w4.b1c5d8e3')); ?>" class="tp-link">تقرير الكل</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('t3u8v1w4.t6u2v8w5')); ?>" class="tp-link">تقرير صافي الربح</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('t3u8v1w4.f4g9h2i7')); ?>" class="tp-link">تقرير السرعة</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('t3u8v1w4.a3b7c1d5')); ?>" class="tp-link">تقرير الحسابات</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                        <li>
                            <a href="#sidebarMo9arant" data-bs-toggle="collapse" class="tp-link">
                                <span class="nav-icon"><i class="mdi mdi-compare"></i></span>
                                <span> المقارنات </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarMo9arant">
                                <ul class="nav-second-level">
                                    <?php if(Route::has('t3u8v1w4.l3m8n2o6')): ?>
                                        <li>
                                            <a href="<?php echo e(route('t3u8v1w4.l3m8n2o6')); ?>" class="tp-link">تقرير مقارن</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Route::has('t3u8v1w4.p4q9r1s7')): ?>
                                        <li>
                                            <a href="<?php echo e(route('t3u8v1w4.p4q9r1s7')); ?>" class="tp-link">تقرير مقارن حسب فترة</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(!Route::has('t3u8v1w4.l3m8n2o6') && !Route::has('t3u8v1w4.p4q9r1s7')): ?>
                                        <li>
                                            <span class="tp-link text-muted">لا توجد تقارير متاحة</span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                <?php endif; ?>



            </ul>
        </div>
        <!-- End Sidebar -->
        
        <div class="clearfix"></div>

    </div>
    </div>
</div>
<?php endif; ?>
<!-- Right Sidebar End --><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>