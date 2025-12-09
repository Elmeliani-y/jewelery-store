@extends('layouts.vertical', ['title' => 'إدارة الأصناف'])
@section('title','إدارة الأصناف')

@section('css')
<style>
    .category-card{border-radius:12px;transition:.25s;background:var(--bs-card-bg);border:1px solid var(--bs-border-color);}
    .category-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px);}
    .page-header{background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.75rem 1.5rem;border-radius:14px;color:#fff;margin-bottom:2rem;}
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
            <h4 class="mb-1"><iconify-icon icon="solar:tag-bold-duotone" class="fs-4 me-2"></iconify-icon> إدارة الأصناف</h4>
            <p class="mb-0 opacity-75">إدارة أصناف المبيعات</p>
        </div>
        <div>
            <a href="{{ route('categories.create') }}" class="btn btn-light">
                <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة صنف جديد
            </a>
        </div>
    </div>

    <div class="row">
        @forelse($categories as $category)
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="category-card card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $category->name }}</h5>
                        @if($category->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">عدد المبيعات: {{ $category->sales_count }}</small>
                        @if($category->defaultCaliber)
                            <small class="text-muted d-block mt-1">
                                <iconify-icon icon="solar:medal-star-bold-duotone" class="text-warning"></iconify-icon>
                                العيار الافتراضي: <strong>{{ $category->defaultCaliber->name }}</strong>
                            </small>
                        @else
                            <small class="text-muted d-block mt-1">
                                <iconify-icon icon="solar:info-circle-bold-duotone"></iconify-icon>
                                بدون عيار افتراضي
                            </small>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary flex-fill">
                            <iconify-icon icon="solar:pen-bold"></iconify-icon> تعديل
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="flex-fill" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')">
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
                لا توجد أصناف مسجلة
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $categories->links() }}
    </div>
</div>
@endsection
