<?php $__env->startSection('title'); ?> قائمة المبيعات <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
                        <?php if(auth()->user()->isBranch()): ?>
                            <a href="<?php echo e(url('/branch/daily-sales')); ?>" class="btn btn-outline-primary">
                                <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة اليومية
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('t6u1v5w8.index')); ?>" class="btn btn-outline-primary">
                                <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة
                            </a>
                        <?php endif; ?>
                    </div>
                <div class="d-flex justify-content-end mb-3">
                    <?php if(auth()->user()->isBranch()): ?>
                        <a href="<?php echo e(url('/branch/daily-sales')); ?>" class="btn btn-outline-primary">
                            <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة اليومية
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('t6u1v5w8.index')); ?>" class="btn btn-outline-primary">
                            <i class="mdi mdi-arrow-left"></i> رجوع إلى القائمة
                        </a>
                    <?php endif; ?>
                </div>
            <?php if(!request()->filled('invoice_number')): ?>
            <div class="col-auto">
                <?php if(auth()->user()->isBranch()): ?>
                    <a href="<?php echo e(url('/branch/daily-sales')); ?>" class="btn btn-light">
                        <i class="ri-add-line me-1"></i>
                        إضافة مبيعة جديدة
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('t6u1v5w8.create')); ?>" class="btn btn-light">
                        <i class="ri-add-line me-1"></i>
                        إضافة مبيعة جديدة
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters: Branch, Dates, Invoice Number -->
    <form method="GET" class="mb-3 row g-2 align-items-end">
        <div class="col-md-2">
            <label for="branch_id" class="form-label mb-1">الفرع</label>
            <select name="branch_id" id="branch_id" class="form-select">
                <option value="">كل الفروع</option>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id', '') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label mb-1">من تاريخ</label>
            <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request('date_from', now()->format('Y-m-d'))); ?>">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request('date_to', now()->format('Y-m-d'))); ?>">
        </div>
        <div class="col-md-2">
            <label for="invoice_number" class="form-label mb-1">رقم الفاتورة</label>
            <input type="number" name="invoice_number" id="invoice_number" class="form-control" placeholder="بحث برقم الفاتورة" value="<?php echo e(request('invoice_number')); ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit" style="margin-top: 2px;">بحث</button>
        </div>
    </form>

    <!-- Sales List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <?php if($sales->count() > 0): ?>
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
                                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary"><?php echo e($sale->id); ?></span>
                                    </td>
                                    <td class="text-muted"><?php echo e($sale->branch->name); ?></td>
                                    <td class="text-muted"><?php echo e($sale->employee->name); ?></td>
                                    <td class="text-muted"><?php echo e(number_format($sale->weight, 2)); ?></td>
                                    <td dir="ltr">
                                        <?php
                                            $pricePerGram = $sale->weight > 0 ? $sale->total_amount / $sale->weight : 0;
                                            $isLowPrice = $minGramPrice > 0 && $pricePerGram < $minGramPrice;
                                        ?>
                                        <span class="fw-semibold <?php echo e($isLowPrice ? 'text-danger' : 'text-dark'); ?>">
                                            <?php echo e(number_format($pricePerGram, 2)); ?>

                                            <?php if($isLowPrice): ?>
                                                <i class="mdi mdi-alert-circle-outline" data-bs-toggle="tooltip" title="أقل من الحد الأدنى (<?php echo e(number_format($minGramPrice, 2)); ?>)"></i>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td dir="ltr">
                                        <span class="fw-semibold text-dark"><?php echo e(number_format($sale->total_amount, 0, ',', '.')); ?> <small class="text-muted">ريال</small></span>
                                    </td>
                                    <td class="text-muted"><?php echo e($sale->created_at->format('Y-m-d')); ?></td>
                                    <td class="text-center sales-table-actions">
                                        <a href="<?php echo e(route('t6u1v5w8.show', $sale)); ?>" class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip" data-bs-original-title="عرض">
                                            <i class="mdi mdi-eye-outline text-info fs-16"></i>
                                        </a>
                                        <a href="<?php echo e(route('t6u1v5w8.edit', $sale)); ?>" class="btn btn-icon btn-sm bg-warning-subtle" data-bs-toggle="tooltip" data-bs-original-title="تعديل">
                                            <i class="mdi mdi-pencil-outline text-warning fs-16"></i>
                                        </a>
                                        <?php if(!auth()->user()->isBranch()): ?>
                                            <button type="button" class="btn btn-icon btn-sm bg-danger-subtle btn-delete-sale" 
                                                    data-url="<?php echo e(route('t6u1v5w8.destroy', $sale)); ?>" 
                                                    data-invoice="<?php echo e($sale->invoice_number); ?>"
                                                    data-bs-toggle="tooltip" data-bs-original-title="حذف">
                                                <i class="mdi mdi-delete-outline text-danger fs-16"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if(!$sale->is_returned): ?>
                                            <?php if(!auth()->user()->isBranch()): ?>
                                                <button type="button" class="btn btn-icon btn-sm bg-secondary-subtle ms-1 btn-return-sale" data-sale-id="<?php echo e($sale->id); ?>" data-url="<?php echo e(route('t6u1v5w8.x2y7z3a9', $sale)); ?>" data-bs-toggle="tooltip" data-bs-original-title="تعيين كمرتجع">
                                                    <i class="mdi mdi-backup-restore text-secondary fs-16"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-danger">مرتجع</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <?php
                                $ijmaliWazn = $sales->sum('weight');
                                $ijmaliCount = $sales->count();
                                $ijmaliMabalegh = $sales->sum('total_amount');
                            ?>
                            <tfoot class="table-light">
                                <tr class="fw-semibold">
                                    <td colspan="3">الإجماليات</td>
                                    <td class="text-end"><?php echo e(number_format($ijmaliWazn, 2)); ?></td>
                                    <td></td>
                                    <td class="text-end"><?php echo e(number_format($ijmaliMabalegh, 2)); ?></td>
                                    <td class="text-end">عدد المبيعات: <?php echo e($ijmaliCount); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                            <small class="text-muted">
                                عرض <?php echo e($sales->firstItem()); ?>–<?php echo e($sales->lastItem()); ?> من أصل <?php echo e($sales->total()); ?>

                            </small>
                        </div>
                        <?php echo e($sales->links('pagination::bootstrap-5')); ?>

                    </div>
                    <?php else: ?>
                    <div class="py-5 text-center">
                        <i class="mdi mdi-shopping-outline" style="font-size:3.5rem; color:#adb5bd;"></i>
                        <h5 class="text-muted mt-3">لا توجد مبيعات</h5>
                        <p class="text-muted">قم بإضافة مبيعة جديدة للبدء</p>
                        <?php if(!request()->filled('invoice_number')): ?>
                        <a href="<?php echo e(route('t6u1v5w8.create')); ?>" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            إضافة مبيعة
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
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
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
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
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary">تأكيد</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'قائمة المبيعات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/sales/index.blade.php ENDPATH**/ ?>