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
  @page { size: A4 landscape; margin: 12mm; }
  body { 
    font-family: 'Arial', sans-serif !important;
    background: white !important;
    padding: 0 !important;
  }
  .topbar-custom,.app-sidebar-menu,.footer, .report-toolbar, .no-print, .card-footer { display:none !important; }
  
  /* Print Header */
  .print-title { 
    display: block !important; 
    text-align: center !important; 
    margin: 0 0 20px 0 !important; 
    padding: 20px 15px !important; 
    border-bottom: none !important;
    background: #2c3e50 !important;
    border-radius: 6px !important;
  }
  .print-title h2 { 
    font-size: 26px !important; 
    font-weight: 700 !important; 
    margin: 0 0 10px 0 !important;
    color: white !important;
    letter-spacing: 0.5px !important;
  }
  .print-title p { 
    font-size: 13px !important; 
    color: #ecf0f1 !important; 
    margin: 0 !important;
    line-height: 1.8 !important;
  }
  
  /* Cards Layout */
  .card {
    box-shadow: none !important;
    border: 1px solid #ddd !important;
    margin: 0 0 16px 0 !important;
    page-break-inside: avoid !important;
    page-break-after: auto !important;
    background: white !important;
    border-radius: 6px !important;
    overflow: hidden !important;
  }
  .card-header {
    background: #34495e !important;
    border-bottom: none !important;
    padding: 14px 20px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    color: white !important;
    text-align: center !important;
    letter-spacing: 0.3px !important;
  }
  .card-body {
    padding: 0 !important;
  }
  
  /* Container */
  .container-fluid { 
    max-width: 100% !important; 
    width: 100% !important; 
    padding: 0 !important; 
  }
  .row { 
    margin: 0 !important;
  }
  .col-lg-6 { 
    padding: 0 8px !important;
    margin-bottom: 15px !important;
  }
  
  /* Tables */
  .table-responsive {
    overflow: visible !important;
  }
  .table { 
    width: 100% !important; 
    font-size: 11px !important; 
    border-collapse: collapse !important;
    margin: 0 !important;
  }
  .table th, .table td { 
    padding: 8px 10px !important; 
    border: 1px solid #ddd !important;
    text-align: center !important;
  }
  .table th { 
    background: #34495e !important;
    font-weight: 700 !important;
    color: white !important;
    font-size: 12px !important;
  }
  .table td {
    background: white !important;
    color: #2c3e50 !important;
  }
  .table tbody tr:nth-child(even) td {
    background: #f8f9fa !important;
  }
  
  /* Badges & Text */
  .badge { 
    border: 1px solid #2c3e50 !important; 
    padding: 3px 8px !important; 
    font-size: 10px !important;
    background: #ecf0f1 !important;
    color: #2c3e50 !important;
    border-radius: 3px !important;
  }
  .text-danger { 
    color: #c0392b !important; 
    font-weight: 600 !important; 
    text-decoration: underline !important;
  }
  .text-success { color: #27ae60 !important; font-weight: 600 !important; }
  .text-warning { color: #f39c12 !important; font-weight: 600 !important; }
  .text-info { color: #3498db !important; font-weight: 600 !important; }
  .text-muted { color: #7f8c8d !important; }
  .fw-bold { font-weight: 700 !important; }
  
  /* Remove pagination */
  .pagination { display: none !important; }
  
  /* Better spacing */
  .mb-4 { margin-bottom: 20px !important; }
  .content-page, .content { padding: 0 !important; }
  .container-fluid { padding: 0 8px !important; }
  
  /* Comparative report: keep main chart full-width in print */
  #branchesSalesExpensesChart { display: block !important; }
  .d-print-block .card { page-break-inside: avoid !important; }
  
  /* Ensure charts print full size and all branches are visible */
  .chart-container,
  #branchesSalesExpensesChart {
    width: 100% !important;
    max-width: 100% !important;
    height: 400px !important;
    min-height: 350px !important;
    display: block !important;
    overflow: visible !important;
  }
  canvas#branchesSalesExpensesChart {
    width: 100% !important;
    max-width: 100% !important;
    height: 400px !important;
    min-height: 350px !important;
    display: block !important;
    overflow: visible !important;
  }
}
</style>
