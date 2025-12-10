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
        <li><a class="dropdown-item" href="{{ route($exportRoute, array_merge($exportQuery ?? [], ['format'=>'pdf'])) }}">PDF</a></li>
        <li><a class="dropdown-item" href="{{ route($exportRoute, array_merge($exportQuery ?? [], ['format'=>'excel'])) }}">Excel</a></li>
        <li><a class="dropdown-item" href="{{ route($exportRoute, array_merge($exportQuery ?? [], ['format'=>'csv'])) }}">CSV</a></li>
      </ul>
    </div>
    <button class="btn btn-primary btn-sm ms-2" onclick="window.print()"><iconify-icon icon="solar:printer-bold" class="me-1"></iconify-icon> طباعة</button>
  </div>
</div>
