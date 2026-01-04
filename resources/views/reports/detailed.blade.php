@extends('layouts.vertical', ['title' => 'تقرير مفصل'])
@section('title','تقرير مفصل')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>تقرير مفصل</h2>
        <p>التاريخ: {{ $filters['date_from'] ?? '-' }} - {{ $filters['date_to'] ?? '-' }}</p>
    </div>

    @include('reports.partials.toolbar', [
        'title' => 'تقرير مفصل',
        'backUrl' => route('t3u8v1w4.b1c5d8e3'),
        'exportRoute' => 'reports.detailed',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">إجمالي المبيعات</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_sales'],2) }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">صافي المبيعات</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_net_sales'],2) }}</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">الوزن</div><div class="fs-5" dir="ltr">{{ number_format($summary['total_weight'],2) }} جم</div></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">عدد الفواتير</div><div class="fs-5" dir="ltr">{{ number_format($summary['sales_count']) }}</div></div></div></div>
    </div>

    <div class="card mb-3"><div class="card-header">حسب الفرع</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>الفرع</th><th>عدد</th><th>الإجمالي</th><th>الوزن</th></tr></thead>
                <tbody>
                    @foreach($groupedData['by_branch'] as $branch=>$rows)
                    <tr><td>{{ $branch }}</td><td>{{ $rows->count() }}</td><td dir="ltr">{{ number_format($rows->sum('total_amount'),2) }}</td><td dir="ltr">{{ number_format($rows->sum('weight'),2) }}</td></tr>
                    @endforeach
                </tbody>
                    @php $branchRows = $groupedData['by_branch']; @endphp
                    @if($branchRows->count())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td>الإجماليات</td>
                            <td>{{ $branchRows->sum->count() }}</td>
                            <td dir="ltr">{{ number_format($branchRows->sum->sum('total_amount'),2) }}</td>
                            <td dir="ltr">{{ number_format($branchRows->sum->sum('weight'),2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-header">حسب الموظف</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>الموظف</th><th>عدد</th><th>الإجمالي</th><th>الوزن</th></tr></thead>
                <tbody>
                    @foreach($groupedData['by_employee'] as $emp=>$rows)
                    <tr><td>{{ $emp }}</td><td>{{ $rows->count() }}</td><td dir="ltr">{{ number_format($rows->sum('total_amount'),2) }}</td><td dir="ltr">{{ number_format($rows->sum('weight'),2) }}</td></tr>
                    @endforeach
                </tbody>
                    @php $empRows = $groupedData['by_employee']; @endphp
                    @if($empRows->count())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td>الإجماليات</td>
                            <td>{{ $empRows->sum->count() }}</td>
                            <td dir="ltr">{{ number_format($empRows->sum->sum('total_amount'),2) }}</td>
                            <td dir="ltr">{{ number_format($empRows->sum->sum('weight'),2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-header">حسب الصنف</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>الصنف</th><th>عدد</th><th>الإجمالي</th></tr></thead>
                <tbody>
                    @foreach($groupedData['by_category'] as $cat=>$rows)
                    <tr><td>{{ $cat }}</td><td>{{ $rows->count() }}</td><td dir="ltr">{{ number_format($rows->sum('total_amount'),2) }}</td></tr>
                    @endforeach
                </tbody>
                    @php $catRows = $groupedData['by_category']; @endphp
                    @if($catRows->count())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td>الإجماليات</td>
                            <td>{{ $catRows->sum->count() }}</td>
                            <td dir="ltr">{{ number_format($catRows->sum->sum('total_amount'),2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-header">حسب العيار</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>العيار</th><th>عدد</th><th>الإجمالي</th><th>الوزن</th></tr></thead>
                <tbody>
                    @foreach($groupedData['by_caliber'] as $cal=>$rows)
                    <tr><td>{{ $cal }}</td><td>{{ $rows->count() }}</td><td dir="ltr">{{ number_format($rows->sum('total_amount'),2) }}</td><td dir="ltr">{{ number_format($rows->sum('weight'),2) }}</td></tr>
                    @endforeach
                </tbody>
                    @php $calRows = $groupedData['by_caliber']; @endphp
                    @if($calRows->count())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td>الإجماليات</td>
                            <td>{{ $calRows->sum->count() }}</td>
                            <td dir="ltr">{{ number_format($calRows->sum->sum('total_amount'),2) }}</td>
                            <td dir="ltr">{{ number_format($calRows->sum->sum('weight'),2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-header">حسب التاريخ</div><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>التاريخ</th><th>عدد</th><th>الإجمالي</th></tr></thead>
                <tbody>
                    @foreach($groupedData['by_date'] as $date=>$rows)
                    <tr><td>{{ $date }}</td><td>{{ $rows->count() }}</td><td dir="ltr">{{ number_format($rows->sum('total_amount'),2) }}</td></tr>
                    @endforeach
                </tbody>
                    @php $dateRows = $groupedData['by_date']; @endphp
                    @if($dateRows->count())
                    <tfoot class="table-light">
                        <tr class="fw-semibold">
                            <td>الإجماليات</td>
                            <td>{{ $dateRows->sum->count() }}</td>
                            <td dir="ltr">{{ number_format($dateRows->sum->sum('total_amount'),2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
    </div></div>
</div>
@endsection