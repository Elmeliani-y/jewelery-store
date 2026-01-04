@extends('layouts.vertical', ['title' => ' إدارة الموظفين'])
@section('css')
<style>
.employee-card{border:1px solid var(--bs-border-color);border-radius:12px;transition:.25s;background:var(--bs-card-bg);} .employee-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px);} [data-bs-theme="dark"] .employee-card{border-color:#2e2e2e;} .status-badge{padding:.35rem .7rem;border-radius:8px;font-size:.7rem;font-weight:600;} .filter-box{border:1px solid var(--bs-border-color);border-radius:12px;padding:1rem;background:var(--bs-body-bg);} .page-header{background:linear-gradient(135deg,#198754,#20c997);padding:1.5rem;border-radius:14px;color:#fff;margin-bottom:1.5rem;} [data-bs-theme="dark"] .page-header{background:linear-gradient(135deg,#1d2530,#2d3b33);} .small-label{font-size:.7rem;opacity:.75;}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1"><iconify-icon icon="solar:user-bold-duotone" class="fs-4 me-2"></iconify-icon> إدارة الموظفين</h4>
            <p class="mb-0 opacity-75">إضافة وتحديث بيانات الموظفين وربطهم بالفروع</p>
        </div>
        <div>
            <a href="{{ route('f3g8h1i4.create') }}" class="btn btn-light"><iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة موظف</a>
        </div>
    </div>

    @include('layouts.partials.flash')

    <div class="filter-box mb-4">
        <form class="row g-3" method="GET" action="{{ route('f3g8h1i4.index') }}">
            <div class="col-md-4">
                <label class="form-label small-label">الفرع</label>
                <select name="branch_id" class="form-select" onchange="this.form.submit()">
                    <option value="">كل الفروع</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small-label">الاسم</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="بحث بالاسم">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit"><iconify-icon icon="solar:search-bold" class="me-1"></iconify-icon> بحث</button>
            </div>
        </form>
    </div>

    <div class="row">
        @forelse($employees as $emp)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="employee-card p-3 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">{{ $emp->name }}</h5>
                        <span class="status-badge {{ $emp->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $emp->is_active ? 'نشط' : 'معطل' }}</span>
                    </div>
                    <iconify-icon icon="solar:user-bold-duotone" class="fs-2 text-success"></iconify-icon>
                </div>
                <div class="mt-2 flex-grow-1 small">
                    <div class="d-flex justify-content-between mb-1"><span>الفرع:</span><strong>{{ $emp->branch->name }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span>الراتب:</span><strong>{{ number_format($emp->salary,2) }} ريال</strong></div>
                    @if($emp->phone)<div class="text-muted mb-1">☎ {{ $emp->phone }}</div>@endif
                    @if($emp->email)<div class="text-muted mb-1">✉ {{ $emp->email }}</div>@endif
                </div>
                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('f3g8h1i4.show',$emp) }}" class="btn btn-secondary btn-sm flex-fill"><iconify-icon icon="solar:eye-bold" class="me-1"></iconify-icon> عرض</a>
                    <a href="{{ route('f3g8h1i4.edit',$emp) }}" class="btn btn-primary btn-sm flex-fill"><iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> تعديل</a>
                    <form action="{{ route('f3g8h1i4.j9k5l2m7',$emp) }}" method="POST" class="flex-fill">@csrf <button class="btn btn-sm btn-{{ $emp->is_active? 'warning':'success' }} w-100" type="submit"><iconify-icon icon="solar:{{ $emp->is_active? 'eye-closed':'eye' }}-bold" class="me-1"></iconify-icon>{{ $emp->is_active? 'تعطيل':'تفعيل' }}</button></form>
                    @if(!$emp->sales()->exists())
                    <form action="{{ route('f3g8h1i4.destroy',$emp) }}" method="POST" class="flex-fill delete-form">@csrf @method('DELETE') <button class="btn btn-danger btn-sm w-100" type="submit"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button></form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card p-5 text-center">
                <iconify-icon icon="solar:user-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                <h5 class="text-muted mb-2">لا يوجد موظفون</h5>
                <p class="text-muted mb-3">ابدأ بإضافة أول موظف للمتجر</p>
                <a href="{{ route('f3g8h1i4.create') }}" class="btn btn-success"><iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> إضافة موظف</a>
            </div>
        </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center mt-2">{{ $employees->appends(request()->query())->links() }}</div>
</div>
@endsection
@section('script')<script>document.querySelectorAll('.delete-form').forEach(f=>f.addEventListener('submit',e=>{if(!confirm('حذف الموظف؟')) e.preventDefault();}));</script>@endsection