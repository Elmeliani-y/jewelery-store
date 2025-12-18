@extends('layouts.vertical', ['title' => 'إجمالي مبيعات الفروع'])

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-3">إجمالي مبيعات الفروع</h4>
            <form method="GET" class="row g-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">الفرع</label>
                    <select name="branch_id" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                                @if(!empty($salesPerCaliber))
                                <div class="mt-4">
                                    <h5 class="mb-2">إجمالي المبيعات لكل عيار:</h5>
                                    <div class="d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
                                        @php
                                            $badgeColors = ['primary','success','danger','warning','info','secondary','dark','purple','pink','teal','orange'];
                                            $colorCount = count($badgeColors);
                                        @endphp
                                        @foreach($salesPerCaliber as $i => $caliber)
                                            <span class="badge bg-{{ $badgeColors[$i % $colorCount] }}" style="font-size:1rem;">
                                                {{ $caliber['name'] }}: {{ number_format($caliber['total'], 2) }} ريال
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                <div class="col-md-3">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="from" class="form-control" value="{{ $from ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control" value="{{ $to ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>الفرع</th>
                                <th>عدد العمليات</th>
                                <th>إجمالي المبيعات</th>
                                <th>إجمالي الأوزان</th>
                                <th>معدل سعر الجرام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($totals as $branchId => $data)
                                <tr>
                                    <td>{{ $data['branch']->name }}</td>
                                    <td>{{ $data['count'] }}</td>
                                    <td class="fw-bold text-success">
                                        {{ number_format($data['total'], 2) }}
                                        @if(!empty($branchCaliberSales[$branchId]))
                                            <div class="d-flex flex-wrap mt-1" style="gap:0.25rem;">
                                                @php
                                                    $badgeColors = ['#0dcaf0','#20c997','#ffc107','#fd7e14','#6f42c1','#d63384','#198754','#0d6efd','#6610f2','#6c757d'];
                                                @endphp
                                                @foreach($branchCaliberSales[$branchId] as $i => $caliber)
                                                    <span style="background:{{$badgeColors[$i%count($badgeColors)]}};color:#fff;padding:2px 10px;border-radius:12px;font-size:0.95em;display:inline-block;min-width:38px;text-align:center;">
                                                        {{ $caliber['name'] }}: {{ number_format($caliber['total'], 2) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        {{ number_format($data['weight'], 3) }}
                                        @if(!empty($branchCaliberSales[$branchId]))
                                            <div class="d-flex flex-wrap mt-1" style="gap:0.25rem;">
                                                @php
                                                    $badgeColors = ['#0dcaf0','#20c997','#ffc107','#fd7e14','#6f42c1','#d63384','#198754','#0d6efd','#6610f2','#6c757d'];
                                                @endphp
                                                @foreach($branchCaliberSales[$branchId] as $i => $caliber)
                                                    <span style="background:{{$badgeColors[$i%count($badgeColors)]}};color:#fff;padding:2px 10px;border-radius:12px;font-size:0.95em;display:inline-block;min-width:38px;text-align:center;">
                                                        {{ $caliber['name'] }}: {{ number_format($caliber['weight'], 3) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-info">
                                        {{ number_format($data['avg_gram'], 2) }} ريال
                                        @if(!empty($branchCaliberSales[$branchId]))
                                            <div class="d-flex flex-wrap mt-1" style="gap:0.25rem;">
                                                @php
                                                    $badgeColors = ['#0dcaf0','#20c997','#ffc107','#fd7e14','#6f42c1','#d63384','#198754','#0d6efd','#6610f2','#6c757d'];
                                                @endphp
                                                @foreach($branchCaliberSales[$branchId] as $i => $caliber)
                                                    <span style="background:{{$badgeColors[$i%count($badgeColors)]}};color:#fff;padding:2px 10px;border-radius:12px;font-size:0.95em;display:inline-block;min-width:38px;text-align:center;">
                                                        {{ $caliber['name'] }}: {{ number_format($caliber['price_per_gram'], 2) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td
                                </tr>
                            @empty
                                <tr><td colspan="5">لا توجد بيانات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
