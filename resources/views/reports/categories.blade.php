@extends('layouts.vertical', ['title' => 'تقرير الأقسام'])
@section('title','تقرير الأقسام')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    @include('reports.partials.toolbar', [
        'title' => 'تقرير الأقسام',
        'backUrl' => route('reports.index'),
        'exportRoute' => 'reports.categories',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="card"><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>القسم</th><th>عدد المبيعات</th><th>إجمالي المبيعات</th><th>إجمالي الوزن</th></tr></thead>
                <tbody>
                    @forelse($categoriesData as $row)
                    <tr>
                        <td>{{ $row['category']->name }}</td>
                        <td>{{ $row['sales_count'] }}</td>
                        <td dir="ltr">{{ number_format($row['total_amount'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
                @php
                    $categoryRows = $categoriesData instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($categoriesData->items()) : $categoriesData;
                @endphp
                @if($categoryRows->count())
                <tfoot class="table-light">
                    <tr class="fw-semibold">
                        <td>الإجماليات</td>
                        <td>{{ number_format($categoryRows->sum('sales_count')) }}</td>
                        <td dir="ltr">{{ number_format($categoryRows->sum('total_amount'),2) }}</td>
                        <td dir="ltr">{{ number_format($categoryRows->sum('total_weight'),2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($categoriesData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="p-2">
            {!! $categoriesData->withQueryString()->links() !!}
        </div>
        @endif
    </div></div>
</div>
@endsection