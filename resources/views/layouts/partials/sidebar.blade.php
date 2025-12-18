<!-- Right Sidebar Start -->
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
                <a href="{{ auth()->user()->isBranch() ? route('branch.daily-sales') : route('dashboard') }}" class="logo">
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

                @if(!auth()->user()->isBranch())
                    <li class="menu-title">لوحة التحكم</li>

                    <li>
                        <a href="{{ route('dashboard') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-home-outline"></i></span>
                            <span> الرئيسية </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('branches.sales-summary') }}" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-store"></i></span>
                            <span> إجمالي مبيعات الفروع </span>
                        </a>
                    </li>
                @endif

                <li class="menu-title mt-2">إدارة المبيعات</li>
                @if(auth()->user()->isBranch())
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
                @else
                    <li>
                        <a href="#sidebarSales" data-bs-toggle="collapse" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-shopping-outline"></i></span>
                            <span> المبيعات </span>
                        </a>
                        <div class="collapse" id="sidebarSales">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('sales.index') }}" class="tp-link">قائمة المبيعات</a>
                                </li>
                                <li>
                                    <a href="{{ route('sales.returns') }}" class="tp-link">قائمة المرتجعات</a>
                                </li>
                                <li>
                                    <a href="{{ route('sales.create') }}" class="tp-link">تسجيل مبيعة جديدة</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(auth()->user()->isBranch())
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
                @else
                    <li>
                        <a href="#sidebarExpenses" data-bs-toggle="collapse" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-wallet-outline"></i></span>
                            <span> المصروفات </span>
                        </a>
                        <div class="collapse" id="sidebarExpenses">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('expenses.index') }}" class="tp-link">قائمة المصروفات</a>
                                </li>
                                <li>
                                    <a href="{{ route('expenses.create') }}" class="tp-link">تسجيل مصروف جديد</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(!auth()->user()->isBranch())

                    <li class="menu-title mt-2">التقارير</li>
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
                                    <a href="{{ route('reports.kasr') }}" class="tp-link">تقرير الكسر (صافي الربح)</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.comparative') }}" class="tp-link">تقرير المقارن</a>
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

                    <li class="menu-title mt-2">الإدارة</li>

                    <li>
                        <a href="#sidebarManagement" data-bs-toggle="collapse" class="tp-link">
                            <span class="nav-icon"><i class="mdi mdi-cog-outline"></i></span>
                            <span> إعدادات النظام </span>
                        </a>
                        <div class="collapse" id="sidebarManagement">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('branches.index') }}" class="tp-link">الفروع</a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.index') }}" class="tp-link">الموظفين</a>
                                </li>
                                @if(auth()->user()->isAdmin() || auth()->user()->isAccountant())
                                <li>
                                    <a href="{{ route('users.index') }}" class="tp-link">المستخدمين</a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('calibers.index') }}" class="tp-link">العيارات والضرائب</a>
                                </li>
                                <li>
                                    <a href="{{ route('categories.index') }}" class="tp-link">الأصناف</a>
                                </li>
                                <li>
                                    <a href="{{ route('expense-types.index') }}" class="tp-link">أنواع المصروفات</a>
                                </li>
                                <li>
                                    <a href="{{ route('settings.index') }}" class="tp-link">إعدادات النظام</a>
                                </li>
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
<!-- Right Sidebar End -->