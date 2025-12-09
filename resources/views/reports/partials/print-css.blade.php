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
  @page { size: A4 landscape; margin: 10mm; }
  .topbar-custom,.app-sidebar-menu,.footer, .report-toolbar { display:none !important; }
  .print-title { display: block !important; text-align: center !important; margin-bottom: 15px !important; padding-bottom: 10px !important; border-bottom: 2px solid #333 !important; }
  .print-title h2 { font-size: 18px !important; font-weight: 700 !important; margin: 0 0 5px 0 !important; }
  .print-title p { font-size: 11px !important; color: #666 !important; margin: 0 !important; }
  .card{box-shadow:none !important;border:1px solid #ddd !important;margin-bottom:10px !important;}
  .card-header{background:#f8f9fa !important;border-bottom:1px solid #ddd !important;padding:8px 12px !important;font-weight:600 !important;}
  .container-fluid { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
  .row { margin: 0 !important; }
  .col-lg-6 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; page-break-after: always; }
  .table { width: 100% !important; font-size: 9px !important; table-layout: auto !important; border-collapse: collapse !important; }
  .table th, .table td { padding: 3px 6px !important; white-space: normal !important; border: 1px solid #ddd !important; }
  .table th { background: #f0f0f0 !important; font-weight: 600 !important; }
  .badge { border: 1px solid #333 !important; padding: 2px 5px !important; font-size: 8px !important; }
  .text-danger { color: #dc3545 !important; }
  .text-success { color: #28a745 !important; }
  .fw-bold { font-weight: 700 !important; }
  .card { margin-bottom: 15px !important; }
}
</style>
