@extends('layouts.vertical', ['title' => 'تقرير حسب الفروع'])
@section('title','تقرير حسب الفروع')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>تقرير حسب الفروع</h2>
        <p>التاريخ: {{ $filters['date_from'] ?? '-' }} - {{ $filters['date_to'] ?? '-' }}</p>
    </div>

    @include('reports.partials.toolbar', [
        'title' => 'تقرير حسب الفروع',
        'backUrl' => route('t3u8v1w4.b1c5d8e3'),
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
                            <th>سعر الجرام</th>
                            <th>عدد المصروفات</th>
                            <th>إجمالي المصروفات</th>
                            <th>صافي الربح</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branchData as $row)
                        @php
                            $branchPricePerGram = $row['total_weight'] > 0 ? $row['total_sales'] / $row['total_weight'] : 0;
                            $isBranchLow = $minGramPrice > 0 && $branchPricePerGram > 0 && $branchPricePerGram < $minGramPrice;
                        @endphp
                        <tr>
                            <td>{{ $row['branch']->name }}</td>
                            <td>{{ $row['sales_count'] }}</td>
                            <td dir="ltr">{{ number_format($row['total_sales'],2) }}</td>
                            <td dir="ltr">{{ number_format($row['total_net_sales'],2) }}</td>
                            <td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
                            <td dir="ltr">
                                <span class="{{ $isBranchLow ? 'text-danger fw-bold' : 'text-warning fw-bold' }}" style="{{ $isBranchLow ? 'color: #dc3545 !important; text-decoration: underline;' : '' }}" data-bs-toggle="tooltip" title="{{ $isBranchLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : '' }}">
                                    @if($row['total_weight'] > 0)
                                        {{ number_format($branchPricePerGram, 2) }}
                                        @if($isBranchLow)
                                            <i class="mdi mdi-alert-circle-outline"></i>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
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
