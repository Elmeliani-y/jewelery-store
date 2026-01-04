

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title text-primary-emphasis">مقارنة بالفترات</h4>
                <div>
                    <a href="<?php echo e(route('t3u8v1w4.b1c5d8e3')); ?>" class="btn btn-secondary">عودة للتقارير</a>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('t3u8v1w4.p4q9r1s7')); ?>" class="card mb-4" id="mo9aranaForm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3" id="from1-group"></div>
                <div class="col-md-3" id="to1-group"></div>
                <div class="col-md-3" id="from2-group"></div>
                <div class="col-md-3" id="to2-group"></div>

                <div class="col-md-3">
                    <label class="form-label">الفرع</label>
                    <select name="branch_id" class="form-select">
                        <option value="">كل الفروع</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($b->id); ?>" <?php echo e((string)($filters['branch_id'] ?? '') === (string)$b->id ? 'selected' : ''); ?>><?php echo e($b->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">نوع المدة</label>
                    <select name="period_type" class="form-select" id="periodTypeSelect">
                        <option value="annual" <?php echo e(($filters['period_type'] ?? 'annual') === 'annual' ? 'selected' : ''); ?>>سنوي</option>
                        <option value="monthly" <?php echo e(($filters['period_type'] ?? '') === 'monthly' ? 'selected' : ''); ?>>شهري</option>
                        <option value="weekly" <?php echo e(($filters['period_type'] ?? '') === 'weekly' ? 'selected' : ''); ?>>أسبوعي</option>
                        <option value="special" <?php echo e(($filters['period_type'] ?? '') === 'special' ? 'selected' : ''); ?>>مخصص</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" type="submit">عرض المقارنة</button>
                </div>
            </div>
        </div>
    </form>
    <script>
    // Helper to get default years
    function getDefaultYear(offset = 0) {
        const d = new Date();
        return d.getFullYear() + offset;
    }
    function getDefaultMonth() {
        return (new Date().getMonth() + 1).toString().padStart(2, '0');
    }
    function renderInputs(periodType) {
        // Use actual GET params for all modes
        const filters = {
            from1: "<?php echo e(request('from1') ? request('from1') : (old('from1', $filters['from1'] ?? ''))); ?>",
            to1: "<?php echo e(request('to1') ? request('to1') : (old('to1', $filters['to1'] ?? ''))); ?>",
            from2: "<?php echo e(request('from2') ? request('from2') : (old('from2', $filters['from2'] ?? ''))); ?>",
            to2: "<?php echo e(request('to2') ? request('to2') : (old('to2', $filters['to2'] ?? ''))); ?>",
            from1_year: "<?php echo e(request('from1_year') ? request('from1_year') : (old('from1_year', ''))); ?>",
            from2_year: "<?php echo e(request('from2_year') ? request('from2_year') : (old('from2_year', ''))); ?>"
        };
        let yThis = getDefaultYear();
        let yLast = getDefaultYear(-1);
        let mThis = getDefaultMonth();
        // Annual: year dropdowns (calendar style)
            if (periodType === 'annual') {
                let f1y = filters.from1_year || filters.from1 || yThis;
                let f2y = filters.from2_year || filters.from2 || yLast;
                let yearOptions1 = '';
                let yearOptions2 = '';
                for (let y = yThis + 1; y >= 2000; y--) {
                    yearOptions1 += `<option value='${y}'${y == f1y ? ' selected' : ''}>${y}</option>`;
                    yearOptions2 += `<option value='${y}'${y == f2y ? ' selected' : ''}>${y}</option>`;
                }
                document.getElementById('from1-group').innerHTML = `<label class='form-label'>من (الفترة 1)</label><div class='input-group'><span class='input-group-text'><i class='mdi mdi-calendar'></i></span><select name='from1_year' class='form-select'>${yearOptions1}</select></div>`;
                document.getElementById('to1-group').innerHTML = '';
                document.getElementById('from2-group').innerHTML = `<label class='form-label'>من (الفترة 2)</label><div class='input-group'><span class='input-group-text'><i class='mdi mdi-calendar'></i></span><select name='from2_year' class='form-select'>${yearOptions2}</select></div>`;
                document.getElementById('to2-group').innerHTML = '';
        } else if (periodType === 'monthly') {
            // Monthly: month/year dropdowns only for each period
            let monthOptions = '';
            for (let m = 1; m <= 12; m++) {
                let mm = m.toString().padStart(2, '0');
                monthOptions += `<option value='${mm}'>${mm}</option>`;
            }
            let yearOptions = '';
            for (let y = yThis + 1; y >= 2000; y--) {
                yearOptions += `<option value='${y}'>${y}</option>`;
            }
            document.getElementById('from1-group').innerHTML = `<label class='form-label'>الفترة 1 (شهر/سنة)</label><div class='input-group'><span class='input-group-text'><i class='mdi mdi-calendar'></i></span><select name='from1_year' class='form-select'>${yearOptions}</select><select name='from1_month' class='form-select'>${monthOptions}</select></div>`;
            document.getElementById('to1-group').innerHTML = '';
            document.getElementById('from2-group').innerHTML = `<label class='form-label'>الفترة 2 (شهر/سنة)</label><div class='input-group'><span class='input-group-text'><i class='mdi mdi-calendar'></i></span><select name='from2_year' class='form-select'>${yearOptions}</select><select name='from2_month' class='form-select'>${monthOptions}</select></div>`;
            document.getElementById('to2-group').innerHTML = '';
            setTimeout(function() {
                // Set initial values robustly
                const from1y = document.querySelector("select[name='from1_year']");
                const from1m = document.querySelector("select[name='from1_month']");
                const from2y = document.querySelector("select[name='from2_year']");
                const from2m = document.querySelector("select[name='from2_month']");
                let f1y = filters.from1 ? filters.from1.split('-')[0] : yThis;
                let f1m = filters.from1 ? filters.from1.split('-')[1] : mThis;
                let f2y = filters.from2 ? filters.from2.split('-')[0] : yThis;
                let f2m = filters.from2 ? filters.from2.split('-')[1] : mThis;
                from1y.value = f1y;
                from1m.value = f1m;
                from2y.value = f2y;
                from2m.value = f2m;
            }, 50);
        } else if (periodType === 'weekly') {
            // Weekly: single date picker for each period, backend will use week of selected date
            document.getElementById('from1-group').innerHTML = `<label class='form-label'>الفترة 1 (اختر يومًا)</label><input type='date' name='from1' class='form-control' value='${filters.from1 || ''}'>`;
            document.getElementById('to1-group').innerHTML = '';
            document.getElementById('from2-group').innerHTML = `<label class='form-label'>الفترة 2 (اختر يومًا)</label><input type='date' name='from2' class='form-control' value='${filters.from2 || ''}'>`;
            document.getElementById('to2-group').innerHTML = '';
        } else {
            // Special: full date pickers
            document.getElementById('from1-group').innerHTML = `<label class='form-label'>من (الفترة 1)</label><input type='date' name='from1' class='form-control' value='${filters.from1 || ''}'>`;
            document.getElementById('to1-group').innerHTML = `<label class='form-label'>إلى (الفترة 1)</label><input type='date' name='to1' class='form-control' value='${filters.to1 || ''}'>`;
            document.getElementById('from2-group').innerHTML = `<label class='form-label'>من (الفترة 2)</label><input type='date' name='from2' class='form-control' value='${filters.from2 || ''}'>`;
            document.getElementById('to2-group').innerHTML = `<label class='form-label'>إلى (الفترة 2)</label><input type='date' name='to2' class='form-control' value='${filters.to2 || ''}'>`;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('periodTypeSelect');
        renderInputs(select.value);
        select.addEventListener('change', function() {
            renderInputs(this.value);
        });

        // Fix: On form submit, assemble YYYY-MM for monthly mode
        document.getElementById('mo9aranaForm').addEventListener('submit', function(e) {
            const periodType = document.getElementById('periodTypeSelect').value;
            if (periodType === 'monthly') {
                // ...existing code...
            }
            if (periodType === 'special') {
                // Convert all date inputs to YYYY-MM-DD before submit
                ['from1','to1','from2','to2'].forEach(function(name) {
                    let el = document.querySelector(`input[name='${name}']`);
                    if (el && el.value) {
                        // Try to parse and reformat if needed
                        let v = el.value;
                        // If value is MM/DD/YYYY, convert to YYYY-MM-DD
                        if (/^\d{2}\/\d{2}\/\d{4}$/.test(v)) {
                            let parts = v.split('/');
                            el.value = `${parts[2]}-${parts[0].padStart(2,'0')}-${parts[1].padStart(2,'0')}`;
                        }
                    }
                });
            }
        });
    });
    </script
    </form>

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()"><i class="mdi mdi-printer"></i> طباعة</button>
            <button class="btn btn-outline-danger" onclick="exportTableToPDF()"><i class="mdi mdi-file-pdf"></i> PDF</button>
            <button class="btn btn-outline-success" onclick="exportTableToCSV()"><i class="mdi mdi-file-excel"></i> CSV</button>
        </div>
    </div>

    <style>
    @media print {
        body, html { margin: 0 !important; padding: 0 !important; }
        .container-fluid { width: 100vw !important; margin: 0 !important; padding: 0 !important; }
        .card, .card-header, .card-body, .table-responsive, .table, .table * { visibility: visible !important; }
        .card, .table-responsive { page-break-inside: avoid; }
        .btn, .form-control, .form-select, .navbar, .sidebar, .footer, .row.mb-3 { display: none !important; }
        .page-title-box, .row.g-3, form { display: block !important; visibility: visible !important; }
        .table { width: 100vw !important; font-size: 10pt; margin: 0 !important; }
        th, td { padding: 3px !important; }
        h4, h5 { font-size: 12pt !important; margin-top: 2px !important; margin-bottom: 2px !important; }
        .card { border: 1px solid #333 !important; margin-bottom: 2px !important; }
        .card-header { background: #eee !important; font-weight: bold !important; padding: 2px !important; }
        .card-body { padding: 2px !important; }
        .row.mt-4, .row.mt-3 { margin-top: 2px !important; }
        @page { size: A4 landscape; margin: 5mm; }
    }
    .report-title-print {
        display: block;
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        margin-top: 2px;
        margin-bottom: 2px;
    }
    .report-date-print {
        display: block;
        text-align: center;
        font-size: 10pt;
        margin-bottom: 2px;
    }
    </style>

    <div class="report-title-print">مقارنة بالفترات</div>
    <div class="report-date-print">تاريخ الطباعة: <?php echo e(now()->format('Y-m-d H:i')); ?></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
    <script>
    function exportTableToCSV() {
        let csv = '';
        document.querySelectorAll('.table-bordered').forEach(function(table) {
            let rows = table.querySelectorAll('tr');
            rows.forEach(function(row) {
                let cols = row.querySelectorAll('th,td');
                let rowData = [];
                cols.forEach(function(col) { rowData.push('"' + col.innerText.replace(/"/g, '""') + '"'); });
                csv += rowData.join(',') + '\n';
            });
            csv += '\n';
        });
        let blob = new Blob([csv], { type: 'text/csv' });
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'report.csv';
        link.click();
    }
    function exportTableToPDF() {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF('l', 'pt', 'a4');
        let y = 20;
        document.querySelectorAll('.card').forEach(function(card, idx) {
            let title = card.querySelector('.card-header')?.innerText || '';
            let table = card.querySelector('table');
            if (table) {
                if (title) doc.text(title, 40, y + 20);
                window.jspdf.autoTable(doc, { html: table, startY: y + 30, theme: 'grid', styles: { fontSize: 10 } });
                y = doc.lastAutoTable.finalY + 30;
            }
        });
        doc.save('report.pdf');
    }
    </script>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الفترة 1: <?php echo e($period1['period']); ?></h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>المقياس</th>
                                <th class="text-end">القيمة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                function calculatePercentDiff($val1, $val2) {
                                    $diff = $val1 - $val2;
                                    if ($val2 != 0) {
                                        return round(($diff / $val2) * 100, 2);
                                    } elseif ($val1 != 0) {
                                        return 100.00;
                                    } else {
                                        return 0;
                                    }
                                }
                                $salesPct = calculatePercentDiff($period1['total_sales'] ?? 0, $period2['total_sales'] ?? 0);
                                $weightPct = calculatePercentDiff($period1['total_weight'] ?? 0, $period2['total_weight'] ?? 0);
                                $expensesPct = calculatePercentDiff($period1['total_expenses'] ?? 0, $period2['total_expenses'] ?? 0);
                                $countPct = calculatePercentDiff($period1['sales_count'] ?? 0, $period2['sales_count'] ?? 0);
                            ?>
                            <tr>
                                <td>إجمالي المبيعات</td>
                                <td class="text-end"><?php echo e(number_format($period1['total_sales'] ?? 0, 2)); ?> ريال</td>
                            </tr>
                            <tr>
                                <td>إجمالي الوزن</td>
                                <td class="text-end"><?php echo e(number_format($period1['total_weight'] ?? 0, 2)); ?> جرام</td>
                            </tr>
                            <tr>
                                <td>معدل سعر الجرام</td>
                                <td class="text-end"><?php echo e(number_format($period1['price_per_gram'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>عدد المبيعات</td>
                                <td class="text-end"><?php echo e(number_format($period1['sales_count'] ?? 0, 0, ',', '.')); ?></td>
                            </tr>
                            <tr>
                                <td>إجمالي المصروفات</td>
                                <td class="text-end"><?php echo e(number_format($period1['total_expenses'] ?? 0, 2)); ?> ريال</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الفترة 2: <?php echo e($period2['period']); ?></h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>المقياس</th>
                                <th class="text-end">القيمة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>إجمالي المبيعات</td>
                                <td class="text-end"><?php echo e(number_format($period2['total_sales'] ?? 0, 2)); ?> ريال</td>
                            </tr>
                            <tr>
                                <td>إجمالي الوزن</td>
                                <td class="text-end"><?php echo e(number_format($period2['total_weight'] ?? 0, 2)); ?> جرام</td>
                            </tr>
                            <tr>
                                <td>معدل سعر الجرام</td>
                                <td class="text-end"><?php echo e(number_format($period2['price_per_gram'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>عدد المبيعات</td>
                                <td class="text-end"><?php echo e(number_format($period2['sales_count'] ?? 0, 0, ',', '.')); ?></td>
                            </tr>
                            <tr>
                                <td>إجمالي المصروفات</td>
                                <td class="text-end"><?php echo e(number_format($period2['total_expenses'] ?? 0, 2)); ?> ريال</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الفروقات %</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="d-block text-muted mb-1">المبيعات</small>
                                <strong class="<?php echo e($salesPct > 0 ? 'text-success' : ($salesPct < 0 ? 'text-danger' : '')); ?>">
                                    <?php echo e($salesPct > 0 ? '+' : ''); ?><?php echo e(number_format($salesPct, 2)); ?>%
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="d-block text-muted mb-1">الوزن</small>
                                <strong class="<?php echo e($weightPct > 0 ? 'text-success' : ($weightPct < 0 ? 'text-danger' : '')); ?>">
                                    <?php echo e($weightPct > 0 ? '+' : ''); ?><?php echo e(number_format($weightPct, 2)); ?>%
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="d-block text-muted mb-1">المصروفات</small>
                                <strong class="<?php echo e($expensesPct > 0 ? 'text-success' : ($expensesPct < 0 ? 'text-danger' : '')); ?>">
                                    <?php echo e($expensesPct > 0 ? '+' : ''); ?><?php echo e(number_format($expensesPct, 2)); ?>%
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="d-block text-muted mb-1">العدد</small>
                                <strong class="<?php echo e($countPct > 0 ? 'text-success' : ($countPct < 0 ? 'text-danger' : '')); ?>">
                                    <?php echo e($countPct > 0 ? '+' : ''); ?><?php echo e(number_format($countPct, 2)); ?>%
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="row mt-4">
        <div class="col-12">
            <h5 class="mb-3">جدول تفصيلي حسب الموظف / العيار / الصنف</h5>
            <?php $__currentLoopData = ['employees' => 'الموظف', 'calibers' => 'العيار', 'categories' => 'الصنف']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-4">
                    <div class="card-header bg-light"><b>حسب <?php echo e($label); ?></b></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo e($label); ?></th>
                                        <th>مبيعات الفترة 1</th>
                                        <th>مبيعات الفترة 2</th>
                                        <th>وزن الفترة 1</th>
                                        <th>وزن الفترة 2</th>
                                        <th>نسبة الفرق بالمبيعات</th>
                                        <th>نسبة الفرق بالوزن</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $groupAggregates[$type]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($row['name']); ?></td>
                                            <td class="text-end"><?php echo e(number_format($row['total_sales_1'], 2)); ?></td>
                                            <td class="text-end"><?php echo e(number_format($row['total_sales_2'], 2)); ?></td>
                                            <td class="text-end"><?php echo e(number_format($row['total_weight_1'], 2)); ?></td>
                                            <td class="text-end"><?php echo e(number_format($row['total_weight_2'], 2)); ?></td>
                                            <td class="text-end">
                                                <span class="fw-bold <?php echo e($row['sales_diff_pct'] > 0 ? 'text-success' : ($row['sales_diff_pct'] < 0 ? 'text-danger' : '')); ?>">
                                                    <?php if($row['sales_diff_pct'] > 0): ?>+<?php endif; ?><?php echo e(number_format($row['sales_diff_pct'], 2)); ?>%
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold <?php echo e($row['weight_diff_pct'] > 0 ? 'text-success' : ($row['weight_diff_pct'] < 0 ? 'text-danger' : '')); ?>">
                                                    <?php if($row['weight_diff_pct'] > 0): ?>+<?php endif; ?><?php echo e(number_format($row['weight_diff_pct'], 2)); ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <?php $rows = collect($groupAggregates[$type]); ?>
                                <?php if($rows->count()): ?>
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <td>الإجماليات</td>
                                        <td class="text-end"><?php echo e(number_format($rows->sum('total_sales_1'), 2)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($rows->sum('total_sales_2'), 2)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($rows->sum('total_weight_1'), 2)); ?></td>
                                        <td class="text-end"><?php echo e(number_format($rows->sum('total_weight_2'), 2)); ?></td>
                                        <td class="text-end">
                                            <?php
                                                $totalSales1 = $rows->sum('total_sales_1');
                                                $totalSales2 = $rows->sum('total_sales_2');
                                                $salesDiff = $totalSales2 - $totalSales1;
                                                if ($totalSales1 != 0) {
                                                    $avgSalesDiff = round(($salesDiff / $totalSales1) * 100, 2);
                                                } elseif ($totalSales2 != 0) {
                                                    $avgSalesDiff = 100.00;
                                                } else {
                                                    $avgSalesDiff = 0;
                                                }
                                            ?>
                                            <span class="fw-bold <?php echo e($avgSalesDiff > 0 ? 'text-success' : ($avgSalesDiff < 0 ? 'text-danger' : '')); ?>">
                                                <?php if($avgSalesDiff > 0): ?>+<?php endif; ?><?php echo e(number_format($avgSalesDiff, 2)); ?>%
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php
                                                $totalWeight1 = $rows->sum('total_weight_1');
                                                $totalWeight2 = $rows->sum('total_weight_2');
                                                $weightDiff = $totalWeight2 - $totalWeight1;
                                                if ($totalWeight1 != 0) {
                                                    $avgWeightDiff = round(($weightDiff / $totalWeight1) * 100, 2);
                                                } elseif ($totalWeight2 != 0) {
                                                    $avgWeightDiff = 100.00;
                                                } else {
                                                    $avgWeightDiff = 0;
                                                }
                                            ?>
                                            <span class="fw-bold <?php echo e($avgWeightDiff > 0 ? 'text-success' : ($avgWeightDiff < 0 ? 'text-danger' : '')); ?>">
                                                <?php if($avgWeightDiff > 0): ?>+<?php endif; ?><?php echo e(number_format($avgWeightDiff, 2)); ?>%
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['title' => 'مقارنة بالفترات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/reports/period_comparison.blade.php ENDPATH**/ ?>