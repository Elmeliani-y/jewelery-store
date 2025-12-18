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
                <div class="col-md-3">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
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
                                    <td class="fw-bold text-success">{{ number_format($data['total'], 2) }} ريال</td>
                                    <td class="fw-bold">{{ number_format($data['weight'], 3) }} جم</td>
                                    <td class="fw-bold text-info">{{ number_format($data['avg_gram'], 2) }} ريال</td>
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
