@extends('layouts.vertical', ['title' => 'تقرير الموظفين'])
@section('title','تقرير الموظفين')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>تقرير الموظفين</h2>
        <p>التاريخ: {{ $filters['date_from'] ?? '-' }} - {{ $filters['date_to'] ?? '-' }}</p>
    </div>

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
                <thead><tr><th>الموظف</th><th>الفرع</th><th>عدد المبيعات</th><th>إجمالي المبيعات</th><th>إجمالي الوزن</th><th>سعر الجرام</th><th>صافي الربح (بعد الراتب)</th></tr></thead>
                <tbody>
                    @forelse($employeesData as $row)
                    @php
                        $empPricePerGram = $row['total_weight'] > 0 ? $row['total_sales'] / $row['total_weight'] : 0;
                        $isEmpLow = $minGramPrice > 0 && $empPricePerGram > 0 && $empPricePerGram < $minGramPrice;
                    @endphp
                    <tr>
                        <td>{{ $row['employee']->name }}</td>
                        <td>{{ optional($row['employee']->branch)->name ?? '-' }}</td>
                        <td>{{ $row['sales_count'] }}</td>
                        <td dir="ltr">{{ number_format($row['total_sales'],2) }}</td>
                        <td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
                        <td dir="ltr">
                            <span class="{{ $isEmpLow ? 'text-danger fw-bold' : 'text-warning fw-bold' }}" style="{{ $isEmpLow ? 'color: #dc3545 !important; text-decoration: underline;' : '' }}" data-bs-toggle="tooltip" title="{{ $isEmpLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : '' }}">
                                @if($row['total_weight'] > 0)
                                    {{ number_format($empPricePerGram, 2) }}
                                    @if($isEmpLow)
                                        <i class="mdi mdi-alert-circle-outline"></i>
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </td>
                        <td dir="ltr">{{ number_format($row['net_profit'],2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
                @php
                    $employeeRows = $employeesData instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($employeesData->items()) : $employeesData;
                    $totalWeight = $employeeRows->sum('total_weight');
                    $totalSales = $employeeRows->sum('total_sales');
                @endphp
                @if($employeeRows->count())
                <tfoot class="table-light">
                    <tr class="fw-semibold">
                        <td colspan="2">الإجماليات</td>
                        <td>{{ number_format($employeeRows->sum('sales_count')) }}</td>
                        <td dir="ltr">{{ number_format($totalSales,2) }}</td>
                        <td dir="ltr">{{ number_format($totalWeight,2) }}</td>
                        <td dir="ltr" class="text-warning">
                            @if($totalWeight > 0)
                                {{ number_format($totalSales / $totalWeight, 2) }}
                            @else
                                -
                            @endif
                        </td>
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

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection