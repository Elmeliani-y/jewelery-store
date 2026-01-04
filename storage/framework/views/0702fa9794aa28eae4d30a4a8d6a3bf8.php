<?php $__env->startSection('title','تقرير الكل'); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('reports.partials.print-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Title -->
    <div class="mb-4 no-print">
        <h2 class="mb-1">
            <i class="mdi mdi-file-document-multiple text-primary"></i>
            تقرير شامل - المبيعات والمصروفات
        </h2>
        <p class="text-muted mb-0">عرض تفصيلي لجميع المبيعات والمصروفات مع الإحصائيات</p>
    </div>

    <!-- Print Title (only visible when printing) -->
    <div class="print-title" style="display: none;">
        <h2>تقرير شامل - المبيعات والمصروفات</h2>
        <p>
            التاريخ: <?php echo e(request('from', date('Y-m-01'))); ?> - <?php echo e(request('to', date('Y-m-d'))); ?>

            <?php if(request('branch')): ?>
                | الفرع: <?php echo e($branches->find(request('branch'))?->name); ?>

            <?php endif; ?>
        </p>
    </div>

    <?php echo $__env->make('reports.partials.toolbar', [
        'title' => '',
        'backUrl' => route('t3u8v1w4.b1c5d8e3'),
        'exportRoute' => 'reports.all',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Quick Links to Other Reports -->
    <div class="card mb-3 no-print" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: none;">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="<?php echo e(route('t3u8v1w4.t6u2v8w5')); ?>" class="btn btn-dark" style="background-color: #0f172a; border-color: #0f172a; min-width: 150px;">
					<i class="mdi mdi-chart-box me-1"></i> تقرير صافي الربح
                </a>
                <a href="<?php echo e(route('t3u8v1w4.l3m8n2o6', request()->query())); ?>" class="btn btn-dark" style="background-color: #0f172a; border-color: #0f172a; min-width: 150px;">
                    <i class="mdi mdi-chart-line me-1"></i> التقرير المقارن
                </a>
                <a href="<?php echo e(route('t3u8v1w4.f4g9h2i7', request()->query())); ?>" class="btn btn-dark" style="background-color: #0f172a; border-color: #0f172a; min-width: 150px;">
                    <i class="mdi mdi-speedometer me-1"></i> تقرير السرعة
                </a>
            </div>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('t3u8v1w4.b1c5d8e3')); ?>" class="card mb-4 no-print" id="filterForm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="branch" class="form-label">الفرع</label>
                    <select name="branch" id="branch" class="form-select">
                        <option value="">كل الفروع</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="employee" class="form-label">الموظف</label>
                    <select name="employee" id="employee" class="form-select">
                        <option value="">كل الموظفين</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($employee->id); ?>" <?php echo e(request('employee') == $employee->id ? 'selected' : ''); ?>><?php echo e($employee->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">القسم</label>
                    <select name="category" id="category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل الأقسام</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="caliber" class="form-label">العيار</label>
                    <select name="caliber" id="caliber" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل العيارات</option>
                        <?php $__currentLoopData = $calibers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caliber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($caliber->id); ?>" <?php echo e(request('caliber') == $caliber->id ? 'selected' : ''); ?>><?php echo e($caliber->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="expense_type" class="form-label">نوع المصروف</label>
                    <select name="expense_type" id="expense_type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل أنواع المصروفات</option>
                        <?php $__currentLoopData = $expenseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>" <?php echo e(request('expense_type') == $type->id ? 'selected' : ''); ?>><?php echo e($type->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">طريقة الدفع</label>
                    <select name="payment_method" id="payment_method" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل طرق الدفع</option>
                        <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>نقدي</option>
                        <option value="network" <?php echo e(request('payment_method') == 'network' ? 'selected' : ''); ?>>شبكة</option>
                        <option value="mixed" <?php echo e(request('payment_method') == 'mixed' ? 'selected' : ''); ?>>مختلط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sale_id" class="form-label">رقم المبيعة</label>
                    <input type="number" name="sale_id" id="sale_id" value="<?php echo e(request('sale_id')); ?>" class="form-control" placeholder="بحث برقم المبيعة">
                </div>
                <div class="col-md-3">
                    <label for="expense_id" class="form-label">رقم المصروف</label>
                    <input type="number" name="expense_id" id="expense_id" value="<?php echo e(request('expense_id')); ?>" class="form-control" placeholder="بحث برقم المصروف">
                </div>
                <div class="col-md-3">
                    <label for="from" class="form-label">من تاريخ</label>
                    <input type="date" name="from" id="from" value="<?php echo e(request('from', date('Y-m-01'))); ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" id="to" value="<?php echo e(request('to', date('Y-m-d'))); ?>" class="form-control">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">تصفية</button>
                    <a href="<?php echo e(route('t3u8v1w4.b1c5d8e3')); ?>" class="btn btn-secondary ms-2">إعادة تعيين</a>
                    <button type="button" class="btn btn-success ms-2" onclick="window.print()">طباعة A4</button>
                </div>
            </div>
        </div>
    </form>

	<?php
		// Determine which tables to show based on filters
		$hasSaleIdFilter = request()->filled('sale_id');
		$hasExpenseIdFilter = request()->filled('expense_id');
		$hasEmployeeFilter = request()->filled('employee');
		$hasBranchFilter = request()->filled('branch');
		$hasCategoryFilter = request()->filled('category');
		$hasCaliberFilter = request()->filled('caliber');
		$hasPaymentMethodFilter = request()->filled('payment_method');
		$hasExpenseTypeFilter = request()->filled('expense_type');
		$hasDateFilter = request()->filled('from') || request()->filled('to');
		
		// If sale_id is provided, show ONLY sales table
		if ($hasSaleIdFilter) {
			$showSales = true;
			$showExpenses = false;
			$showBranches = false;
			$showEmployees = false;
		}
		// If expense_id is provided, show ONLY expenses table
		elseif ($hasExpenseIdFilter) {
			$showSales = false;
			$showExpenses = true;
			$showBranches = false;
			$showEmployees = false;
		}
		// If branch is selected, show ALL tables with branch data
		elseif ($hasBranchFilter) {
			$showSales = true;
			$showExpenses = true;
			$showBranches = true;
			$showEmployees = true;
		}
		// Check if ANY other filter is applied
		elseif ($hasEmployeeFilter || $hasCategoryFilter || $hasCaliberFilter || $hasPaymentMethodFilter || $hasExpenseTypeFilter || $hasDateFilter) {
			// Show sales if no expense-specific filters OR if employee/category/caliber/payment filters are set
			$showSales = !$hasExpenseTypeFilter || $hasEmployeeFilter || $hasCategoryFilter || $hasCaliberFilter || $hasPaymentMethodFilter;
			
			// Show expenses only if expense type filter is selected OR if no sales-specific filters
			$showExpenses = $hasExpenseTypeFilter || (!$hasEmployeeFilter && !$hasCategoryFilter && !$hasCaliberFilter && !$hasPaymentMethodFilter);
			
			// Show branches table if no specific filters
			$showBranches = false;
			
			// Show employees table only if employee filter is selected
			$showEmployees = $hasEmployeeFilter;
		}
		// If no filters at all, show all tables
		else {
			$showSales = true;
			$showExpenses = true;
			$showBranches = true;
			$showEmployees = true;
		}
	?>

	<div class="row">
		<?php if($showSales): ?>
		<div class="col-lg-6 mb-4">
			<div class="card shadow-sm">
				<div class="card-header bg-primary text-white">تقرير المبيعات</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-sm mb-0">
							<thead>
								<tr>
									<th>#</th>
									<th>التاريخ</th>
									<th>الفرع</th>
									<th>الموظف</th>
									<th>الوزن (جم)</th>
									<th>سعر الجرام</th>
									<th>طريقة الدفع</th>
									<th>المبلغ</th>
									<th>الضريبة</th>
									<th>ملاحظات</th>
								</tr>
							</thead>
							<tbody>
								<?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<?php
									$pricePerGram = $sale->price_per_gram;
									$isLow = $minGramPrice > 0 && $pricePerGram < $minGramPrice;
								?>
								<tr>
									<td><strong><?php echo e($sale->id); ?></strong></td>
									<td><?php echo e($sale->created_at->format('Y-m-d')); ?></td>
									<td><?php echo e($sale->branch->name ?? '-'); ?></td>
									<td><?php echo e($sale->employee->name ?? '-'); ?></td>
									<td dir="ltr"><?php echo e(number_format($sale->weight, 2)); ?></td>
									<td dir="ltr">
										<span class="<?php echo e($isLow ? 'text-danger fw-bold' : ''); ?>" style="<?php echo e($isLow ? 'color: #dc3545 !important; text-decoration: underline;' : ''); ?>" data-bs-toggle="tooltip" title="<?php echo e($isLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : ''); ?>">
											<?php echo e(number_format($pricePerGram, 2)); ?>

											<?php if($isLow): ?>
												<i class="mdi mdi-alert-circle-outline"></i>
											<?php endif; ?>
										</span>
									</td>
									<td>
										<?php if($sale->payment_method == 'cash'): ?>
											<span class="badge bg-success">نقدي</span>
										<?php elseif($sale->payment_method == 'network'): ?>
											<span class="badge bg-info">شبكة</span>
										<?php elseif($sale->payment_method == 'mixed'): ?>
											<span class="badge bg-warning">مختلط</span>
										<?php else: ?>
											-
										<?php endif; ?>
									</td>
									<td dir="ltr"><strong><?php echo e(number_format($sale->total_amount, 2)); ?></strong></td>
									<td dir="ltr"><?php echo e(number_format($sale->tax_amount, 2)); ?></td>
									<td><?php echo e($sale->notes ?: '-'); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr><td colspan="10" class="text-center text-muted">لا توجد بيانات مبيعات</td></tr>
								<?php endif; ?>
								</tbody>
								<?php if($sales->count()): ?>
								<tfoot>
									<tr class="fw-bold">
										<td colspan="4"></td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي الوزن</span></div>
											<div><?php echo e(number_format($sales->sum('weight'), 2)); ?></div>
										</td>
										<td dir="ltr">
											<?php
												$totalSales = $sales->sum('total_amount');
												$totalWeight = $sales->sum('weight');
												$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
											?>
											<div><span class="text-muted small">إجمالي سعر الجرام</span></div>
											<div style="font-weight:700; color:#facc15;"><?php echo e(number_format($totalPricePerGram, 2)); ?></div>
										</td>
										<td></td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المبيعات</span></div>
											<div><?php echo e(number_format($sales->sum('total_amount'), 2)); ?></div>
										</td>

										<td></td>
									</tr>
								</tfoot>
								<?php endif; ?>
							</table>
					</div>
					<div class="card-footer">
						<?php echo e($sales->appends(request()->except('sales_page'))->links()); ?>

					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		
		<?php if(isset($returnedSales) && $returnedSales->count()): ?>
		<div class="col-lg-6 mb-4">
			<div class="card shadow-sm">
				<div class="card-header bg-danger text-white">تقرير المبيعات المرتجعة</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-sm mb-0">
							<thead>
								<tr>
									<th>#</th>
									<th>التاريخ</th>
									<th>الفرع</th>
									<th>الموظف</th>
									<th>الوزن (جم)</th>
									<th>سعر الجرام</th>
									<th>طريقة الدفع</th>
									<th>المبلغ</th>
									<th>الضريبة</th>
									<th>ملاحظات</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $returnedSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
									$pricePerGram = $sale->price_per_gram;
									$isLow = $minGramPrice > 0 && $pricePerGram < $minGramPrice;
								?>
								<tr>
									<td><strong><?php echo e($sale->id); ?></strong></td>
									<td><?php echo e($sale->created_at->format('Y-m-d')); ?></td>
									<td><?php echo e($sale->branch->name ?? '-'); ?></td>
									<td><?php echo e($sale->employee->name ?? '-'); ?></td>
									<td dir="ltr"><?php echo e(number_format($sale->weight, 2)); ?></td>
									<td dir="ltr">
										<span class="<?php echo e($isLow ? 'text-danger fw-bold' : ''); ?>" style="<?php echo e($isLow ? 'color: #dc3545 !important; text-decoration: underline;' : ''); ?>" data-bs-toggle="tooltip" title="<?php echo e($isLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : ''); ?>">
											<?php echo e(number_format($pricePerGram, 2)); ?>

											<?php if($isLow): ?>
												<i class="mdi mdi-alert-circle-outline"></i>
											<?php endif; ?>
										</span>
									</td>
									<td>
										<?php if($sale->payment_method == 'cash'): ?>
											<span class="badge bg-success">نقدي</span>
										<?php elseif($sale->payment_method == 'network'): ?>
											<span class="badge bg-info">شبكة</span>
										<?php elseif($sale->payment_method == 'mixed'): ?>
											<span class="badge bg-warning">مختلط</span>
										<?php else: ?>
											-
										<?php endif; ?>
									</td>
									<td dir="ltr"><strong><?php echo e(number_format($sale->total_amount, 2)); ?></strong></td>
									<td dir="ltr"><?php echo e(number_format($sale->tax_amount, 2)); ?></td>
									<td><?php echo e($sale->notes ?: '-'); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
								<?php if($returnedSales->count()): ?>
								<tfoot>
									<tr class="fw-bold">
										<td colspan="4" class="text-end">الإجمالي</td>
										<td dir="ltr"><?php echo e(number_format($returnedSales->sum('weight'), 2)); ?></td>
										<td></td>
										<td></td>
										<td dir="ltr"><?php echo e(number_format($returnedSales->sum('total_amount'), 2)); ?></td>

										<td></td>
									</tr>
								</tfoot>
								<?php endif; ?>
							</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if($showExpenses): ?>
		<div class="col-lg-6 mb-4">
			<div class="card shadow-sm">
				<div class="card-header bg-success text-white">تقرير المصروفات</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-sm mb-0">
							<thead>
								<tr>
									<th>التاريخ</th>
									<th>الفرع</th>
									<th>نوع المصروف</th>
									<th>المبلغ</th>
									<th>ملاحظات</th>
								</tr>
							</thead>
							<tbody>
								<?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<tr>
									<td><?php echo e($expense->expense_date); ?></td>
									<td><?php echo e($expense->branch->name ?? '-'); ?></td>
									<td><?php echo e($expense->expenseType->name ?? '-'); ?></td>
									<td dir="ltr"><?php echo e(number_format($expense->amount, 2)); ?></td>
									<td><?php echo e($expense->notes); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr><td colspan="5" class="text-center text-muted">لا توجد بيانات مصروفات</td></tr>
								<?php endif; ?>
								</tbody>
								<?php if($expenses->count()): ?>
								<tfoot>
									<tr class="fw-bold">
										<td colspan="3" class="text-end">إجمالي المصروفات</td>
										<td dir="ltr"><?php echo e(number_format($expenses->sum('amount'), 2)); ?></td>
										<td></td>
									</tr>
								</tfoot>
								<?php endif; ?>
							</table>
					</div>
					<div class="card-footer">
						<?php echo e($expenses->appends(request()->except('expenses_page'))->links()); ?>

					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<div class="row">
		<?php if($showBranches): ?>
		<div class="col-lg-6 mb-4">
			<div class="card shadow-sm">
				<div class="card-header bg-info text-white">تقرير الفروع</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-sm mb-0">
							<thead>
								<tr>
									<th>الفرع</th>
									<th>عدد المبيعات</th>
									<th>إجمالي المبيعات</th>
									<th>إجمالي الوزن</th>
									<th>سعر الجرام</th>
									<th>إجمالي المصروفات</th>
								</tr>
							</thead>
							<tbody>
								<?php $__empty_1 = true; $__currentLoopData = $branchData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<?php
									$branchPricePerGram = $row['total_weight'] > 0 ? $row['total_sales'] / $row['total_weight'] : 0;
									$isBranchLow = $minGramPrice > 0 && $branchPricePerGram > 0 && $branchPricePerGram < $minGramPrice;
								?>
								<tr>
									<td><?php echo e($row['branch']->name); ?></td>
									<td><?php echo e($row['sales_count']); ?></td>
									<td dir="ltr"><?php echo e(number_format($row['total_sales'],2)); ?></td>
									<td dir="ltr"><?php echo e(number_format($row['total_weight'],2)); ?></td>
								<td dir="ltr">
									<span class="<?php echo e($isBranchLow ? 'text-danger fw-bold' : 'text-warning fw-bold'); ?>" style="<?php echo e($isBranchLow ? 'color: #dc3545 !important; text-decoration: underline;' : ''); ?>" data-bs-toggle="tooltip" title="<?php echo e($isBranchLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : ''); ?>">
										<?php if($row['total_weight'] > 0): ?>
											<?php echo e(number_format($branchPricePerGram, 2)); ?>

											<?php if($isBranchLow): ?>
												<i class="mdi mdi-alert-circle-outline"></i>
											<?php endif; ?>
										<?php else: ?>
											-
										<?php endif; ?>
									</span>
								</td>
									<td dir="ltr"><?php echo e(number_format($row['total_expenses'],2)); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr><td colspan="7" class="text-center text-muted">لا توجد بيانات فروع</td></tr>
								<?php endif; ?>
								</tbody>
								<?php if($branchData->count()): ?>
								<tfoot>
									<tr class="fw-bold">
										<td></td>
										<td>
											<div><span class="text-muted small">إجمالي عدد المبيعات</span></div>
											<div><?php echo e($branchData->sum('sales_count')); ?></div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المبيعات</span></div>
											<div><?php echo e(number_format($branchData->sum('total_sales'),2)); ?></div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي الوزن</span></div>
											<div><?php echo e(number_format($branchData->sum('total_weight'),2)); ?></div>
										</td>
										<td dir="ltr">
											<?php
												$totalSales = $branchData->sum('total_sales');
												$totalWeight = $branchData->sum('total_weight');
												$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
											?>
											<div><span class="text-muted small">إجمالي سعر الجرام</span></div>
											<div style="font-weight:700; color:#facc15;"><?php echo e(number_format($totalPricePerGram, 2)); ?></div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المصروفات</span></div>
											<div><?php echo e(number_format($branchData->sum('total_expenses'),2)); ?></div>
										</td>
									</tr>
								</tfoot>
								<?php endif; ?>
							</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if($showEmployees): ?>
		<div class="col-lg-6 mb-4">
			<div class="card shadow-sm">
				<div class="card-header bg-warning text-dark">تقرير الموظفين</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-sm mb-0">
							<thead>
								<tr>
									<th>الموظف</th>
									<th>الفرع</th>
									<th>عدد المبيعات</th>
									<th>إجمالي المبيعات</th>
									<th>إجمالي الوزن</th>
									<th>سعر الجرام</th>
								</tr>
							</thead>
							<tbody>
								<?php $__empty_1 = true; $__currentLoopData = $employeesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
								<?php
									$empPricePerGram = $row['total_weight'] > 0 ? $row['total_sales'] / $row['total_weight'] : 0;
									$isEmpLow = $minGramPrice > 0 && $empPricePerGram > 0 && $empPricePerGram < $minGramPrice;
								?>
								<tr>
									<td><?php echo e($row['employee']->name); ?></td>
									<td><?php echo e($row['employee']->branch->name ?? '-'); ?></td>
									<td><?php echo e($row['sales_count']); ?></td>
									<td dir="ltr"><?php echo e(number_format($row['total_sales'],2)); ?></td>
									<td dir="ltr"><?php echo e(number_format($row['total_weight'],2)); ?></td>
								<td dir="ltr">
									<span class="<?php echo e($isEmpLow ? 'text-danger fw-bold' : 'text-warning fw-bold'); ?>" style="<?php echo e($isEmpLow ? 'color: #dc3545 !important; text-decoration: underline;' : ''); ?>" data-bs-toggle="tooltip" title="<?php echo e($isEmpLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : ''); ?>">
										<?php if($row['total_weight'] > 0): ?>
											<?php echo e(number_format($empPricePerGram, 2)); ?>

											<?php if($isEmpLow): ?>
												<i class="mdi mdi-alert-circle-outline"></i>
											<?php endif; ?>
										<?php else: ?>
											-
										<?php endif; ?>
									</span>
								</td>

								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
								<tr><td colspan="7" class="text-center text-muted">لا توجد بيانات موظفين</td></tr>
								<?php endif; ?>
								</tbody>
								<?php if($employeesData->count()): ?>
								<tfoot>
									<tr class="fw-bold">
										<td></td>
										<td></td>
										<td>
											<div><span class="text-muted small">إجمالي عدد المبيعات</span></div>
											<div><?php echo e($employeesData->sum('sales_count')); ?></div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المبيعات</span></div>
											<div><?php echo e(number_format($employeesData->sum('total_sales'),2)); ?></div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي الوزن</span></div>
											<div><?php echo e(number_format($employeesData->sum('total_weight'),2)); ?></div>
										</td>
										<td dir="ltr">
											<?php
												$totalSales = $employeesData->sum('total_sales');
												$totalWeight = $employeesData->sum('total_weight');
												$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
											?>
											<div><span class="text-muted small">إجمالي سعر الجرام</span></div>
											<div style="font-weight:700; color:#facc15;"><?php echo e(number_format($totalPricePerGram, 2)); ?></div>
										</td>
									</tr>
								</tfoot>
								<?php endif; ?>
							</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Store all employees initially
    const allEmployees = <?php echo json_encode($employees, 15, 512) ?>;
    const selectedEmployee = '<?php echo e(request("employee")); ?>';
    
    // Filter employees by branch
    $('#branch').change(function() {
        const branchId = $(this).val();
        const employeeSelect = $('#employee');
        
        // Clear current options
        employeeSelect.html('<option value="">كل الموظفين</option>');
        
        if (branchId) {
            // Filter employees by selected branch
            $.get('<?php echo e(route("f9g2h6i3.j7k1l4m8")); ?>', { branch_id: branchId })
                .done(function(data) {
                    data.forEach(function(employee) {
                        const selected = selectedEmployee == employee.id ? 'selected' : '';
                        employeeSelect.append(`<option value="${employee.id}" ${selected}>${employee.name}</option>`);
                    });
                })
                .fail(function() {
                    // If API fails, filter from allEmployees array
                    allEmployees.forEach(function(employee) {
                        if (employee.branch_id == branchId) {
                            const selected = selectedEmployee == employee.id ? 'selected' : '';
                            employeeSelect.append(`<option value="${employee.id}" ${selected}>${employee.name}</option>`);
                        }
                    });
                });
        } else {
            // Show all employees
            allEmployees.forEach(function(employee) {
                const selected = selectedEmployee == employee.id ? 'selected' : '';
                employeeSelect.append(`<option value="${employee.id}" ${selected}>${employee.name}</option>`);
            });
        }
    });
    
    // Trigger change if branch is already selected on page load
    const selectedBranch = $('#branch').val();
    if (selectedBranch) {
        $('#branch').trigger('change');
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'تقرير الكل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/reports/all.blade.php ENDPATH**/ ?>