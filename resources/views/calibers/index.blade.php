@extends('layouts.master')

@section('title')
    @lang('translation.calibers-management')
@endsection

@section('css')
<style>
    .caliber-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .caliber-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    [data-bs-theme="dark"] .caliber-card {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: #1a1d21;
    }
    
    [data-bs-theme="dark"] .caliber-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .tax-badge {
        font-size: 1.25rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .action-buttons .btn {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 2rem;
    }
    
    [data-bs-theme="dark"] .page-header {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-1">
                    <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-4 me-2"></iconify-icon>
                    إدارة العيارات
                </h3>
                <p class="mb-0 opacity-75">إدارة عيارات الذهب ونسب الضرائب الخاصة بكل عيار</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('calibers.create') }}" class="btn btn-light">
                    <iconify-icon icon="solar:add-circle-bold" class="fs-5 me-1"></iconify-icon>
                    إضافة عيار جديد
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <iconify-icon icon="solar:check-circle-bold" class="fs-5 me-2"></iconify-icon>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <iconify-icon icon="solar:danger-circle-bold" class="fs-5 me-2"></iconify-icon>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Calibers Grid -->
    <div class="row">
        @forelse($calibers as $caliber)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card caliber-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">{{ $caliber->name }}</h4>
                                <span class="status-badge {{ $caliber->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    {{ $caliber->is_active ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                            <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-2 text-warning"></iconify-icon>
                        </div>

                        <div class="text-center my-4">
                            <div class="tax-badge bg-primary-subtle text-primary">
                                {{ $caliber->tax_rate }}%
                            </div>
                            <small class="text-muted d-block mt-2">نسبة الضريبة</small>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">عدد المبيعات:</small>
                                <strong>{{ $caliber->sales()->count() }}</strong>
                            </div>
                        </div>

                        <div class="action-buttons mt-3 d-flex gap-2">
                            <a href="{{ route('calibers.edit', $caliber) }}" class="btn btn-sm btn-primary flex-fill">
                                <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                تعديل
                            </a>
                            
                            <form action="{{ route('calibers.toggle-status', $caliber) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $caliber->is_active ? 'warning' : 'success' }} w-100">
                                    <iconify-icon icon="solar:{{ $caliber->is_active ? 'eye-closed' : 'eye' }}-bold"></iconify-icon>
                                    {{ $caliber->is_active ? 'تعطيل' : 'تفعيل' }}
                                </button>
                            </form>
                            
                            @if($caliber->sales()->count() == 0)
                                <form action="{{ route('calibers.destroy', $caliber) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <iconify-icon icon="solar:medal-star-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                        <h5 class="text-muted">لا توجد عيارات</h5>
                        <p class="text-muted mb-3">قم بإضافة عيار جديد للبدء</p>
                        <a href="{{ route('calibers.create') }}" class="btn btn-primary">
                            <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon>
                            إضافة عيار
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection

@section('script')
<script>
    // Confirm delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('هل أنت متأكد من حذف هذا العيار؟')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
