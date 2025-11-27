@extends('layouts.vertical', ['title' => 'تقرير الموظفين'])
@section('title','تقرير الموظفين')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    @include('reports.partials.toolbar', [
        'title' => 'تقرير الموظفين',
        'backUrl' => route('reports.index'),
        'exportRoute' => 'reports.employees',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="card"><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>الموظف</th><th>الفرع</th><th>عدد المبيعات</th><th>إجمالي المبيعات</th><th>إجمالي الوزن</th><th>صافي الربح (بعد الراتب)</th></tr></thead>
                <tbody>
                    @forelse($employeesData as $row)
                    <tr>
                        <td>{{ $row['employee']->name }}</td>
                        <td>{{ optional($row['employee']->branch)->name ?? '-' }}</td>
                        <td>{{ $row['sales_count'] }}</td>
                        <td dir="ltr">{{ number_format($row['total_sales'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['net_profit'],2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
                @php
                    $employeeRows = $employeesData instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($employeesData->items()) : $employeesData;
                @endphp
                @if($employeeRows->count())
                <tfoot class="table-light">
                    <tr class="fw-semibold">
                        <td colspan="2">الإجماليات</td>
                        <td>{{ number_format($employeeRows->sum('sales_count')) }}</td>
                        <td dir="ltr">{{ number_format($employeeRows->sum('total_sales'),2) }}</td>
                        <td dir="ltr">{{ number_format($employeeRows->sum('total_weight'),2) }}</td>
                        <td dir="ltr">{{ number_format($employeeRows->sum('net_profit'),2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($employeesData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="p-2">
            {!! $employeesData->withQueryString()->links() !!}
        </div>
        @endif
    </div></div>
</div>
@endsection