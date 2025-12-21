@extends('layouts.vertical')

@section('title') تفاصيل المبيعة @endsection

@section('css')
<style>
    .sale-header {background: linear-gradient(110deg,var(--bs-primary) 0%,var(--bs-primary) 55%,#5f6ad9 100%); color:#fff; border-radius:.9rem; padding:1.6rem 1.8rem; margin-bottom:1.5rem; position:relative; overflow:hidden;}
    .sale-header:before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 85% 15%, rgba(255,255,255,.15), transparent 70%);}    
    .sale-header h4 {font-weight:600; letter-spacing:.5px;}
    .sale-meta .badge {font-weight:500; backdrop-filter: blur(2px);}    
    .metric-card {border:1px solid var(--bs-border-color); border-radius:.75rem; padding:1rem 1rem; position:relative; background:var(--bs-body-bg); transition:.25s;}
    .metric-card:hover {box-shadow:0 .25rem .9rem rgba(0,0,0,.08); transform:translateY(-2px);}    
    .metric-icon {width:38px; height:38px; border-radius:.6rem; display:flex; align-items:center; justify-content:center; font-size:1.1rem;}
    .detail-table th {font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; opacity:.65;}
    .timeline {list-style:none; margin:0; padding:0; position:relative;}
    .timeline:before {content:''; position:absolute; top:0; bottom:0; right:14px; width:2px; background:var(--bs-border-color);}    
    .timeline-item {position:relative; padding:0 0 .9rem 0;}
    .timeline-item:last-child {padding-bottom:0;}
    .timeline-badge {position:absolute; right:6px; top:2px; width:16px; height:16px; background:var(--bs-primary); border-radius:50%; box-shadow:0 0 0 3px var(--bs-body-bg);}    
    [data-bs-theme="dark"] .sale-header {background: linear-gradient(110deg,var(--bs-primary) 0%,#4a50a8 100%);}    
    .value-lg {font-size:1.15rem; font-weight:600;}
    .text-mono {font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size:.85rem; direction:ltr;}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="sale-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div class="d-flex flex-column gap-1">
            <h4 class="mb-0"><i class="mdi mdi-file-document-outline me-1"></i> الفاتورة رقم <span class="text-mono">{{ $sale->invoice_number }}</span></h4>
            <span class="opacity-75">تفاصيل العملية ومكوناتها بشكل منسق</span>
        </div>
        <div class="sale-meta d-flex flex-wrap gap-2">
            <span class="badge bg-primary-subtle text-primary"><i class="mdi mdi-storefront-outline me-1"></i> {{ $sale->branch->name }}</span>
            <span class="badge bg-info-subtle text-info"><i class="mdi mdi-account-outline me-1"></i> {{ $sale->employee->name }}</span>
            @if($sale->products && count($sale->products) > 0)
            <span class="badge bg-success-subtle text-success"><i class="mdi mdi-package-variant-closed me-1"></i> {{ count($sale->products) }} منتج</span>
            @endif
            @if($sale->is_returned)
            <span class="badge bg-danger"><i class="mdi mdi-backup-restore me-1"></i> مرتجع</span>
            @else
            <span class="badge bg-secondary"><i class="mdi mdi-cash me-1"></i> عملية بيع</span>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="row g-3 mb-1">
                <div class="col-6 col-md-3">
                    <div class="metric-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-1"><span class="text-muted small">الوزن</span><span class="metric-icon bg-primary-subtle text-primary"><i class="mdi mdi-scale"></i></span></div>
                        <div class="value-lg">{{ number_format($sale->weight,2) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-1"><span class="text-muted small">المبلغ</span><span class="metric-icon bg-success-subtle text-success"><i class="mdi mdi-cash"></i></span></div>
                        <div class="value-lg text-mono" dir="ltr">{{ number_format($sale->total_amount,2) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-1"><span class="text-muted small">تاريخ الإنشاء</span><span class="metric-icon bg-info-subtle text-info"><i class="mdi mdi-clock-outline"></i></span></div>
                        <div class="value-lg">{{ $sale->created_at->format('Y-m-d') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-1"><span class="text-muted small">آخر تحديث</span><span class="metric-icon bg-warning-subtle text-warning"><i class="mdi mdi-history"></i></span></div>
                        <div class="value-lg">{{ $sale->updated_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($sale->products && count($sale->products) > 0)
            <div class="card mb-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0"><i class="mdi mdi-package-variant-closed me-1"></i> المنتجات ({{ count($sale->products) }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الفئة</th>
                                    <th>العيار</th>
                                    <th>الوزن</th>
                                    <th>المبلغ</th>
                                    <th>الضريبة</th>
                                    <th>الصافي</th>
                                </tr>
                            </thead>
                            <tbody class="fs-13">
                                @foreach($sale->products as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product['category_name'] ?? '' }}</td>
                                    <td>{{ $product['caliber_name'] ?? '' }}</td>
                                    <td>{{ number_format($product['weight'], 2) }}</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($product['amount'], 2) }}</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($product['tax_amount'], 0, ',', '.') }}</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($product['net_amount'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-secondary fw-semibold">
                                    <td colspan="3" class="text-end">المجموع:</td>
                                    <td>{{ number_format($sale->weight, 2) }}</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($sale->net_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">المعلومات التفصيلية</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 detail-table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الحقل</th>
                                    <th>القيمة</th>
                                </tr>
                            </thead>
                            <tbody class="fs-13">
                                <tr>
                                    <td class="text-muted">رقم الفاتورة</td>
                                    <td class="fw-semibold text-mono" dir="ltr">{{ $sale->id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">الفرع</td>
                                    <td>{{ $sale->branch->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">الموظف</td>
                                    <td>{{ $sale->employee->name }}</td>
                                </tr>
                                @if($sale->caliber)
                                <tr>
                                    <td class="text-muted">العيار</td>
                                    <td>{{ $sale->caliber->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">الوزن</td>
                                    <td>{{ number_format($sale->weight,2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">المبلغ</td>
                                    <td class="text-mono" dir="ltr">{{ number_format($sale->total_amount,2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">طريقة الدفع</td>
                                    <td>
                                        @if($sale->payment_method === 'cash')
                                            <span class="badge bg-success-subtle text-success"><i class="mdi mdi-cash me-1"></i>نقداً</span>
                                        @elseif($sale->payment_method === 'network')
                                            <span class="badge bg-info-subtle text-info"><i class="mdi mdi-credit-card-outline me-1"></i>شبكة</span>
                                        @elseif($sale->payment_method === 'transfer')
                                            <span class="badge bg-primary-subtle text-primary"><i class="mdi mdi-bank-transfer me-1"></i>تحويل</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning"><i class="mdi mdi-swap-horizontal me-1"></i>مختلط (نقدي + شبكة)</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($sale->payment_method === 'mixed')
                                <tr class="table-active">
                                    <td colspan="2" class="text-center fw-semibold">
                                        <i class="mdi mdi-information-outline me-1"></i>تفاصيل الدفع المختلط
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">
                                        <i class="mdi mdi-cash text-success me-1"></i>المبلغ النقدي
                                    </td>
                                    <td class="text-mono fw-semibold text-success" dir="ltr">{{ number_format($sale->cash_amount,2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">
                                        <i class="mdi mdi-credit-card-outline text-info me-1"></i>مبلغ الشبكة
                                    </td>
                                    <td class="text-mono fw-semibold text-info" dir="ltr">{{ number_format($sale->network_amount,2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-4">
                                        <i class="mdi mdi-calculator me-1"></i>المجموع
                                    </td>
                                    <td class="text-mono fw-bold" dir="ltr">{{ number_format($sale->cash_amount + $sale->network_amount,2) }}</td>
                                </tr>
                                @elseif($sale->payment_method === 'cash')
                                <tr>
                                    <td class="text-muted">المبلغ النقدي</td>
                                    <td class="text-mono fw-semibold text-success" dir="ltr">{{ number_format($sale->cash_amount,2) }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td class="text-muted">مبلغ الشبكة</td>
                                    <td class="text-mono fw-semibold text-info" dir="ltr">{{ number_format($sale->network_amount,2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">تاريخ الإنشاء</td>
                                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">آخر تحديث</td>
                                    <td>{{ $sale->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">حالة استلام العميل</td>
                                    <td>
                                        @if($sale->customer_received)
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="mdi mdi-check-circle me-1"></i>استلم الفاتورة
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">
                                                <i class="mdi mdi-clock-outline me-1"></i>لم يستلم بعد
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">سجل الحالة</h6>
                </div>
                <div class="card-body">
                    <ul class="timeline mb-0">
                        <li class="timeline-item">
                            <span class="timeline-badge"></span>
                            <div class="ms-4">
                                <div class="fw-semibold">تم إنشاء المبيعة</div>
                                <small class="text-muted">{{ $sale->created_at->diffForHumans() }} • {{ $sale->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-badge bg-warning"></span>
                            <div class="ms-4">
                                <div class="fw-semibold">آخر تعديل</div>
                                <small class="text-muted">{{ $sale->updated_at->diffForHumans() }} • {{ $sale->updated_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">إجراءات</h6>
                </div>
                <div class="card-body d-flex flex-wrap gap-2">
                    @if(!auth()->user()->isBranch())
                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil-outline me-1"></i> تعديل</a>
                        <button type="button" class="btn btn-danger btn-sm btn-delete-sale" 
                            data-url="{{ route('sales.destroy', $sale) }}"
                            data-invoice="{{ $sale->invoice_number }}">
                            <i class="mdi mdi-delete-outline me-1"></i> حذف
                        </button>
                    @endif
                    @if(auth()->user()->isBranch())
                        <a href="{{ url('/branch/daily-sales') }}" class="btn btn-secondary btn-sm"><i class="mdi mdi-arrow-left me-1"></i> رجوع للقائمة اليومية</a>
                    @else
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm"><i class="mdi mdi-arrow-left me-1"></i> رجوع للقائمة</a>
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
