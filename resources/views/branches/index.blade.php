@extends('layouts.vertical', ['title' => 'إدارة الفروع'])
@section('title','إدارة الفروع')

@section('css')
<style>
    .branch-card{border-radius:12px;transition:.25s;background:var(--bs-card-bg);border:1px solid var(--bs-border-color);} 
    .branch-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px);} 
    [data-bs-theme="dark"] .branch-card{border-color:#2e2e2e;} 
    .status-badge{padding:.35rem .75rem;border-radius:8px;font-size:.75rem;font-weight:600;} 
    .page-header{background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.75rem 1.5rem;border-radius:14px;color:#fff;margin-bottom:2rem;} 
    [data-bs-theme="dark"] .page-header{background:linear-gradient(135deg,#1d2530,#3a4757);} 
    .stats-small{font-size:.75rem;opacity:.8;} 
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1"><iconify-icon icon="solar:buildings-bold-duotone" class="fs-4 me-2"></iconify-icon> إدارة الفروع</h4>
            <p class="mb-0 opacity-75">استعراض وإدارة الفروع وتفعيلها وتعطيلها</p>
        </div>
        <div>
            <a href="{{ route('x9y4z1a6.create') }}" class="btn btn-light">
                <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة فرع
            </a>
        </div>
    </div>

    @include('layouts.partials.flash')

    <div class="row">
        @forelse($branches as $branch)
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="branch-card p-3 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $branch->name }}</h5>
                            <span class="status-badge {{ $branch->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $branch->is_active ? 'مفعل' : 'معطل' }}</span>
                        </div>
                        <iconify-icon icon="solar:buildings-bold-duotone" class="fs-2 text-primary"></iconify-icon>
                    </div>

                    <div class="mt-3 flex-grow-1">
                        <div class="d-flex justify-content-between mb-1 stats-small">
                            <span>الموظفين:</span>
                            <strong>{{ $branch->employees_count }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1 stats-small">
                            <span>المبيعات:</span>
                            <strong>{{ $branch->sales_count }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 stats-small">
                            <span>المصروفات:</span>
                            <strong>{{ $branch->expenses_count }}</strong>
                        </div>
                        @if($branch->phone)
                            <div class="text-muted small">☎ {{ $branch->phone }}</div>
                        @endif
                        @if($branch->address)
                            <div class="text-muted small">📍 {{ $branch->address }}</div>
                        @endif
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('x9y4z1a6.show',$branch) }}" class="btn btn-secondary btn-sm flex-fill">
                            <iconify-icon icon="solar:eye-bold" class="me-1"></iconify-icon>عرض
                        </a>
                        <a href="{{ route('x9y4z1a6.edit',$branch) }}" class="btn btn-primary btn-sm flex-fill">
                            <iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon>تعديل
                        </a>
                        <form action="{{ route('x9y4z1a6.b2c7d5e8',$branch) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-{{ $branch->is_active? 'warning':'success' }} w-100">
                                <iconify-icon icon="solar:{{ $branch->is_active? 'eye-closed':'eye' }}-bold" class="me-1"></iconify-icon>
                                {{ $branch->is_active? 'تعطيل':'تفعيل' }}
                            </button>
                        </form>
                        @if(!$branch->employees_count && !$branch->sales_count)
                        <form action="{{ route('x9y4z1a6.destroy',$branch) }}" method="POST" class="flex-fill delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100" title="حذف">
                                <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center">
                    <iconify-icon icon="solar:buildings-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                    <h5 class="text-muted mb-2">لا توجد فروع</h5>
                    <p class="text-muted mb-3">ابدأ بإضافة فرع جديد للمتجر</p>
                    <a href="{{ route('x9y4z1a6.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة فرع</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-2">{{ $branches->links() }}</div>
</div>
@endsection

@section('script')
<script>
 document.querySelectorAll('.delete-form').forEach(f=>{
    f.addEventListener('submit',e=>{if(!confirm('هل أنت متأكد من حذف الفرع؟')) e.preventDefault();});
 });
</script>
@endsection