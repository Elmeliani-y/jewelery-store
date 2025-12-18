@extends('layouts.vertical', ['title' => 'تقرير صافي الربح'])
@section('title', 'تقرير صافي الربح')

@section('css')
<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            background: #fff !important;
        }
        .kasr-filters-form, .kasr-filters-form * {
            display: none !important;
        }
        .kasr-receipt {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 10mm 8mm 8mm 8mm !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            background: #fff !important;
            font-size: 12px !important;
            page-break-inside: avoid !important;
            page-break-before: avoid !important;
            page-break-after: avoid !important;
            break-inside: avoid !important;
            break-before: avoid !important;
            break-after: avoid !important;
        }
        .kasr-receipt * {
            font-size: 12px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .kasr-receipt .kasr-grid-2by2 {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 6px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .kasr-receipt .row-line,
        .kasr-receipt .kasr-card {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .kasr-receipt .muted {
            color: #888 !important;
        }
    }
</style>

@include('reports.partials.print-css')
<style>
    @media print {
        .kasr-filters-form, .kasr-filters-form * {
            display: none !important;
        }
    }
        /* Make ta9rir safi rib7 report titles white for dark mode */
        .kasr-receipt h4,
        .page-title {
            color: #fff !important;
        }
    .kasr-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        padding: 12px 8px;
        border: 1px solid #dee2e6;
    }
    .kasr-table td {
        text-align: center;
        padding: 10px 8px;
        border: 1px solid #dee2e6;
    }
    .kasr-table {
        border-collapse: collapse;
        width: 100%;
    }
    .total-row {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .caliber-label {
        font-weight: 600;
        color: #495057;
    }
    /* Receipt-style result layout */
    .kasr-receipt {
        max-width: 380px;
        margin: 0 auto;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 16px 18px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        font-size: 13px;
    }
    .kasr-receipt h4 {
        font-size: 18px;
        margin-bottom: 8px;
        font-weight: 700;
    }
    .kasr-receipt .sep {
        border: 0;
        border-top: 1px solid #e5e7eb;
        margin: 6px 0;
    }
    .kasr-receipt .row-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        padding: 4px 0;
        line-height: 1.3;
    }
    .kasr-receipt .label {
        color: #374151;
        font-weight: 600;
    }
    .kasr-receipt .value {
        color: #111827;
        font-weight: 600;
    }
    .kasr-receipt .muted {
        color: #6b7280;
        font-size: 12px;
    }
    .kasr-receipt .highlight {
        color: #15803d;
        font-weight: 700;
    }
    .kasr-receipt .danger {
        color: #b91c1c;
        font-weight: 700;
    }
    @media print {
        .kasr-receipt {
            box-shadow: none;
            border: 1px solid #000;
        }
    }
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right no-print">
                    <a href="{{ route('reports.all') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> عودة
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> طباعة
                    </button>
                </div>
                <h4 class="page-title"><i class="mdi mdi-chart-box me-1"></i> تقرير صافي الربح</h4>
            </div>
        </div>
    </div>

    <!-- Input Form -->
    <div class="card mb-3 kasr-filters-form">
        <div class="card-body">
            <form method="POST" action="{{ route('reports.kasr') }}" id="kasrForm">
                @csrf
                                <!-- Removed customInterestPopup and shake styles -->
                                <!-- Removed customInterestPopup HTML -->
                
                <!-- Filters Row -->
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <input type="hidden" name="auto_refresh" id="auto_refresh" value="0">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" id="date_from" 
                               value="{{ request('date_from', $filters['date_from'] ?? date('Y-m-01')) }}" 
                               class="form-control"
                               onchange="submitKasrFilters()">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" id="date_to" 
                               value="{{ request('date_to', $filters['date_to'] ?? date('Y-m-d')) }}" 
                               class="form-control"
                               onchange="submitKasrFilters()">
                    </div>
                    <div class="col-md-4">
                        <label for="branch_id" class="form-label">الفرع</label>
                        <select name="branch_id" id="branch_id" class="form-select" onchange="submitKasrFilters()">
                            <option value="">اختر الفرع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" 
                                    {{ request('branch_id', $filters['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100 text-muted small text-center">يتم التحديث تلقائياً عند تغيير الفرع أو التاريخ</div>
                    </div>
                </div>

                <!-- Calibers Input Table -->
                <h5 class="mb-3">أدخل بيانات العيارات</h5>
                <div class="table-responsive">
                    <table class="kasr-table table">
                        <thead>
                            <tr>
                                <th style="width: 33%">النوع</th>
                                <th style="width: 33%">الوزن</th>
                                <th style="width: 34%">سعر الكسر</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($reportData['calibers'] ?? [] as $caliber)
                            <tr>
                                <td class="caliber-label text-end">{{ $caliber['name'] }}</td>
                                <td>
                                     <input type="number" value="{{ $caliber['weight'] ?? 0 }}" step="0.01" class="form-control" disabled>
                                </td>
                                <td>
                                    @php
                                        $inputName = 'price_' . $caliber['id'];
                                        $inputValue = request($inputName, $caliber['price_per_gram'] ?? 0);
                                    @endphp
                                    <input type="number" name="{{ $inputName }}" value="{{ $inputValue }}" step="0.01" class="form-control" autocomplete="off">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Expenses and Salaries (Auto-loaded from database) -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label for="expenses" class="form-label fw-semibold">المصروفات الكاملة للفرع خلال المدة</label>
                        <select name="expenses_select" id="expenses_select" class="form-select mb-2">
                                                        @php
                                                            $expensesSum = 0;
                                                            if(isset($expensesList) && is_array($expensesList)) {
                                                                $expensesSum = array_sum($expensesList);
                                                            } elseif(isset($expenses)) {
                                                                $expensesSum = $expenses;
                                                            }
                                                        @endphp
                                                        <option value="{{ $expensesSum }}" {{ (request('expenses_select', $expensesSum) == $expensesSum) ? 'selected' : '' }}>المجموع: {{ number_format($expensesSum, 2) }}</option>
                                                        <option value="custom" {{ (request('expenses_select') == 'custom') ? 'selected' : '' }}>أدخل قيمة يدوياً</option>
                        </select>
                        <input type="number" step="0.01" name="expenses" id="expenses_input" class="form-control" style="display:none;" value="{{ request('expenses', '') }}" placeholder="أدخل المصروفات يدوياً">
                        <small class="text-muted">إجمالي كل المصروفات المسجلة للفرع المختار خلال الفترة. يمكنك التعديل هنا.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="salaries" class="form-label fw-semibold">الرواتب من شاشة الموظفين</label>
                        @php
                            $salariesSum = 0;
                            if(isset($branches) && isset($filters['branch_id']) && $filters['branch_id']) {
                                $branchEmployees = \App\Models\Employee::active()->where('branch_id', $filters['branch_id'])->get();
                                $salariesSum = $branchEmployees->sum('salary');
                            }
                        @endphp
                        <select name="salaries_select" id="salaries_select" class="form-select mb-2">
                            <option value="{{ $salariesSum }}" {{ (request('salaries_select', $salariesSum) == $salariesSum) ? 'selected' : '' }}>المجموع: {{ number_format($salariesSum, 2) }}</option>
                            <option value="custom" {{ (request('salaries_select') == 'custom') ? 'selected' : '' }}>أدخل قيمة يدوياً</option>
                        </select>
                        <input type="number" step="0.01" name="salaries" id="salaries_input" class="form-control" style="display:none;" value="{{ request('salaries', '') }}" placeholder="أدخل الرواتب يدوياً">
                        <small class="text-muted">مجموع رواتب موظفي الفرع (من شاشة الموظفين) دون ربط بفترة. يمكنك التعديل هنا.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="interest_rate" class="form-label fw-semibold">قيمة الفائدة</label>
                                           <input type="number" name="interest_rate" id="interest_rate"
                                               value="{{ old('interest_rate', $filters['interest_value'] ?? 0) }}"
                                               step="0.01" min="0" class="form-control" placeholder="مثال: 100">
                           <small class="text-muted">أدخل قيمة الفائدة مباشرة (ليست نسبة مئوية).</small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-4">
                    <button type="submit" id="kasrCalculateBtn" class="btn btn-primary btn-lg w-100">
                        <i class="mdi mdi-calculator me-1"></i> احسب التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
// Inline to avoid missing stack includes; auto-submit on branch/date change.
// Custom validation for interest_rate
(function() {
    const form = document.getElementById('kasrForm');
    const autoRefresh = document.getElementById('auto_refresh');
    window.submitKasrFilters = function() {
        if (!form) return;
        if (autoRefresh) autoRefresh.value = '1';
        setTimeout(() => {
            if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }, 0);
    };
    ['date_from','date_to','branch_id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', submitKasrFilters);
            el.addEventListener('input', submitKasrFilters);
        }
    });

    // Live interest value calculation and live update for expenses/salaries
    const interestInput = document.getElementById('interest_rate');
    const interestValue = document.getElementById('interest_value');
    const expensesInput = document.getElementById('expenses_input');
    const expensesSelect = document.getElementById('expenses_select');
    const salariesInput = document.getElementById('salaries_input');
    const salariesSelect = document.getElementById('salaries_select');
    // Try to get the total amount from the page (from the hidden input or a JS variable)
    let totalAmount = 0;
    @if(isset($reportData['total_amount']))
        totalAmount = {{ floatval($reportData['total_amount']) }};
    @endif

    function toggleInput(selectEl, inputEl) {
        if (!selectEl || !inputEl) return;
        if (selectEl.value === 'custom') {
            inputEl.style.display = '';
            inputEl.required = true;
        } else {
            inputEl.style.display = 'none';
            inputEl.required = false;
            inputEl.value = selectEl.value;
        }
    }

    if (expensesSelect && expensesInput) {
        expensesSelect.addEventListener('change', function() {
            toggleInput(expensesSelect, expensesInput);
            updateReceipt();
        });
        // Initial state
        toggleInput(expensesSelect, expensesInput);
    }
    if (salariesSelect && salariesInput) {
        salariesSelect.addEventListener('change', function() {
            toggleInput(salariesSelect, salariesInput);
            updateReceipt();
        });
        // Initial state
        toggleInput(salariesSelect, salariesInput);
    }

    function updateInterestValue() {
        const value = parseFloat(interestInput.value) || 0;
        interestValue.textContent = value.toFixed(2);
        updateReceipt();
    }
    function updateReceipt() {
        // Update receipt values for expenses, salaries, interest, profit, net profit
        const expenses = parseFloat(expensesInput.value) || 0;
        const salaries = parseFloat(salariesInput.value) || 0;
        const interest = parseFloat(interestInput.value) || 0;
        const interestAmount = interest;
        // Update receipt fields if present
        const receiptExpenses = document.getElementById('receipt_expenses');
        const receiptSalaries = document.getElementById('receipt_salaries');
        const receiptInterestRate = document.getElementById('receipt_interest_rate');
        const receiptInterestAmount = document.getElementById('receipt_interest_amount');
        const receiptProfit = document.getElementById('receipt_profit');
        const receiptNetProfit = document.getElementById('receipt_net_profit');
        if (receiptExpenses) receiptExpenses.textContent = expenses.toFixed(2);
        if (receiptSalaries) receiptSalaries.textContent = salaries.toFixed(2);
        if (receiptInterestRate) receiptInterestRate.textContent = interest.toFixed(2);
        if (receiptInterestAmount) receiptInterestAmount.textContent = interestAmount.toFixed(2);
        if (receiptProfit) receiptProfit.textContent = (totalAmount - expenses).toFixed(2);
        if (receiptNetProfit) receiptNetProfit.textContent = (totalAmount - (expenses + salaries + interestAmount)).toFixed(2);
    }
    if (interestInput && interestValue) {
        interestInput.addEventListener('input', updateInterestValue);
        updateInterestValue();
    }
    if (expensesInput) expensesInput.addEventListener('input', updateReceipt);
    if (salariesInput) salariesInput.addEventListener('input', updateReceipt);
    // Initial update
    updateReceipt();
})();
</script>

    @if(isset($reportData))
        @php
            // Ensure these variables are always defined for the styled results section
            $faida_sum = 0;
            if(isset($reportData['calibers'])) {
                foreach($reportData['calibers'] as $caliber) {
                    $faida_sum += ($caliber['weight'] ?? 0) * ($caliber['price_per_gram'] ?? 0);
                }
            }
            $total_sales = ($reportData['net_sales'] ?? 0) + ($reportData['total_tax'] ?? 0);
            $final_faida =  $total_sales - $faida_sum;
            // Get expenses and salaries from the top filters (form input or select)
            $expenses_from_filter = request('expenses_select') === 'custom'
                ? floatval(request('expenses', 0))
                : floatval(request('expenses_select', $expensesSum ?? 0));
            $salaries_from_filter = request('salaries_select') === 'custom'
                ? floatval(request('salaries', 0))
                : floatval(request('salaries_select', $salariesSum ?? 0));
            $safi = $final_faida - ($expenses_from_filter + $salaries_from_filter);
            $interest_value = $reportData['interest_value'] ?? $filters['interest_value'] ?? 0;
            $safi = isset($safi) ? $safi : 0;
            $gramWithFaida = ($interest_value != 0) ? number_format($safi / $interest_value, 2) : '—';
        @endphp
    <!-- Modern Beautiful Kasr Report Results -->
    <div class="kasr-receipt mt-3" style="width:100%; max-width:700px; margin:0 auto; background:linear-gradient(135deg,#f8fafc 60%,#e0e7ef 100%); box-shadow:0 6px 32px rgba(0,0,0,0.10); border-radius:18px; padding:32px 28px 18px 28px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
            <span style="font-size:2.2rem;color:#6366f1;"><i class="mdi mdi-cash-multiple"></i></span>
            <h3 style="margin:0;font-weight:800;color:#222;letter-spacing:0.5px;">تقرير صافي الربح</h3>
        </div>
        <div class="kasr-grid-2by2" style="margin-bottom:18px;">
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">الفرع</div>
                <div style="font-size:1.2rem;color:#222;font-weight:700;">{{ $selectedBranch->name ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">الفترة</div>
                <div style="font-size:1.2rem;color:#222;font-weight:700;">{{ $filters['date_from'] ?? '-' }} إلى {{ $filters['date_to'] ?? '-' }}</div>
            </div>
        </div>
        <div class="kasr-grid-2by2" style="margin-bottom:18px;">
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">المصروفات</div>
                <div style="font-size:1.2rem;color:#e11d48;font-weight:700;" id="receipt_expenses">{{ number_format($reportData['expenses'] ?? $expenses ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">الرواتب</div>
                <div style="font-size:1.2rem;color:#eab308;font-weight:700;" id="receipt_salaries">{{ number_format($reportData['salaries'] ?? $salaries ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">قيمة الفائدة</div>
                <div style="font-size:1.2rem;color:#0ea5e9;font-weight:700;" id="receipt_interest_rate">{{ number_format($reportData['interest_value'] ?? $filters['interest_value'] ?? 0, 2) }}</div>
            </div>
        </div>
        <div style="border-top:1.5px dashed #cbd5e1;margin:18px 0 18px 0;"></div>
        <div class="kasr-grid-2by2" style="margin-bottom:18px;">
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">إجمالي المبيعات (بدون الضريبة)</div>
                <div style="font-size:1.2rem;color:#16a34a;font-weight:700;">{{ number_format($reportData['total_sales_and_returns'] ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">قيمة المرتجع</div>
                <div style="font-size:1.2rem;color:#e11d48;font-weight:700;">{{ number_format($reportData['total_returns'] ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">مجموع الضريبة</div>
                <div style="font-size:1.2rem;color:#0ea5e9;font-weight:700;">{{ number_format($reportData['total_tax'] ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">الإجمالي (صافي المبيعات + الضريبة)</div>
                <div style="font-size:1.2rem;color:#6366f1;font-weight:700;">{{ number_format(($reportData['net_sales'] ?? 0) + ($reportData['total_tax'] ?? 0), 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">صافي المبيعات</div>
                <div style="font-size:1.2rem;color:#16a34a;font-weight:700;">{{ number_format($reportData['net_sales'] ?? 0, 2) }}</div>
            </div>
        </div>
        <div style="border-top:1.5px dashed #cbd5e1;margin:18px 0 18px 0;"></div>
        <div class="kasr-grid-2by2" style="margin-bottom:18px;">
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">مجموع الوزن (المبيعات)</div>
                <div style="font-size:1.2rem;color:#0ea5e9;font-weight:700;">{{ number_format(($reportData['total_weight'] ?? 0) + ($reportData['total_weight_returns'] ?? 0), 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">وزن المرتجع</div>
                <div style="font-size:1.2rem;color:#e11d48;font-weight:700;">{{ number_format($reportData['total_weight_returns'] ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">معدل الجرام (صافي المبيعات ÷ صافي الوزن)</div>
                <div style="font-size:1.2rem;color:#6366f1;font-weight:700;">{{ number_format($reportData['avg_price_per_gram'] ?? 0, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">سعر الجرام (الإجمالي ÷ صافي الوزن)</div>
                <div style="font-size:1.2rem;color:#6366f1;font-weight:700;">{{ number_format($reportData['price_of_gram'] ?? 0, 2) }}</div>
            </div>
        </div>
        @if(isset($reportData['calibers']) && is_array($reportData['calibers']))
        <div style="border-top:1.5px dashed #cbd5e1;margin:18px 0 18px 0;"></div>
        <div style="margin-bottom:18px;">
            <div style="font-size:1.2rem;font-weight:700;color:#222;margin-bottom:8px;">تفاصيل العيارات</div>
            <div style="display:flex;flex-wrap:wrap;gap:18px;">
                @foreach($reportData['calibers'] as $caliber)
                    @if(($caliber['cash'] ?? 0) > 0 || ($caliber['weight'] ?? 0) > 0)
                    <div style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #e0e7ef;padding:18px 16px;min-width:180px;flex:1 1 220px;">
                        <div style="font-size:1.1rem;color:#6366f1;font-weight:700;margin-bottom:6px;"><i class="mdi mdi-gold"></i> {{ $caliber['name'] }}</div>
                        <div style="font-size:1.05rem;color:#64748b;font-weight:600;">مبيعات بدون ضريبة</div>
                        <div style="font-size:1.2rem;color:#16a34a;font-weight:700;">{{ number_format($caliber['cash'] ?? 0, 2) }}</div>
                        <div style="font-size:1.05rem;color:#64748b;font-weight:600;">ذهب (وزن)</div>
                        <div style="font-size:1.2rem;color:#eab308;font-weight:700;">{{ number_format($caliber['weight'], 2) }}</div>
                        <div style="font-size:1.05rem;color:#64748b;font-weight:600;">معدل الجرام</div>
                        <div style="font-size:1.2rem;color:#0ea5e9;font-weight:700;">{{ number_format($caliber['avg_price_per_gram'] ?? 0, 2) }}</div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        <div style="border-top:1.5px dashed #cbd5e1;margin:18px 0 18px 0;"></div>
        <div class="kasr-grid-2by2" style="margin-bottom:18px;">
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">فائدة</div>
                <div style="font-size:1.2rem;color:#0ea5e9;font-weight:700;" id="receipt_profit">{{ number_format($final_faida, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">صافي الربح</div>
                <div style="font-size:1.2rem;color:#16a34a;font-weight:700;" id="receipt_net_profit">{{ number_format($safi, 2) }}</div>
            </div>
            <div>
                <div style="font-size:1.1rem;color:#64748b;font-weight:600;">سعر الجرام بفائدة</div>
                <div style="font-size:1.2rem;color:#e11d48;font-weight:700;">{{ $gramWithFaida }}</div>
            </div>
        </div>
        <div class="muted text-center mt-2" style="font-size:1.1rem;">{{ \Carbon\Carbon::now()->format('l, F d, Y') }}</div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
        // Auto-print functionality if needed
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
                window.onload = function() {
                        window.print();
                };
        }
        // Only allow numbers in expenses and salaries text inputs
        document.addEventListener('DOMContentLoaded', function() {
            function onlyNumberInput(e) {
                const v = e.target.value;
                if (v && !/^\d*\.?\d*$/.test(v)) {
                    e.target.value = v.replace(/[^\d.]/g, '');
                }
            }
            const expensesInput = document.getElementById('expenses_input');
            const salariesInput = document.getElementById('salaries_input');
            if (expensesInput) expensesInput.addEventListener('input', onlyNumberInput);
            if (salariesInput) salariesInput.addEventListener('input', onlyNumberInput);
        });
</script>
@endsection   