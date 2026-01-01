@php
    $user = auth()->user();
@endphp
<!-- Right Sidebar Start -->
@if($user)
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <div class="logo-box text-center py-2">
                @php
                    $settings = \App\Models\Setting::all()->pluck('value', 'key');
                    $logoWebPath = !empty($settings['logo_path']) ? $settings['logo_path'] : null;
                    $logoFullPath = $logoWebPath ? public_path($logoWebPath) : null;
                    $showCustomLogo = $logoFullPath && file_exists($logoFullPath);
                @endphp
                <a href="{{ $user->isBranch() ? route('branch.daily-sales') : route('dashboard') }}" class="logo">
                    @if($showCustomLogo)
                        <img src="{{ asset($logoWebPath) }}" alt="Logo" style="height:64px; max-width:90%; border-radius:10px; display:block; margin:0 auto 8px;">
                    @else
                        <img src="/images/logo-light.png" alt="Logo" class="logo-img logo-light" style="height:48px; max-width:90%; border-radius:10px; display:block; margin:0 auto 8px;">
                        <img src="/images/logo-dark.png" alt="Logo" class="logo-img logo-dark" style="height:48px; max-width:90%; border-radius:10px; display:block; margin:0 auto 8px; display:none;">
                        <style>
                            body[data-bs-theme="dark"] .logo-light { display: none !important; }
                            body[data-bs-theme="dark"] .logo-dark { display: block !important; }
                            body:not([data-bs-theme="dark"]) .logo-light { display: block !important; }
                            body:not([data-bs-theme="dark"]) .logo-dark { display: none !important; }
                        </style>
                    @endif
                </a>
            </div>
            <ul id="side-menu">
                @if($user->isAdmin())
                    <li class="menu-title mt-2">الإدارة (أدمن فقط)</li>
                    <li>
                        <a href="#sidebarManagement" data-bs-toggle="collapse" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-cog-outline"></i></span>
                            <span> إعدادات النظام </span>
                        </a>
                        <div class="collapse show" id="sidebarManagement">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('settings.devices') }}" class="tp-link">إدارة الأجهزة</a>
                                </li>
                                <li>
                                    <a href="{{ route('users.index') }}" class="tp-link">المستخدمين</a>
                                </li>
                                <li>
                                    <a href="{{ route('settings.index') }}" class="tp-link">إعدادات النظام</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @elseif($user->isBranch())
                    <li class="menu-title mt-2">فرع</li>
                    <li>
                        <a href="{{ route('branch.daily-sales') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-calendar-today"></i></span>
                            <span> المبيعات اليومية </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.create') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-shopping-outline"></i></span>
                            <span> تسجيل مبيعة جديدة </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('branch.daily-expenses') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-calendar-month"></i></span>
                            <span> المصروفات اليومية </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expenses.create') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-wallet-outline"></i></span>
                            <span> تسجيل مصروف جديد </span>
                        </a>
                    </li>
                @elseif($user->isAccountant())
                    <li class="menu-title mt-2">محاسب</li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-home-outline"></i></span>
                            <span> الرئيسية </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.index') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-shopping-outline"></i></span>
                            <span> قائمة المبيعات </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expenses.index') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-wallet-outline"></i></span>
                            <span> قائمة المصروفات </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('branches.index') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-store"></i></span>
                            <span> الفروع </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-account-group"></i></span>
                            <span> الموظفين </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-tag-multiple"></i></span>
                            <span> الأصناف </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calibers.index') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-scale-balance"></i></span>
                            <span> العيارات والضرائب </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('settings.index') }}" class="tp-link">
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
                                    <a href="{{ route('reports.all') }}" class="tp-link">تقرير الكل</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.kasr') }}" class="tp-link">تقرير صافي الربح</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.speed') }}" class="tp-link">تقرير السرعة</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.accounts') }}" class="tp-link">تقرير الحسابات</a>
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
                                    @if (Route::has('reports.comparative'))
                                        <li>
                                            <a href="{{ route('reports.comparative') }}" class="tp-link">تقرير مقارن</a>
                                        </li>
                                    @endif
                                    @if (Route::has('reports.period_comparison'))
                                        <li>
                                            <a href="{{ route('reports.period_comparison') }}" class="tp-link">تقرير مقارن حسب فترة</a>
                                        </li>
                                    @endif
                                    @if (!Route::has('reports.comparative') && !Route::has('reports.period_comparison'))
                                        <li>
                                            <span class="tp-link text-muted">لا توجد تقارير متاحة</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                @endif



            </ul>
        </div>
        <!-- End Sidebar -->
        
        <div class="clearfix"></div>

    </div>
    </div>
</div>
@endif
<!-- Right Sidebar End -->