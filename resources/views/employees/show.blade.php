@extends('layouts.vertical', ['title' => 'تفاصيل موظف'])
@section('css')<style>.stats-box{border:1px solid var(--bs-border-color);border-radius:12px;padding:1rem;} .sales-chip{background:var(--bs-primary-bg-subtle);color:var(--bs-primary);padding:.25rem .6rem;border-radius:20px;font-size:.7rem;font-weight:600;}</style>@endsection
@section('content')
<div class="container-fluid">
    <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:linear-gradient(135deg,#198754,#20c997);padding:1.25rem 1rem;border-radius:14px;color:#fff;">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('f3g8h1i4.index') }}" class="btn btn-light btn-sm"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
            <h5 class="mb-0"><iconify-icon icon="solar:user-bold-duotone" class="fs-4 me-1"></iconify-icon> الموظف: {{ $employee->name }}</h5>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('f3g8h1i4.edit',$employee) }}" class="btn btn-primary btn-sm"><iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> تعديل</a>
            <form action="{{ route('f3g8h1i4.j9k5l2m7',$employee) }}" method="POST">@csrf <button class="btn btn-{{ $employee->is_active? 'warning':'success' }} btn-sm" type="submit"><iconify-icon icon="solar:{{ $employee->is_active? 'eye-closed':'eye' }}-bold" class="me-1"></iconify-icon>{{ $employee->is_active? 'تعطيل':'تفعيل' }}</button></form>
            @if(!$employee->sales()->exists())
            <form action="{{ route('f3g8h1i4.destroy',$employee) }}" method="POST" class="delete-form">@csrf @method('DELETE') <button class="btn btn-danger btn-sm" type="submit"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button></form>
            @endif
        </div>
    </div>

    @include('layouts.partials.flash')

    <div class="row g-3">
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">البيانات الأساسية</h6>
                <p class="mb-1"><strong>الحالة:</strong> <span class="badge {{ $employee->is_active? 'bg-success-subtle text-success':'bg-danger-subtle text-danger' }}">{{ $employee->is_active? 'نشط':'معطل' }}</span></p>
                <p class="mb-1"><strong>الفرع:</strong> {{ $employee->branch->name }}</p>
                @if($employee->phone)<p class="mb-1"><strong>الهاتف:</strong> {{ $employee->phone }}</p>@endif
                @if($employee->email)<p class="mb-1"><strong>البريد:</strong> {{ $employee->email }}</p>@endif
                <p class="mb-1"><strong>الراتب:</strong> {{ number_format($employee->salary,2) }} ريال</p>
                <p class="text-muted small mt-2 mb-0">تم الإنشاء في {{ $employee->created_at->format('Y-m-d') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">إحصائيات المبيعات</h6>
                <div class="d-flex justify-content-between mb-2"><span>عدد المبيعات:</span><strong>{{ $salesStats['sales_count'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>إجمالي الوزن:</span><strong>{{ number_format($salesStats['total_weight'],2) }} جم</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>إجمالي المبيعات:</span><strong>{{ number_format($salesStats['total_sales'],2) }} ريال</strong></div>
                <div class="d-flex justify-content-between"><span>مبيعات الشهر:</span><strong>{{ number_format($salesStats['monthly_sales'],2) }} ريال</strong></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-box h-100">
                <h6 class="mb-3">آخر 5 مبيعات</h6>
                @php($recent=$employee->sales()->notReturned()->latest()->take(5)->get())
                @forelse($recent as $sale)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>#{{ $sale->invoice_number }}</span>
                        <span class="sales-chip">{{ number_format($sale->total_amount,2) }} ر</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">لا توجد مبيعات</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')<script>document.querySelectorAll('.delete-form').forEach(f=>{f.addEventListener('submit',e=>{if(!confirm('حذف الموظف؟')) e.preventDefault();});});</script>@endsection