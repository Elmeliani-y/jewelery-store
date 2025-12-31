<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar-dark { background: #343a40; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">{{ config('app.name', 'Laravel') }}</a>
        </div>
    </nav>
    <main class="container">
        @yield('content')
    </main>

    <!-- Admin Only Error Modal -->
    @if(session('admin_only_error'))
        <div class="modal fade show" id="adminOnlyErrorModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">تنبيه</h5>
                    </div>
                    <div class="modal-body text-center">
                        <span class="fw-bold">{{ session('admin_only_error') }}</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="document.getElementById('adminOnlyErrorModal').style.display='none'; location.href='/'">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Prevent interaction with background
            document.body.classList.add('modal-open');
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
