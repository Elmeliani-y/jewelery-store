@extends('layouts.vertical', ['title' => 'إدارة أنواع المصروفات'])
@section('title','إدارة أنواع المصروفات')

@section('css')
<style>
    .expense-type-card{border-radius:12px;transition:.25s;background:var(--bs-card-bg);border:1px solid var(--bs-border-color);}
    .expense-type-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px);}
    .page-header{background:linear-gradient(135deg,#dc3545,#fd7e14);padding:1.75rem 1.5rem;border-radius:14px;color:#fff;margin-bottom:2rem;}
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1"><iconify-icon icon="solar:card-transfer-bold-duotone" class="fs-4 me-2"></iconify-icon> إدارة أنواع المصروفات</h4>
            <p class="mb-0 opacity-75">إدارة تصنيفات المصروفات</p>
        </div>
        <div>
            <a href="{{ route('z8a3b6c2.create') }}" class="btn btn-light">
                <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة نوع جديد
            </a>
        </div>
    </div>

    <div class="row">
        @forelse($expenseTypes as $type)
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="expense-type-card card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $type->name }}</h5>
                        @if($type->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">عدد المصروفات: {{ $type->expenses_count }}</small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('z8a3b6c2.edit', $type) }}" class="btn btn-sm btn-primary flex-fill">
                            <iconify-icon icon="solar:pen-bold"></iconify-icon> تعديل
                        </a>
                        <form action="{{ route('z8a3b6c2.destroy', $type) }}" method="POST" class="flex-fill" onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100">
                                <iconify-icon icon="solar:trash-bin-bold"></iconify-icon> حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                لا توجد أنواع مصروفات مسجلة
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $expenseTypes->links() }}
    </div>
</div>
@endsection
