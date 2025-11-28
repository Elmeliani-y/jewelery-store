@extends('layouts.vertical', ['title' => 'تقرير حسب الفروع'])
@section('title','تقرير حسب الفروع')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    @include('reports.partials.toolbar', [
        'title' => 'تقرير حسب الفروع',
        'backUrl' => route('reports.index'),
        'exportRoute' => 'reports.by-branch',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="card mt-3">
        <div class="card-header">ملخص حسب الفروع</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>الفرع</th>
                            <th>عدد المبيعات</th>
                            <th>إجمالي المبيعات</th>
                            <th>صافي المبيعات</th>
                            <th>إجمالي الوزن</th>
                            <th>عدد المصروفات</th>
                            <th>إجمالي المصروفات</th>
                            <th>صافي الربح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branchData as $row)
                        <tr>
                            <td>{{ $row['branch']->name }}</td>
                            <td>{{ $row['sales_count'] }}</td>
                            <td dir="ltr">{{ number_format($row['total_sales'],2) }}</td>
                            <td dir="ltr">{{ number_format($row['total_net_sales'],2) }}</td>
                            <td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
                            <td>{{ $row['expenses_count'] }}</td>
                            <td dir="ltr">{{ number_format($row['total_expenses'],2) }}</td>
                            <td dir="ltr">{{ number_format($row['net_profit'],2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($branchData instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-2">
                {!! $branchData->withQueryString()->links() !!}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
