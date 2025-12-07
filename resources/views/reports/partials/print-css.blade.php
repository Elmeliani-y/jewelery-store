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
  @page { size: A4 landscape; margin: 8mm; }
  .topbar-custom,.app-sidebar-menu,.footer, .report-toolbar { display:none !important; }
  .card{box-shadow:none !important;border:0 !important;}
  .container-fluid { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
  .row { margin: 0 !important; }
  .col-lg-6 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; page-break-after: always; }
  .table { width: 100% !important; font-size: 9px !important; table-layout: auto !important; }
  .table th, .table td { padding: 3px 6px !important; white-space: normal !important; }
  .card { margin-bottom: 15px !important; }
}
</style>
