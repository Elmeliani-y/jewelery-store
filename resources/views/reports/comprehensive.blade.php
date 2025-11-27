@extends('layouts.vertical', ['title' => 'تقرير شامل'])
@section('title','تقرير شامل')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    @include('reports.partials.toolbar', [
        'title' => 'تقرير شامل',
        'backUrl' => route('reports.index'),
        'exportRoute' => 'reports.comprehensive',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="row g-3 mb-3">
        <div class="col-md-2"><div class="card"><div class="card-body"><div class="text-muted small">إجمالي المبيعات</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_sales'],2) }}</div></div></div></div>
        <div class="col-md-2"><div class="card"><div class="card-body"><div class="text-muted small">صافي المبيعات</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_net_sales'],2) }}</div></div></div></div>
        <div class="col-md-2"><div class="card"><div class="card-body"><div class="text-muted small">الضريبة</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_tax'],2) }}</div></div></div></div>
        <div class="col-md-2"><div class="card"><div class="card-body"><div class="text-muted small">الوزن</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_weight'],2) }} جم</div></div></div></div>
        <div class="col-md-2"><div class="card"><div class="card-body"><div class="text-muted small">المصروفات</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_expenses'],2) }}</div></div></div></div>
        <div class="col-md-2"><div class="card"><div class="card-body"><div class="text-muted small">صافي الربح</div><div class="fs-5" dir="ltr">{{ number_format($summary['net_profit'],2) }}</div></div></div></div>
    </div>

    <div class="card mb-3"><div class="card-header">المبيعات</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>#فاتورة</th><th>الفرع</th><th>الموظف</th><th>الصنف</th><th>العيار</th><th>الوزن</th><th>الإجمالي</th><th>الصافي</th><th>التاريخ</th></tr></thead>
                <tbody>
                    @forelse($sales as $s)
                    <tr>
                        <td>{{ $s->invoice_number }}</td>
                        <td>{{ $s->branch->name ?? '-' }}</td>
                        <td>{{ $s->employee->name ?? '-' }}</td>
                        <td>{{ $s->category->name ?? '-' }}</td>
                        <td>{{ $s->caliber->name ?? '-' }}</td>
                        <td dir="ltr">{{ number_format($s->weight,2) }}</td>
                        <td dir="ltr">{{ number_format($s->total_amount,2) }}</td>
                        <td dir="ltr">{{ number_format($s->net_amount,2) }}</td>
                        <td>{{ $s->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($sales, 'hasPages') && $sales->hasPages())
        <div class="p-2">
            {!! $sales->withQueryString()->links() !!}
        </div>
        @endif
    </div></div>

    <div class="card"><div class="card-header">المصروفات</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>الفرع</th><th>النوع</th><th>البيان</th><th>المبلغ</th><th>التاريخ</th></tr></thead>
                <tbody>
                    @forelse($expenses as $e)
                    <tr>
                        <td>{{ $e->branch->name ?? '-' }}</td>
                        <td>{{ $e->expenseType->name ?? '-' }}</td>
                        <td>{{ $e->description }}</td>
                        <td dir="ltr">{{ number_format($e->amount,2) }}</td>
                        <td>{{ $e->expense_date?->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($expenses, 'hasPages') && $expenses->hasPages())
        <div class="p-2">
            {!! $expenses->withQueryString()->links() !!}
        </div>
        @endif
    </div></div>
</div>
@endsection