@extends('layouts.vertical', ['title' => 'تقرير صافي الربح'])
@section('title','تقرير صافي الربح')
@section('css')
@include('reports.partials.print-css')
@endsection
@section('content')
<div class="container-fluid">
    <!-- Print Title -->
    <div class="print-title" style="display: none;">
        <h2>تقرير صافي الربح</h2>
        <p>التاريخ: {{ $filters['date_from'] ?? '-' }} - {{ $filters['date_to'] ?? '-' }}</p>
    </div>

    @include('reports.partials.toolbar', [
        'title' => 'تقرير صافي الربح',
        'backUrl' => route('t3u8v1w4.b1c5d8e3'),
        'exportRoute' => 'reports.net-profit',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    @include('reports.partials.filters')

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card"><div class="card-body">
                <div class="text-muted">إجمالي صافي المبيعات</div>
                <div class="fs-5" dir="ltr">{{ number_format($total_sales ?? 0,2) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body">
                <div class="text-muted">إجمالي المصروفات</div>
                <div class="fs-5" dir="ltr">{{ number_format($total_expenses ?? 0,2) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body">
                <div class="text-muted">إجمالي الرواتب</div>
                <div class="fs-5" dir="ltr">{{ number_format($total_salaries ?? 0,2) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body">
                <div class="text-muted">صافي الربح</div>
                <div class="fs-4 fw-bold" dir="ltr">{{ number_format($net_profit ?? 0,2) }}</div>
            </div></div>
        </div>
    </div>

    <div class="card"><div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>المؤشر</th><th>القيمة</th></tr></thead>
                <tbody>
                    <tr><td>إجمالي صافي المبيعات</td><td dir="ltr">{{ number_format($total_sales ?? 0,2) }}</td></tr>
                    <tr><td>إجمالي المصروفات</td><td dir="ltr">{{ number_format($total_expenses ?? 0,2) }}</td></tr>
                    <tr><td>إجمالي الرواتب</td><td dir="ltr">{{ number_format($total_salaries ?? 0,2) }}</td></tr>
                    <tr class="table-light"><td class="fw-bold">صافي الربح</td><td class="fw-bold" dir="ltr">{{ number_format($net_profit ?? 0,2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div></div>
</div>
@endsection