<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/title-meta', ['title' => $title])
    @include('layouts.partials/head-css')
</head>

<body class="maintenance-bg-image">
    
    <!-- Begin page -->
    <div class="maintenance-pages">
        <div class="bg-overlay"></div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-xl-12 align-self-center">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>

</body>

</html>