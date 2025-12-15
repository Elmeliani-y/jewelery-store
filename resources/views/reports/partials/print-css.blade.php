  <style>
  @media print {
    h4.page-title {
      display: none !important;
    }
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
  @page { size: A4 portrait; margin: 2mm 8mm 2mm 8mm; }
  /* Remove forced height and overflow to allow content to print fully */

  /* Scale content to fit one page if needed */
  body {
    transform-origin: top left !important;
    /* The scale will be set by the browser if user selects "Fit to page" in print dialog */
  }
  body {
    font-family: 'Cairo', 'Arial', 'Tahoma', 'sans-serif' !important;
    background: white !important;
    padding: 0 !important;
    font-size: 13px !important;
    direction: rtl !important;
  }
  .topbar-custom,
  .app-sidebar-menu,
  .footer,
  .report-toolbar,
  .no-print,
  .card-footer,
  .kasr-filters-form {
    display: none !important;
  }
  /* Always show main report content in print */
  .container-fluid,
  .kasr-receipt {
    display: block !important;
    visibility: visible !important;
    color: #222 !important;
    background: #fff !important;
    max-width: 100% !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    border: none !important;
  }
  /* For kasr report: keep all content on one page, but do NOT force each element to a new page */
  .kasr-receipt, .kasr-table, .kasr-table th, .kasr-table td, .kasr-table tr, .kasr-table tbody, .kasr-table thead {
    page-break-inside: avoid !important;
    page-break-before: avoid !important;
    page-break-after: avoid !important;
    visibility: visible !important;
    color: #222 !important;
  }
  .kasr-receipt .row-line {
    font-size: 13px !important;
    padding: 1.5mm 0 !important;
    display: flex !important;
    visibility: visible !important;
  }
  .kasr-receipt legend {
    font-size: 15px !important;
    font-weight: bold !important;
    margin-bottom: 2mm !important;
    margin-top: 1mm !important;
    visibility: visible !important;
  }
  .kasr-receipt .label, .kasr-receipt .value {
    font-size: 13px !important;
    font-family: 'Cairo', 'Arial', 'Tahoma', 'sans-serif' !important;
    visibility: visible !important;
  }
  /* Remove page breaks for kasr report */
  .kasr-receipt, .kasr-table, .kasr-table th, .kasr-table td, .kasr-table tr, .kasr-table tbody, .kasr-table thead {
    page-break-after: auto !important;
    page-break-inside: avoid !important;
  }
  .kasr-receipt:last-of-type, .kasr-table:last-of-type {
    page-break-after: auto !important;
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
    font-size: 14px !important;
    border-collapse: collapse !important;
    margin: 0 !important;
    direction: rtl !important;
    border: 1.5px solid #222 !important;
  }
  .table th, .table td {
    padding: 12px 14px !important;
    border: 1.5px solid #222 !important;
    text-align: center !important;
    font-size: 14px !important;
    font-family: 'Cairo', 'Arial', 'Tahoma', 'sans-serif' !important;
  }
  .table th {
    background: #34495e !important;
    font-weight: 700 !important;
    color: white !important;
    font-size: 15px !important;
    border-bottom: 2px solid #222 !important;
  }
  .table td {
    background: white !important;
    color: #222 !important;
  }
  .table tbody tr:nth-child(even) td {
    background: #f3f3f3 !important;
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

  /* Remove forced page breaks for tables and charts so all report content prints together */
}
</style>
