// Chart.js datalabels plugin loader for dashboard
// Loads the plugin from CDN if not already loaded
(function(){
  function registerDatalabels() {
    if (window.Chart && window.Chart.register && window.ChartDataLabels) {
      try { window.Chart.register(window.ChartDataLabels); } catch(e) {}
      window._datalabelsReady = true;
      document.dispatchEvent(new Event('datalabels:ready'));
    }
  }
  if (window.Chart && window.Chart.register && window.ChartDataLabels) {
    registerDatalabels();
    return;
  }
  var script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js';
  script.onload = registerDatalabels;
  document.head.appendChild(script);
})();
