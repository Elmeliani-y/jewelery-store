@extends('layouts.vertical', ['title' => 'تفاصيل المصروف'])

@section('css')
    @include('components.form-styles')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">تفاصيل المصروف</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">المصروفات</a></li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">المعلومات الأساسية</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th>رقم المصروف</th>
                                    <td>{{ $expense->id }}</td>
                                </tr>
                                <tr>
                                    <th>الفرع</th>
                                    <td>{{ $expense->branch->name }}</td>
                                </tr>
                                <tr>
                                    <th>نوع المصروف</th>
                                    <td>{{ $expense->expenseType->name }}</td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $expense->description }}</td>
                                </tr>
                                <tr>
                                    <th>المبلغ</th>
                                    <td dir="ltr">{{ number_format($expense->amount, 2) }} ريال</td>
                                </tr>
                                <tr>
                                    <th>تاريخ المصروف</th>
                                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                </tr>
                                @if($expense->notes)
                                <tr>
                                    <th>ملاحظات</th>
                                    <td>{{ $expense->notes }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الإجراءات</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">
                            <i class="mdi mdi-pencil-outline me-1"></i> تعديل
                        </a>
                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="mdi mdi-delete-outline me-1"></i> حذف
                            </button>
                        </form>
                        <a href="{{ route('expenses.index') }}" class="btn btn-light">عودة إلى المصروفات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection