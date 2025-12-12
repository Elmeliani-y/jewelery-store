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
                    <div class="d-flex justify-content-end mt-4">
                        @if(auth()->user()->isBranch())
                            <a href="{{ url('/branch/daily-sales') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة اليومية
                            </a>
                        @else
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-primary">
                                <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة
                            </a>
                        @endif
                    </div>
                <div class="d-flex justify-content-end mb-3">
                    @if(auth()->user()->isBranch())
                        <a href="{{ url('/branch/daily-sales') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة اليومية
                        </a>
                    @else
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة
                        </a>
                    @endif
                </div>
            @if(!request()->filled('invoice_number'))
            <div class="col-auto">
                @if(auth()->user()->isBranch())
                    <a href="{{ url('/branch/daily-sales') }}" class="btn btn-light">
                        <i class="ri-add-line me-1"></i>
                        إضافة مبيعة جديدة
                    </a>
                @else
                    <a href="{{ route('sales.create') }}" class="btn btn-light">
                        <i class="ri-add-line me-1"></i>
                        إضافة مبيعة جديدة
                    </a>
                @endif
            </div>
            @endif
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

    <!-- Search by Invoice Number -->
    <form method="GET" class="mb-3" style="max-width:300px">
        <div class="input-group">
            <input type="number" name="invoice_number" class="form-control" placeholder="بحث برقم الفاتورة" value="{{ request('invoice_number') }}">
            <button class="btn btn-primary" type="submit">بحث</button>
        </div>
    </form>

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
                                    <th scope="col">الوزن (جم)</th>
                                    <th scope="col">سعر الجرام</th>
                                    <th scope="col">المبلغ</th>
                                    <th scope="col">التاريخ</th>
                                    <th scope="col" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">{{ $sale->id }}</span>
                                    </td>
                                    <td class="text-muted">{{ $sale->branch->name }}</td>
                                    <td class="text-muted">{{ $sale->employee->name }}</td>
                                    <td class="text-muted">{{ number_format($sale->weight, 2) }}</td>
                                    <td dir="ltr">
                                        @php
                                            $pricePerGram = $sale->weight > 0 ? $sale->total_amount / $sale->weight : 0;
                                            $isLowPrice = $minGramPrice > 0 && $pricePerGram < $minGramPrice;
                                        @endphp
                                        <span class="fw-semibold {{ $isLowPrice ? 'text-danger' : 'text-dark' }}">
                                            {{ number_format($pricePerGram, 2) }}
                                            @if($isLowPrice)
                                                <i class="mdi mdi-alert-circle-outline" data-bs-toggle="tooltip" title="أقل من الحد الأدنى ({{ number_format($minGramPrice, 2) }})"></i>
                                            @endif
                                        </span>
                                    </td>
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
                                        @if(!auth()->user()->isBranch())
                                            <button type="button" class="btn btn-icon btn-sm bg-danger-subtle btn-delete-sale" 
                                                    data-url="{{ route('sales.destroy', $sale) }}" 
                                                    data-invoice="{{ $sale->invoice_number }}"
                                                    data-bs-toggle="tooltip" data-bs-original-title="حذف">
                                                <i class="mdi mdi-delete-outline text-danger fs-16"></i>
                                            </button>
                                        @endif
                                        @if(!$sale->is_returned)
                                            @if(!auth()->user()->isBranch())
                                                <button type="button" class="btn btn-icon btn-sm bg-secondary-subtle ms-1 btn-return-sale" data-sale-id="{{ $sale->id }}" data-url="{{ route('sales.return', $sale) }}" data-bs-toggle="tooltip" data-bs-original-title="تعيين كمرتجع">
                                                    <i class="mdi mdi-backup-restore text-secondary fs-16"></i>
                                                </button>
                                            @endif
                                        @else
                                            <span class="badge bg-danger">مرتجع</span>
                                        @endif
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
                        @if(!request()->filled('invoice_number'))
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            إضافة مبيعة
                        </a>
                        @endif
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

    // Custom return modal logic
    let returnUrl = '';
    let returnForm = document.getElementById('returnSaleForm');
    document.querySelectorAll('.btn-return-sale').forEach(function(btn) {
        btn.addEventListener('click', function() {
            returnUrl = this.getAttribute('data-url');
            const modal = new bootstrap.Modal(document.getElementById('returnSaleModal'));
            modal.show();
        });
    });
    if (returnForm) {
        returnForm.addEventListener('submit', function(e) {
            this.setAttribute('action', returnUrl);
        });
    }
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

<!-- Return Confirmation Modal -->
<div class="modal fade" id="returnSaleModal" tabindex="-1" aria-labelledby="returnSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="returnSaleModalLabel">
                    <i class="mdi mdi-backup-restore text-secondary fs-4 me-2"></i>
                    تأكيد الاسترجاع
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من تعيين هذه الفاتورة كمرتجع؟
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <form id="returnSaleForm" method="POST" action="#">
                    @csrf
                    <button type="submit" class="btn btn-secondary">تأكيد</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
