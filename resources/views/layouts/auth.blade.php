<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/title-meta', ['title' => $title])
    @include('layouts.partials/head-css')
</head>

<body>

    <!-- Begin page -->
    <div class="account-page">
        <div class="container-fluid p-0">

            <div class="row align-items-center g-0 px-3 py-3 vh-100">
                @yield('content')
            </div>
        </div>
    </div>
    </div>

    @include('layouts.partials/vendor')

</body>

</html>