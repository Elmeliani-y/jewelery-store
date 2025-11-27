<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/title-meta', ['title' => $title ?? 'Dashboard'])

    @include('layouts.partials/head-css')
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

        @include('layouts.partials/topbar')
        @include('layouts.partials/sidebar')
        @include('layouts.partials.flash')

        <div class="content-page">

            <div class="content">

                @yield('content')

            </div>
            
            {{-- Footer removed per request --}}

        </div>

    </div>

    @include('layouts.partials/vendor')

</body>

</html>