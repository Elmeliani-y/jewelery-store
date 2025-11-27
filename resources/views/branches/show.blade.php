@extends('layouts.vertical', ['title' => 'تفاصيل فرع'])
@section('title','تفاصيل فرع')
@section('css')<style>.stats-box{border:1px solid var(--bs-border-color);border-radius:12px;padding:1rem;}</style>@endsection
@section('content')
<div class="container-fluid">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:linear-gradient(135deg,#0d6efd,#6610f2);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('branches.index') }}" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
            <h5 class="mb-0"><iconify-icon icon="solar:buildings-bold-duotone" class="fs-4 me-1"></iconify-icon> الفرع: {{ $branch->name }}</h5>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('branches.edit',$branch) }}" class="btn btn-primary btn-sm"><iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> تعديل</a>
            <form action="{{ route('branches.toggle-status',$branch) }}" method="POST">@csrf <button class="btn btn-{{ $branch->is_active? 'warning':'success' }} btn-sm" type="submit"><iconify-icon icon="solar:{{ $branch->is_active? 'eye-closed':'eye' }}-bold" class="me-1"></iconify-icon>{{ $branch->is_active? 'تعطيل':'تفعيل' }}</button></form>
            @if(!$branch->employees_count && !$branch->sales_count)
            <form action="{{ route('branches.destroy',$branch) }}" method="POST" class="delete-form">@csrf @method('DELETE') <button class="btn btn-danger btn-sm" type="submit"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button></form>
            @endif
        </div>
    </div>

    @include('layouts.partials.flash')

    <div class="row g-3">
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">معلومات أساسية</h6>
                <p class="mb-1"><strong>الحالة:</strong> <span class="badge {{ $branch->is_active? 'bg-success-subtle text-success':'bg-danger-subtle text-danger' }}">{{ $branch->is_active? 'مفعل':'معطل' }}</span></p>
                @if($branch->phone)<p class="mb-1"><strong>الهاتف:</strong> {{ $branch->phone }}</p>@endif
                @if($branch->address)<p class="mb-1"><strong>العنوان:</strong> {{ $branch->address }}</p>@endif
                <p class="text-muted small mt-2 mb-0">تم الإنشاء في {{ $branch->created_at->format('Y-m-d') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">إحصائيات</h6>
                <div class="d-flex justify-content-between mb-2"><span>عدد الموظفين:</span><strong>{{ $branch->employees_count }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>عدد المبيعات:</span><strong>{{ $branch->sales_count }}</strong></div>
                <div class="d-flex justify-content-between"><span>عدد المصروفات:</span><strong>{{ $branch->expenses_count }}</strong></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">موظفون نشطون</h6>
                @forelse($branch->activeEmployees()->take(5)->get() as $emp)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $emp->name }}</span>
                        <span class="text-muted">{{ number_format($emp->salary,2) }} ريال</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">لا يوجد موظفون نشطون</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')<script>document.querySelectorAll('.delete-form').forEach(f=>{f.addEventListener('submit',e=>{if(!confirm('هل أنت متأكد من الحذف؟')) e.preventDefault();});});</script>@endsection