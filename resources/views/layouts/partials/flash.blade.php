@php($success = session('success'))
@php($error = session('error'))
@php($hasValidationErrors = $errors->any())

@if(auth()->check() && auth()->user()->isBranch())
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100">
        @if($success)
        <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="mdi mdi-check-circle-outline me-1"></i> {{ $success }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif
        @if($error)
        <div class="toast align-items-center text-bg-danger border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif
        @if(!$error && !$success && $hasValidationErrors)
        <div class="toast align-items-center text-bg-danger border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="9000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="mdi mdi-alert-outline me-1"></i>
                    <strong>تحقق من المدخلات:</strong>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

    @if($success || $error || $hasValidationErrors)
    <script>
        (function(){
            const toastEls=[].slice.call(document.querySelectorAll('.toast'));
            toastEls.forEach(function(el){
                const t=new bootstrap.Toast(el); t.show();
            });
        })();
    </script>
    @endif
@endif