<!DOCTYPE html>
    <html lang="en">

    <head>
        @include('layouts.partials/title-meta', ['title' => $title])
        @include('layouts.partials/head-css')
    </head>

    <body class="bg-primary-subtle bg-opacity-10">

        <div class="maintenance-pages">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-xl-12 align-self-center">
                        <div class="row">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- App js-->
        @vite(['resources/js/app.js'])
        @include('layouts.partials/vendor')

    </body>
</html>