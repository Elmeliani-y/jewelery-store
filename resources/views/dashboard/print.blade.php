@extends('layouts.vertical', ['title' => 'تقرير لوحة التحكم'])
@section('css')
<style>
@media print { @page { size: A4 portrait; margin: 12mm; } .topbar-custom,.app-sidebar-menu,.footer,.btn-toolbar { display:none !important; } .card{box-shadow:none !important;border:0 !important;} }
.table td[dir="ltr"], .table th[dir="ltr"] { text-align: left; }
.header-badge{border:1px dashed var(--bs-border-color);border-radius:8px;padding:.25rem .5rem}
</style>
@endsection
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="m-0">تقرير لوحة التحكم</h4>
      <div class="text-muted small mt-1">
        <span class="header-badge">الفترة: {{ $period === 'custom' ? ($startDate.' إلى '.$endDate) : $period }}</span>
        @if($branch)
          <span class="header-badge ms-1">الفرع: {{ $branch->name }}</span>
        @endif
      </div>
    </div>
    <div class="btn-toolbar">
      <a href="{{ route('dashboard', request()->query()) }}" class="btn btn-light btn-sm">عودة</a>
      <button class="btn btn-primary btn-sm ms-2" onclick="window.print()">طباعة</button>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">إجمالي المبيعات</div>
      <div class="fs-5" dir="ltr">{{ number_format($metrics['total_sales'],2) }}</div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">صافي المبيعات</div>
      <div class="fs-5" dir="ltr">{{ number_format($metrics['total_net_sales'],2) }}</div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">المصروفات</div>
      <div class="fs-5" dir="ltr">{{ number_format($metrics['total_expenses'],2) }}</div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body">
      <div class="text-muted small">صافي الربح</div>
      <div class="fs-5 fw-semibold" dir="ltr">{{ number_format($metrics['net_profit'],2) }}</div>
    </div></div></div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card"><div class="card-header">المبيعات حسب الفئة</div><div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>الفئة</th><th>عدد</th><th>إجمالي</th></tr></thead>
            <tbody>
              @foreach(($chartsData['sales_by_category'] ?? []) as $row)
                <tr>
                  <td>{{ $row['category'] }}</td>
                  <td dir="ltr">{{ number_format($row['count']) }}</td>
                  <td dir="ltr">{{ number_format($row['amount'],2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div></div>
    </div>

    <div class="col-md-6">
      <div class="card"><div class="card-header">المصروفات حسب النوع</div><div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>النوع</th><th>عدد</th><th>إجمالي</th></tr></thead>
            <tbody>
              @foreach(($chartsData['expenses_by_type'] ?? []) as $row)
                <tr>
                  <td>{{ $row['type'] }}</td>
                  <td dir="ltr">{{ number_format($row['count']) }}</td>
                  <td dir="ltr">{{ number_format($row['amount'],2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div></div>
    </div>

    <div class="col-md-6">
      <div class="card"><div class="card-header">أفضل الفروع</div><div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>الفرع</th><th>عدد</th><th>المبيعات</th><th>الوزن</th></tr></thead>
            <tbody>
              @foreach($topPerformers['branches'] as $row)
                <tr>
                  <td>{{ $row->branch->name }}</td>
                  <td dir="ltr">{{ number_format($row->count) }}</td>
                  <td dir="ltr">{{ number_format($row->amount,2) }}</td>
                  <td dir="ltr">{{ number_format($row->weight,2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div></div>
    </div>

    <div class="col-md-6">
      <div class="card"><div class="card-header">أفضل الموظفين</div><div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>الموظف</th><th>عدد</th><th>المبيعات</th><th>الفرع</th></tr></thead>
            <tbody>
              @foreach($topPerformers['employees'] as $row)
                <tr>
                  <td>{{ $row->employee->name }}</td>
                  <td dir="ltr">{{ number_format($row->count) }}</td>
                  <td dir="ltr">{{ number_format($row->amount,2) }}</td>
                  <td>{{ $row->employee->branch->name ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div></div>
    </div>
  </div>
</div>
@endsection
