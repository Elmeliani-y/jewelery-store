


@extends('layouts.vertical', ['title' => 'تقرير الكل'])
@section('title','تقرير الكل')
@section('css')
@include('reports.partials.print-css')
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="mb-4 no-print">
        <h2 class="mb-1">
            <i class="mdi mdi-file-document-multiple text-primary"></i>
            تقرير شامل - المبيعات والمصروفات
        </h2>
        <p class="text-muted mb-0">عرض تفصيلي لجميع المبيعات والمصروفات مع الإحصائيات</p>
    </div>

	<!-- Summary Card: Total Price Per Gram -->
	<div class="row mb-3">
		<div class="col-md-4">
			<div class="card border-info">
				<div class="card-body text-center">
					<div class="text-muted small mb-1">سعر الجرام الإجمالي (إجمالي المبيعات / إجمالي الوزن)</div>
					@php
						$totalSales = $sales->sum('total_amount');
						$totalWeight = $sales->sum('weight');
						$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
					@endphp
					<div class="fs-4 fw-bold text-info">{{ number_format($totalPricePerGram, 2) }} <span class="fs-6">د/جرام</span></div>
				</div>
			</div>
		</div>
	</div>
    <!-- Print Title (only visible when printing) -->
    <div class="print-title" style="display: none;">
        <h2>تقرير شامل - المبيعات والمصروفات</h2>
        <p>
            التاريخ: {{ request('from', date('Y-m-01')) }} - {{ request('to', date('Y-m-d')) }}
            @if(request('branch'))
                | الفرع: {{ $branches->find(request('branch'))?->name }}
            @endif
        </p>
    </div>

    @include('reports.partials.toolbar', [
        'title' => '',
        'backUrl' => route('reports.index'),
        'exportRoute' => 'reports.all',
        'exportQuery' => request()->query(),
        'filters' => $filters ?? []
    ])

    <!-- Quick Links to Other Reports -->
    <div class="card mb-3 no-print" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: none;">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('reports.kasr') }}" class="btn btn-dark" style="background-color: #0f172a; border-color: #0f172a; min-width: 150px;">
					<i class="mdi mdi-chart-box me-1"></i> تقرير صافي الربح
                </a>
                <a href="{{ route('reports.comparative', request()->query()) }}" class="btn btn-dark" style="background-color: #0f172a; border-color: #0f172a; min-width: 150px;">
                    <i class="mdi mdi-chart-line me-1"></i> التقرير المقارن
                </a>
                <a href="{{ route('reports.speed', request()->query()) }}" class="btn btn-dark" style="background-color: #0f172a; border-color: #0f172a; min-width: 150px;">
                    <i class="mdi mdi-speedometer me-1"></i> تقرير السرعة
                </a>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.all') }}" class="card mb-4 no-print" id="filterForm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="branch" class="form-label">الفرع</label>
                    <select name="branch" id="branch" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="employee" class="form-label">الموظف</label>
                    <select name="employee" id="employee" class="form-select">
                        <option value="">كل الموظفين</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">القسم</label>
                    <select name="category" id="category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل الأقسام</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="caliber" class="form-label">العيار</label>
                    <select name="caliber" id="caliber" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل العيارات</option>
                        @foreach($calibers as $caliber)
                            <option value="{{ $caliber->id }}" {{ request('caliber') == $caliber->id ? 'selected' : '' }}>{{ $caliber->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="expense_type" class="form-label">نوع المصروف</label>
                    <select name="expense_type" id="expense_type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل أنواع المصروفات</option>
                        @foreach($expenseTypes as $type)
                            <option value="{{ $type->id }}" {{ request('expense_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">طريقة الدفع</label>
                    <select name="payment_method" id="payment_method" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">كل طرق الدفع</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="network" {{ request('payment_method') == 'network' ? 'selected' : '' }}>شبكة</option>
                        <option value="mixed" {{ request('payment_method') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sale_id" class="form-label">رقم المبيعة</label>
                    <input type="number" name="sale_id" id="sale_id" value="{{ request('sale_id') }}" class="form-control" placeholder="بحث برقم المبيعة">
                </div>
                <div class="col-md-3">
                    <label for="expense_id" class="form-label">رقم المصروف</label>
                    <input type="number" name="expense_id" id="expense_id" value="{{ request('expense_id') }}" class="form-control" placeholder="بحث برقم المصروف">
                </div>
                <div class="col-md-3">
                    <label for="from" class="form-label">من تاريخ</label>
                    <input type="date" name="from" id="from" value="{{ request('from', date('Y-m-01')) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" id="to" value="{{ request('to', date('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">تصفية</button>
                    <a href="{{ route('reports.all') }}" class="btn btn-secondary ms-2">إعادة تعيين</a>
                    <button type="button" class="btn btn-success ms-2" onclick="window.print()">طباعة A4</button>
                </div>
            </div>
        </div>
    </form>

	@php
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
	@endphp

	<div class="row">
		@if($showSales)
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
								@forelse($sales as $sale)
								@php
									$pricePerGram = $sale->price_per_gram;
									$isLow = $minGramPrice > 0 && $pricePerGram < $minGramPrice;
								@endphp
								<tr>
									<td><strong>{{ $sale->id }}</strong></td>
									<td>{{ $sale->created_at->format('Y-m-d') }}</td>
									<td>{{ $sale->branch->name ?? '-' }}</td>
									<td>{{ $sale->employee->name ?? '-' }}</td>
									<td dir="ltr">{{ number_format($sale->weight, 2) }}</td>
									<td dir="ltr">
										<span class="{{ $isLow ? 'text-danger fw-bold' : '' }}" style="{{ $isLow ? 'color: #dc3545 !important; text-decoration: underline;' : '' }}" data-bs-toggle="tooltip" title="{{ $isLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : '' }}">
											{{ number_format($pricePerGram, 2) }}
											@if($isLow)
												<i class="mdi mdi-alert-circle-outline"></i>
											@endif
										</span>
									</td>
									<td>
										@if($sale->payment_method == 'cash')
											<span class="badge bg-success">نقدي</span>
										@elseif($sale->payment_method == 'network')
											<span class="badge bg-info">شبكة</span>
										@elseif($sale->payment_method == 'mixed')
											<span class="badge bg-warning">مختلط</span>
										@else
											-
										@endif
									</td>
									<td dir="ltr"><strong>{{ number_format($sale->total_amount, 2) }}</strong></td>
									<td dir="ltr">{{ number_format($sale->tax_amount, 2) }}</td>
									<td>{{ $sale->notes ?: '-' }}</td>
								</tr>
								@empty
								<tr><td colspan="10" class="text-center text-muted">لا توجد بيانات مبيعات</td></tr>
								@endforelse
								</tbody>
								@if($sales->count())
								<tfoot>
									<tr class="fw-bold">
										<td colspan="4"></td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي الوزن</span></div>
											<div>{{ number_format($sales->sum('weight'), 2) }}</div>
										</td>
										<td dir="ltr">
											@php
												$totalSales = $sales->sum('total_amount');
												$totalWeight = $sales->sum('weight');
												$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
											@endphp
											<div><span class="text-muted small">إجمالي سعر الجرام</span></div>
											<div style="font-weight:700; color:#facc15;">{{ number_format($totalPricePerGram, 2) }}</div>
										</td>
										<td></td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المبيعات</span></div>
											<div>{{ number_format($sales->sum('total_amount'), 2) }}</div>
										</td>

										<td></td>
									</tr>
								</tfoot>
								@endif
							</table>
					</div>
					<div class="card-footer">
						{{ $sales->appends(request()->except('sales_page'))->links() }}
					</div>
				</div>
			</div>
		</div>
		@endif

		{{-- Returned Sales Table --}}
		@if(isset($returnedSales) && $returnedSales->count())
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
								@foreach($returnedSales as $sale)
								@php
									$pricePerGram = $sale->price_per_gram;
									$isLow = $minGramPrice > 0 && $pricePerGram < $minGramPrice;
								@endphp
								<tr>
									<td><strong>{{ $sale->id }}</strong></td>
									<td>{{ $sale->created_at->format('Y-m-d') }}</td>
									<td>{{ $sale->branch->name ?? '-' }}</td>
									<td>{{ $sale->employee->name ?? '-' }}</td>
									<td dir="ltr">{{ number_format($sale->weight, 2) }}</td>
									<td dir="ltr">
										<span class="{{ $isLow ? 'text-danger fw-bold' : '' }}" style="{{ $isLow ? 'color: #dc3545 !important; text-decoration: underline;' : '' }}" data-bs-toggle="tooltip" title="{{ $isLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : '' }}">
											{{ number_format($pricePerGram, 2) }}
											@if($isLow)
												<i class="mdi mdi-alert-circle-outline"></i>
											@endif
										</span>
									</td>
									<td>
										@if($sale->payment_method == 'cash')
											<span class="badge bg-success">نقدي</span>
										@elseif($sale->payment_method == 'network')
											<span class="badge bg-info">شبكة</span>
										@elseif($sale->payment_method == 'mixed')
											<span class="badge bg-warning">مختلط</span>
										@else
											-
										@endif
									</td>
									<td dir="ltr"><strong>{{ number_format($sale->total_amount, 2) }}</strong></td>
									<td dir="ltr">{{ number_format($sale->tax_amount, 2) }}</td>
									<td>{{ $sale->notes ?: '-' }}</td>
								</tr>
								@endforeach
								</tbody>
								@if($returnedSales->count())
								<tfoot>
									<tr class="fw-bold">
										<td colspan="4" class="text-end">الإجمالي</td>
										<td dir="ltr">{{ number_format($returnedSales->sum('weight'), 2) }}</td>
										<td></td>
										<td></td>
										<td dir="ltr">{{ number_format($returnedSales->sum('total_amount'), 2) }}</td>

										<td></td>
									</tr>
								</tfoot>
								@endif
							</table>
					</div>
				</div>
			</div>
		</div>
		@endif
		@if($showExpenses)
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
								@forelse($expenses as $expense)
								<tr>
									<td>{{ $expense->expense_date }}</td>
									<td>{{ $expense->branch->name ?? '-' }}</td>
									<td>{{ $expense->expenseType->name ?? '-' }}</td>
									<td dir="ltr">{{ number_format($expense->amount, 2) }}</td>
									<td>{{ $expense->notes }}</td>
								</tr>
								@empty
								<tr><td colspan="5" class="text-center text-muted">لا توجد بيانات مصروفات</td></tr>
								@endforelse
								</tbody>
								@if($expenses->count())
								<tfoot>
									<tr class="fw-bold">
										<td colspan="3" class="text-end">إجمالي المصروفات</td>
										<td dir="ltr">{{ number_format($expenses->sum('amount'), 2) }}</td>
										<td></td>
									</tr>
								</tfoot>
								@endif
							</table>
					</div>
					<div class="card-footer">
						{{ $expenses->appends(request()->except('expenses_page'))->links() }}
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>

	<div class="row">
		@if($showBranches)
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
								@forelse($branchData as $row)
								@php
									$branchPricePerGram = $row['total_weight'] > 0 ? $row['total_sales'] / $row['total_weight'] : 0;
									$isBranchLow = $minGramPrice > 0 && $branchPricePerGram > 0 && $branchPricePerGram < $minGramPrice;
								@endphp
								<tr>
									<td>{{ $row['branch']->name }}</td>
									<td>{{ $row['sales_count'] }}</td>
									<td dir="ltr">{{ number_format($row['total_sales'],2) }}</td>
									<td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
								<td dir="ltr">
									<span class="{{ $isBranchLow ? 'text-danger fw-bold' : 'text-warning fw-bold' }}" style="{{ $isBranchLow ? 'color: #dc3545 !important; text-decoration: underline;' : '' }}" data-bs-toggle="tooltip" title="{{ $isBranchLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : '' }}">
										@if($row['total_weight'] > 0)
											{{ number_format($branchPricePerGram, 2) }}
											@if($isBranchLow)
												<i class="mdi mdi-alert-circle-outline"></i>
											@endif
										@else
											-
										@endif
									</span>
								</td>
									<td dir="ltr">{{ number_format($row['total_expenses'],2) }}</td>
								</tr>
								@empty
								<tr><td colspan="7" class="text-center text-muted">لا توجد بيانات فروع</td></tr>
								@endforelse
								</tbody>
								@if($branchData->count())
								<tfoot>
									<tr class="fw-bold">
										<td></td>
										<td>
											<div><span class="text-muted small">إجمالي عدد المبيعات</span></div>
											<div>{{ $branchData->sum('sales_count') }}</div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المبيعات</span></div>
											<div>{{ number_format($branchData->sum('total_sales'),2) }}</div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي الوزن</span></div>
											<div>{{ number_format($branchData->sum('total_weight'),2) }}</div>
										</td>
										<td dir="ltr">
											@php
												$totalSales = $branchData->sum('total_sales');
												$totalWeight = $branchData->sum('total_weight');
												$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
											@endphp
											<div><span class="text-muted small">إجمالي سعر الجرام</span></div>
											<div style="font-weight:700; color:#facc15;">{{ number_format($totalPricePerGram, 2) }}</div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المصروفات</span></div>
											<div>{{ number_format($branchData->sum('total_expenses'),2) }}</div>
										</td>
									</tr>
								</tfoot>
								@endif
							</table>
					</div>
				</div>
			</div>
		</div>
		@endif
		@if($showEmployees)
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
								@forelse($employeesData as $row)
								@php
									$empPricePerGram = $row['total_weight'] > 0 ? $row['total_sales'] / $row['total_weight'] : 0;
									$isEmpLow = $minGramPrice > 0 && $empPricePerGram > 0 && $empPricePerGram < $minGramPrice;
								@endphp
								<tr>
									<td>{{ $row['employee']->name }}</td>
									<td>{{ $row['employee']->branch->name ?? '-' }}</td>
									<td>{{ $row['sales_count'] }}</td>
									<td dir="ltr">{{ number_format($row['total_sales'],2) }}</td>
									<td dir="ltr">{{ number_format($row['total_weight'],2) }}</td>
								<td dir="ltr">
									<span class="{{ $isEmpLow ? 'text-danger fw-bold' : 'text-warning fw-bold' }}" style="{{ $isEmpLow ? 'color: #dc3545 !important; text-decoration: underline;' : '' }}" data-bs-toggle="tooltip" title="{{ $isEmpLow ? 'أقل من الحد الأدنى (' . number_format($minGramPrice, 2) . ')' : '' }}">
										@if($row['total_weight'] > 0)
											{{ number_format($empPricePerGram, 2) }}
											@if($isEmpLow)
												<i class="mdi mdi-alert-circle-outline"></i>
											@endif
										@else
											-
										@endif
									</span>
								</td>

								</tr>
								@empty
								<tr><td colspan="7" class="text-center text-muted">لا توجد بيانات موظفين</td></tr>
								@endforelse
								</tbody>
								@if($employeesData->count())
								<tfoot>
									<tr class="fw-bold">
										<td></td>
										<td></td>
										<td>
											<div><span class="text-muted small">إجمالي عدد المبيعات</span></div>
											<div>{{ $employeesData->sum('sales_count') }}</div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي المبيعات</span></div>
											<div>{{ number_format($employeesData->sum('total_sales'),2) }}</div>
										</td>
										<td dir="ltr">
											<div><span class="text-muted small">إجمالي الوزن</span></div>
											<div>{{ number_format($employeesData->sum('total_weight'),2) }}</div>
										</td>
										<td dir="ltr">
											@php
												$totalSales = $employeesData->sum('total_sales');
												$totalWeight = $employeesData->sum('total_weight');
												$totalPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;
											@endphp
											<div><span class="text-muted small">إجمالي سعر الجرام</span></div>
											<div style="font-weight:700; color:#facc15;">{{ number_format($totalPricePerGram, 2) }}</div>
										</td>
									</tr>
								</tfoot>
								@endif
							</table>
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Store all employees initially
    const allEmployees = @json($employees);
    const selectedEmployee = '{{ request("employee") }}';
    
    // Filter employees by branch
    $('#branch').change(function() {
        const branchId = $(this).val();
        const employeeSelect = $('#employee');
        
        // Clear current options
        employeeSelect.html('<option value="">كل الموظفين</option>');
        
        if (branchId) {
            // Filter employees by selected branch
            $.get('{{ route("api.employees-by-branch") }}', { branch_id: branchId })
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
@endsection
