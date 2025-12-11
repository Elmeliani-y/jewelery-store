@php
    $exportFilename = $exportFilename ?? ($title ? \Illuminate\Support\Str::slug($title, '_') : 'report');
@endphp
<div class="d-flex justify-content-between align-items-center mb-3 report-toolbar">
  <div class="d-flex align-items-center gap-2">
    <h4 class="m-0">{{ $title ?? '' }}</h4>
    @if(!empty($filters['date_from']) || !empty($filters['date_to']))
      <span class="badge bg-light text-muted border small">من {{ $filters['date_from'] ?? '-' }} إلى {{ $filters['date_to'] ?? '-' }}</span>
    @endif
  </div>
  <div class="d-flex align-items-center">
    <a href="{{ $backUrl ?? route('reports.index') }}" class="btn btn-light btn-sm" title="الرجوع"><iconify-icon icon="solar:arrow-right-bold"></iconify-icon></a>
    <div class="btn-group ms-2">
      <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <iconify-icon icon="solar:export-bold" class="me-1"></iconify-icon> تصدير
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><button type="button" class="dropdown-item" onclick="window.exportTablesAsCsv('{{ $exportFilename }}')">CSV</button></li>
        <li><button type="button" class="dropdown-item" onclick="window.exportPageToPdf()">PDF (A4)</button></li>
      </ul>
    </div>
    <button class="btn btn-primary btn-sm ms-2" onclick="window.print()"><iconify-icon icon="solar:printer-bold" class="me-1"></iconify-icon> طباعة</button>
  </div>
</div>

@once
<script>
(() => {
  if (window.__reportsExportBound) return;
  window.__reportsExportBound = true;

  const cleanText = (value) => {
    return (value || '').toString().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
  };

  window.exportTablesAsCsv = function(filename = 'report') {
    const tables = Array.from(document.querySelectorAll('table'));
    if (!tables.length) {
      alert('لا توجد جداول للتصدير');
      return;
    }
    let csv = '';
    tables.forEach((table, idx) => {
      if (idx > 0) csv += '\n\n';
      const rows = Array.from(table.querySelectorAll('tr'));
      rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th,td')).map(cell => `"${cleanText(cell.innerText)}"`);
        csv += cells.join(',') + '\n';
      });
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    const stamp = new Date().toISOString().slice(0,10);
    link.download = `${filename || 'report'}_${stamp}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  };

  window.exportPageToPdf = function() {
    // Rely on browser print-to-PDF with existing A4 print styles.
    window.print();
  };
})();
</script>
@endonce
