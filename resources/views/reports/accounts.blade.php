@extends('layouts.vertical', ['title' => 'تقرير الحسابات'])
@section('title', 'تقرير الحسابات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">تقرير الحسابات</h4>
            </div>
        </div>
    </div>
    <form method="GET" action="{{ route('reports.accounts') }}" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="branch_id" class="form-label">الفرع</label>
                <select name="branch_id" id="branch_id" class="form-select">
                    <option value="">اختر الفرع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', date('Y-m-01')) }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', date('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">عرض التقرير</button>
            </div>
        </div>
    </form>
    @if(isset($summary))
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">ملخص الحسابات</h5>
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="alert alert-info">المبلغ شبكة: <b>{{ number_format($summary['network'], 2) }}</b></div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">المبلغ نقدي: <b>{{ number_format($summary['cash'], 2) }}</b></div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">المبلغ تحويل: <b>{{ number_format($summary['transfer'], 2) }}</b></div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    @endpush
    @endif
</div>
@endsection
