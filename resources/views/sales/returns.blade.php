@extends('layouts.vertical', ['title' => 'قائمة المرتجعات'])
@section('title') قائمة المرتجعات @endsection

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h3 class="mb-1">
            <i class="mdi mdi-backup-restore me-2"></i>
            قائمة المرتجعات
        </h3>
        <p class="mb-0 opacity-75">عرض جميع عمليات البيع التي تم إرجاعها</p>
    </div>
    <div class="d-flex justify-content-end mb-3">
        @if(auth()->user()->isBranch())
            <a href="{{ url('/branch/daily-sales') }}" class="btn btn-outline-primary">
                <i class="mdi mdi-arrow-left"></i> العودة لقائمة اليومية
            </a>
        @else
            <a href="{{ route('t6u1v5w8.index') }}" class="btn btn-outline-primary">
                <i class="mdi mdi-arrow-left"></i> العودة لقائمة المبيعات
            </a>
        @endif
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <!-- Filters: Branch, Dates, Invoice Number (Return) -->
                    <form method="GET" class="mb-3 row g-2 align-items-end">
                        <div class="col-md-2">
                            <label for="branch_id" class="form-label mb-1">الفرع</label>
                            <select name="branch_id" id="branch_id" class="form-select">
                                <option value="">كل الفروع</option>
                                @foreach(App\Models\Branch::active()->get() as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id', '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label mb-1">من تاريخ</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label mb-1">إلى تاريخ</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label for="invoice_number" class="form-label mb-1">رقم الفاتورة</label>
                            <input type="number" name="invoice_number" id="invoice_number" class="form-control" placeholder="بحث برقم الفاتورة" value="{{ request('invoice_number') }}">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit" style="margin-top: 2px;">بحث</button>
                        </div>
                    </form>
                    <!-- Returns List -->
                    @if($returns->count() > 0)
                    <div class="table-responsive table-card sales-table">
                        <table class="table table-borderless table-centered align-middle table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">رقم الفاتورة</th>
                                    <th scope="col">الفرع</th>
                                    <th scope="col">الموظف</th>
                                    <th scope="col">الوزن (جم)</th>
                                    <th scope="col">سعر الجرام</th>
                                    <th scope="col">المبلغ</th>
                                    <th scope="col">تاريخ الإرجاع</th>
                                    <th scope="col" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returns as $sale)
                                <tr>
                                    <td><span class="badge bg-danger-subtle text-danger">{{ $sale->id }}</span></td>
                                    <td class="text-muted">{{ $sale->branch->name }}</td>
                                    <td class="text-muted">{{ $sale->employee->name }}</td>
                                    <td class="text-muted">{{ number_format($sale->weight, 2) }}</td>
                                    <td dir="ltr">
                                        @php
                                            $pricePerGram = $sale->weight > 0 ? $sale->total_amount / $sale->weight : 0;
                                        @endphp
                                        <span class="fw-semibold text-dark">{{ number_format($pricePerGram, 2) }}</span>
                                    </td>
                                    <td dir="ltr"><span class="fw-semibold text-dark">{{ number_format($sale->total_amount, 0, ',', '.') }} <small class="text-muted">ريال</small></span></td>
                                    <td class="text-muted">{{ $sale->returned_at ? $sale->returned_at->format('Y-m-d') : '-' }}</td>
                                    <td class="text-center sales-table-actions">
                                        <a href="{{ route('t6u1v5w8.show', $sale) }}" class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip" data-bs-original-title="عرض">
                                            <i class="mdi mdi-eye-outline text-info fs-16"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-sm bg-success-subtle ms-1 btn-unreturn-sale" data-sale-id="{{ $sale->id }}" data-url="{{ route('t6u1v5w8.b4c8d1e5', $sale) }}" data-bs-toggle="tooltip" data-bs-original-title="إرجاع كعملية بيع">
                                            <i class="mdi mdi-backup-restore text-success fs-16"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-semibold">
                                    <td colspan="3">الإجماليات</td>
                                    <td class="text-end">{{ number_format($returns->sum('weight'), 2) }}</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($returns->sum('total_amount'), 2) }}</td>
                                    <td class="text-end">عدد المرتجعات: {{ $returns->count() }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                            <small class="text-muted">
                                عرض {{ $returns->firstItem() }}–{{ $returns->lastItem() }} من أصل {{ $returns->total() }}
                            </small>
                        </div>
                        {{ $returns->links('pagination::bootstrap-5') }}
                    </div>
                    @else
                    <div class="py-5 text-center">
                        <i class="mdi mdi-backup-restore" style="font-size:3.5rem; color:#adb5bd;"></i>
                        <h5 class="text-muted mt-3">لا توجد مرتجعات</h5>
                        <p class="text-muted">لا توجد عمليات بيع تم إرجاعها حتى الآن</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let unreturnUrl = '';
    let unreturnForm = document.getElementById('unreturnSaleForm');
    document.querySelectorAll('.btn-unreturn-sale').forEach(function(btn) {
        btn.addEventListener('click', function() {
            unreturnUrl = this.getAttribute('data-url');
            const modal = new bootstrap.Modal(document.getElementById('unreturnSaleModal'));
            modal.show();
        });
    });
    if (unreturnForm) {
        unreturnForm.addEventListener('submit', function(e) {
            this.setAttribute('action', unreturnUrl);
        });
    }
});
</script>

<!-- Unreturn Confirmation Modal -->
<div class="modal fade" id="unreturnSaleModal" tabindex="-1" aria-labelledby="unreturnSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="unreturnSaleModalLabel">
                    <i class="mdi mdi-backup-restore text-success fs-4 me-2"></i>
                    تأكيد الإرجاع لقائمة المبيعات
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل تريد إعادة هذه الفاتورة إلى قائمة المبيعات؟
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <form id="unreturnSaleForm" method="POST" action="#">
                    @csrf
                    <button type="submit" class="btn btn-success">تأكيد</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
