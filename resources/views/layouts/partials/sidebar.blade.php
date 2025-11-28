<!-- Right Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a href="{{ auth()->user()->isBranch() ? route('sales.create') : route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-light.png" alt="" height="24">
                    </span>
                </a>
                <a href="{{ auth()->user()->isBranch() ? route('sales.create') : route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-dark.png" alt="" height="24">
                    </span>
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
                @endif

                <li class="menu-title mt-2">إدارة المبيعات</li>
                @if(auth()->user()->isBranch())
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
                                    <a href="{{ route('sales.create') }}" class="tp-link">تسجيل مبيعة جديدة</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(auth()->user()->isBranch())
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
                            <span> التقارير </span>
                        </a>
                        <div class="collapse" id="sidebarReports">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('reports.comprehensive') }}" class="tp-link">التقرير الشامل</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.detailed') }}" class="tp-link">التقرير المفصل</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.calibers') }}" class="tp-link">تقرير العيارات</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.categories') }}" class="tp-link">تقرير الأصناف</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.employees') }}" class="tp-link">تقرير الموظفين</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.net-profit') }}" class="tp-link">صافي الربح</a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.by-branch') }}" class="tp-link">تقرير حسب الفروع</a>
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
                                <li>
                                    <a href="{{ route('calibers.index') }}" class="tp-link">العيارات والضرائب</a>
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