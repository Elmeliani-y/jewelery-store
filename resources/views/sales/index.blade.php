@extends('layouts.vertical')

@section('title')
    المبيعات
@endsection

@section('css')
<style>
    .page-header {
        background: var(--bs-primary);
        padding: 1.75rem 2rem;
        border-radius: .75rem;
        color: #fff;
        margin-bottom: 1.75rem;
    }
    [data-bs-theme="dark"] .page-header { background: var(--bs-primary); }
    .sales-table .badge { font-weight: 500; }
    .sales-table td, .sales-table th { white-space: nowrap; }
    .sales-table .btn-icon { display:inline-flex; align-items:center; justify-content:center; }
    .sales-table-actions .btn-icon { width:32px; height:32px; }
    .table-card { padding:0; }
    .pagination .page-link { font-size:.75rem; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-1">
                    <i class="ri-shopping-bag-line me-2"></i>
                    إدارة المبيعات
                </h3>
                <p class="mb-0 opacity-75">عرض وإدارة جميع عمليات البيع</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales.create') }}" class="btn btn-light">
                    <i class="ri-add-line me-1"></i>
                    إضافة مبيعة جديدة
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Sales List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($sales->count() > 0)
                    <div class="table-responsive table-card sales-table">
                        <table class="table table-borderless table-centered align-middle table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">رقم الفاتورة</th>
                                    <th scope="col">الفرع</th>
                                    <th scope="col">الموظف</th>
                                    <th scope="col">الفئة</th>
                                    <th scope="col">العيار</th>
                                    <th scope="col">الوزن (جم)</th>
                                    <th scope="col">المبلغ</th>
                                    <th scope="col">التاريخ</th>
                                    <th scope="col" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">{{ $sale->invoice_number }}</span>
                                    </td>
                                    <td class="text-muted">{{ $sale->branch->name }}</td>
                                    <td class="text-muted">{{ $sale->employee->name }}</td>
                                    <td class="text-muted">{{ $sale->category->name }}</td>
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning">{{ $sale->caliber->name }}</span>
                                    </td>
                                    <td class="text-muted">{{ number_format($sale->weight, 2) }}</td>
                                    <td dir="ltr">
                                        <span class="fw-semibold text-dark">{{ number_format($sale->total_amount, 0, ',', '.') }} <small class="text-muted">ريال</small></span>
                                    </td>
                                    <td class="text-muted">{{ $sale->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center sales-table-actions">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip" data-bs-original-title="عرض">
                                            <i class="mdi mdi-eye-outline text-info fs-16"></i>
                                        </a>
                                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-icon btn-sm bg-warning-subtle" data-bs-toggle="tooltip" data-bs-original-title="تعديل">
                                            <i class="mdi mdi-pencil-outline text-warning fs-16"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-sm bg-danger-subtle btn-delete-sale" 
                                                data-url="{{ route('sales.destroy', $sale) }}" 
                                                data-invoice="{{ $sale->invoice_number }}"
                                                data-bs-toggle="tooltip" data-bs-original-title="حذف">
                                            <i class="mdi mdi-delete-outline text-danger fs-16"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <small class="text-muted">عرض {{ $sales->firstItem() }}–{{ $sales->lastItem() }} من أصل {{ $sales->total() }}</small>
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>
                    @else
                    <div class="py-5 text-center">
                        <i class="mdi mdi-shopping-outline" style="font-size:3.5rem; color:#adb5bd;"></i>
                        <h5 class="text-muted mt-3">لا توجد مبيعات</h5>
                        <p class="text-muted">قم بإضافة مبيعة جديدة للبدء</p>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            إضافة مبيعة
                        </a>
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
        const modalEl = document.getElementById('deleteSaleModal');
        const form = document.getElementById('deleteSaleForm');
        const invoiceEl = document.getElementById('deleteSaleInvoice');
        document.querySelectorAll('.btn-delete-sale').forEach(function(btn) {
                btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        const invoice = this.getAttribute('data-invoice');
                        form.setAttribute('action', url);
                        if (invoiceEl) invoiceEl.textContent = invoice || '—';
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                });
        });
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deleteSaleModalLabel">
                        <iconify-icon icon="solar:warning-triangle-bold" class="text-danger fs-4 me-2"></iconify-icon>
                        تأكيد الحذف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذه المبيعة؟<br>
                رقم الفاتورة: <strong id="deleteSaleInvoice">—</strong>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteSaleForm" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
