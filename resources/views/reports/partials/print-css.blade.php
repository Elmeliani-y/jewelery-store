<style>
.report-toolbar .btn { border-radius: 8px; }
.report-toolbar .btn + .btn, .report-toolbar .btn-group { margin-inline-start: .35rem; }
.report-toolbar .dropdown-menu { min-width: 160px; }
.table th, .table td { vertical-align: middle; }
.table td[dir="ltr"] { text-align: left; }
/* Print-friendly table flow */
table { page-break-inside: auto; }
tr    { page-break-inside: avoid; page-break-after: auto; }
thead { display: table-header-group; }
tfoot { display: table-footer-group; }
@media print { 
  @page { size: A4 portrait; margin: 12mm; }
  .topbar-custom,.app-sidebar-menu,.footer, .report-toolbar { display:none !important; }
  .card{box-shadow:none !important;border:0 !important;}
}
</style>
