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
  @page { size: A4 landscape; margin: 15mm; }
  body { 
    font-family: 'Arial', sans-serif !important;
    background: white !important;
  }
  .topbar-custom,.app-sidebar-menu,.footer, .report-toolbar, .no-print, .card-footer { display:none !important; }
  
  /* Print Header */
  .print-title { 
    display: block !important; 
    text-align: center !important; 
    margin-bottom: 20px !important; 
    padding: 15px 0 !important; 
    border-bottom: 3px solid #000 !important;
    background: #f8f9fa !important;
  }
  .print-title h2 { 
    font-size: 22px !important; 
    font-weight: 700 !important; 
    margin: 0 0 8px 0 !important;
    color: #000 !important;
  }
  .print-title p { 
    font-size: 12px !important; 
    color: #555 !important; 
    margin: 0 !important;
    line-height: 1.6 !important;
  }
  
  /* Cards Layout */
  .card {
    box-shadow: none !important;
    border: 2px solid #333 !important;
    margin-bottom: 15px !important;
    page-break-inside: avoid !important;
    background: white !important;
  }
  .card-header {
    background: #e9ecef !important;
    border-bottom: 2px solid #333 !important;
    padding: 10px 15px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    color: #000 !important;
    text-align: center !important;
  }
  .card-body {
    padding: 0 !important;
  }
  
  /* Container */
  .container-fluid { 
    max-width: 100% !important; 
    width: 100% !important; 
    padding: 0 10px !important; 
  }
  .row { 
    margin: 0 !important;
    display: block !important;
  }
  .col-lg-6 { 
    width: 100% !important; 
    max-width: 100% !important; 
    flex: none !important;
    margin-bottom: 15px !important;
  }
  
  /* Tables */
  .table-responsive {
    overflow: visible !important;
  }
  .table { 
    width: 100% !important; 
    font-size: 10px !important; 
    table-layout: fixed !important; 
    border-collapse: collapse !important;
    margin: 0 !important;
  }
  .table th, .table td { 
    padding: 6px 8px !important; 
    white-space: nowrap !important; 
    border: 1px solid #333 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }
  .table th { 
    background: #d1d1d1 !important;
    font-weight: 700 !important;
    color: #000 !important;
    text-align: center !important;
    font-size: 11px !important;
  }
  .table td {
    background: white !important;
    color: #000 !important;
  }
  .table tbody tr:nth-child(even) {
    background: #f9f9f9 !important;
  }
  
  /* Badges & Text */
  .badge { 
    border: 1px solid #000 !important; 
    padding: 2px 6px !important; 
    font-size: 9px !important;
    background: white !important;
    color: #000 !important;
  }
  .text-danger { color: #000 !important; font-weight: 700 !important; }
  .text-success { color: #000 !important; font-weight: 700 !important; }
  .text-warning { color: #000 !important; font-weight: 700 !important; }
  .text-info { color: #000 !important; }
  .text-muted { color: #666 !important; }
  .fw-bold { font-weight: 700 !important; }
  
  /* Remove pagination */
  .pagination { display: none !important; }
  
  /* Better spacing */
  .mb-4 { margin-bottom: 15px !important; }
}
</style>
