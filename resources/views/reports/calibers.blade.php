@extends('layouts.vertical', ['title' => 'تقرير العيارات'])
@section('title','تقرير العيارات')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    @include('reports.partials.toolbar', [
        'title' => 'تقرير العيارات',
        'backUrl' => route('reports.index'),
        'exportRoute' => 'reports.calibers',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="card"><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>العيار</th><th>عدد المبيعات</th><th>الإجمالي</th><th>الوزن</th><th>الضريبة</th><th>الصافي</th></tr></thead>
                <tbody>
                    @forelse($calibersData as $row)
                    <tr>
                        <td>{{ $row['caliber']->name }}</td>
                        <td>{{ $row['sales_count'] }}</td>
                        <td dir="ltr">{{ number_format($row['total_amount'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['total_tax'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['net_amount'],2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
                @php
                    $calibersRows = $calibersData instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($calibersData->items()) : $calibersData;
                @endphp
                @if($calibersRows->count())
                <tfoot class="table-light">
                    <tr class="fw-semibold">
                        <td>الإجماليات</td>
                        <td>{{ number_format($calibersRows->sum('sales_count')) }}</td>
                        <td dir="ltr">{{ number_format($calibersRows->sum('total_amount'),2) }}</td>
                        <td dir="ltr">{{ number_format($calibersRows->sum('total_weight'),2) }}</td>
                        <td dir="ltr">{{ number_format($calibersRows->sum('total_tax'),2) }}</td>
                        <td dir="ltr">{{ number_format($calibersRows->sum('net_amount'),2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($calibersData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="p-2">
            {!! $calibersData->withQueryString()->links() !!}
        </div>
        @endif
    </div></div>
</div>
@endsection