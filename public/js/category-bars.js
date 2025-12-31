// category-bars.js
// Renders the sales by category chart using Chart.js


document.addEventListener('DOMContentLoaded', function () {
  if (typeof categoriesData === 'undefined' || !Array.isArray(categoriesData) || categoriesData.length === 0) {
    console.warn('No category data for chart.');
    return;
  }

  const sortedCategories = [...categoriesData].sort((a, b) => {
    const aAmount = (typeof a.amount !== 'undefined' && !isNaN(a.amount)) ? Number(a.amount) : 0;
    const bAmount = (typeof b.amount !== 'undefined' && !isNaN(b.amount)) ? Number(b.amount) : 0;
    return bAmount - aAmount;
  });

  const container = document.getElementById('categories_chart');
  if (!container) return;
  const chartHeight = Math.max(180, sortedCategories.length * 32);
  // Use a scrollable container to ensure full chart and labels are visible
  // Responsive: full width for desktop, 100vw for mobile
  const isMobile = window.innerWidth <= 600;
  // Calculate a min-width for the canvas so that if there are many categories, the chart will overflow and show a scrollbar
  // Each bar needs about 80px for label, bar, and numbers; minimum 400px for a more compact look
  const minCanvasWidth = Math.max(400, sortedCategories.length * 80);
  container.innerHTML = `
    <div style="width:${isMobile ? '100vw' : '100%'};margin:0;padding:0;overflow-x:auto;position:relative;display:flex;justify-content:center;align-items:center;">
    <canvas id=\"categoriesBarChart\" style=\"height:${chartHeight}px;width:${isMobile ? 'auto' : '100%'};min-width:${minCanvasWidth}px;display:block;overflow:visible;\"></canvas>
    </div>
  `;

  const canvas = document.getElementById('categoriesBarChart');
  if (!canvas) return;
  canvas.height = chartHeight;
  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  const labels = sortedCategories.map(c => c.category || '');
  // Restore standard left-aligned bars for a professional look
  const soldData = sortedCategories.map(c => (typeof c.amount !== 'undefined' && !isNaN(c.amount)) ? Number(c.amount) : 0);
  const maxTotal = Math.max(...soldData) * 2.0 || 1;
  const remainingData = soldData.map(v => Math.max(0, maxTotal - v));

  // Detect dark mode using body or html attribute/class or CSS media query
  function isDarkMode() {
    // Try CSS variable first
    const root = document.documentElement;
    const darkVar = getComputedStyle(root).getPropertyValue('--bs-body-bg');
    if (darkVar && (darkVar.includes('rgb(18, 18, 18)') || darkVar.includes('#121212'))) return true;
    // Try class or attribute
    if (document.body.classList.contains('dark') || root.classList.contains('dark') || document.body.getAttribute('data-theme') === 'dark' || root.getAttribute('data-theme') === 'dark') return true;
    // Fallback to media query
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  // Use CSS variables from dashboard index page for bar colors, text, and border
  function getVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name);
    return v && v.trim() ? v.trim() : fallback;
  }
  const chartColors = [
    getVar('--cat-bar-1', '#f59e42'),
    getVar('--cat-bar-2', '#3b82f6'),
    getVar('--cat-bar-3', '#a16207'),
    getVar('--cat-bar-4', '#be185d'),
    getVar('--cat-bar-5', '#059669'),
    getVar('--cat-bar-6', '#f43f5e'),
    getVar('--cat-bar-7', '#6366f1'),
    getVar('--cat-bar-8', '#eab308'),
    getVar('--cat-bar-9', '#b91c1c'),
    getVar('--cat-bar-10', '#0e7490'),
    getVar('--cat-bar-11', '#fbbf24'),
    getVar('--cat-bar-12', '#7c3aed'),
    getVar('--cat-bar-13', '#22d3ee'),
    getVar('--cat-bar-14', '#f472b6'),
    getVar('--cat-bar-15', '#84cc16'),
    getVar('--cat-bar-16', '#facc15'),
    getVar('--cat-bar-17', '#a21caf'),
  ];
  const gridColor = getVar('--cat-bar-border', '#eee');
  // Use isDarkMode() for tickColor
  const tickColor = isDarkMode() ? '#fff' : '#222';
  const labelColor = '#fff';

  // Register Chart.js plugins if not already registered
  if (window.Chart && window.ChartDataLabels) {
    if (!Chart._datalabelsRegistered) {
      try {
        Chart.register(window.ChartDataLabels);
        Chart._datalabelsRegistered = true;
      } catch (e) {
        console.warn('ChartDataLabels already registered or failed to register:', e);
      }
    }
  } else {
    console.warn('ChartDataLabels plugin is missing! Datalabels will not show.');
  }

  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'مباع',
          data: soldData,
          backgroundColor: chartColors.slice(0, soldData.length),
          borderWidth: 0,
          borderRadius: 25,
          datalabels: {
            display: false,
          },
        },
        {
          label: 'الباقي',
          data: remainingData,
          backgroundColor: 'rgba(226, 232, 240, 0.8)',
          borderWidth: 0,
          borderRadius: 25,
          datalabels: {
            display: false,
          },
        },
      ],
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      layout: {
        padding: {
          left: 0,
          right: 0,
          top: 20,
          bottom: 20
        },
      },
      plugins: {
        legend: { display: false },
        title: { display: false },
        tooltip: { enabled: false },
        customFullBarLabel: {
          enabled: true
        },
      },
      scales: {
        x: {
          stacked: true,
          display: false,
          min: 0,
          max: maxTotal,
          barPercentage: 0.07,
          categoryPercentage: 0.07,
          maxBarThickness: 6,
        },
        y: {
          stacked: true,
          grid: { display: false },
          position: 'left',
          ticks: {
              font: function() {
                // Responsive font size for mobile
                const isMobile = window.innerWidth <= 600;
                return {
                  size: isMobile ? 12 : 16,
                  family: 'Cairo, Tahoma, Arial, sans-serif',
                  weight: 'bold',
                };
              },
            callback: function(value, index, ticks) {
              // Show category name only on left
              const cat = sortedCategories[index];
              return cat.category;
            },
            align: 'center',
              // Use a unified purple color for both light and dark modes
              color: '#7168EE',
            mirror: false,
            padding: 10,
            crossAlign: 'center',
          },
        },
      },
    },
  });

  // Custom plugin to draw only total sales and percent inside the bar, and weights on the right
  Chart.register({
    id: 'customFullBarLabel',
    afterDatasetsDraw(chart, args, pluginOptions) {
      if (!pluginOptions.enabled) return;
      const {ctx, chartArea, scales} = chart;
      const meta = chart.getDatasetMeta(0); // 'مباع' dataset
      const xScale = scales.x;
      const yScale = scales.y;
      const totalSales = soldData.reduce((a, b) => a + b, 0) || 1;
      ctx.save();
      // Use purple for total sales, black/white for category name and weight
      let salesColor = '#7168EE';
      let labelColor = isDarkMode() ? '#fff' : '#222';
      meta.data.forEach((bar, i) => {
        if (!bar) return;
        const y = bar.y;
        const cat = sortedCategories[i];
        // Draw total sales value, centered in the bar
        const barLeft = xScale.left;
        const barRight = xScale.right;
        const barWidth = barRight - barLeft;
        const labelX = barLeft + barWidth / 2;
        const sold = soldData[i];
        let soldLabel = (sold !== null && sold !== undefined) ? Number(sold).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
        ctx.font = 'bold 16px Cairo, Tahoma, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.save();
        ctx.fillStyle = salesColor;
        ctx.shadowColor = 'rgba(0,0,0,0.08)';
        ctx.shadowBlur = 2;
        ctx.fillText(soldLabel, labelX, y);
        ctx.restore();

        // Draw weight value, centered inside the bar
        let weightRaw = typeof cat.weight !== 'undefined' ? Number(cat.weight) : null;
        if (weightRaw !== null && !isNaN(weightRaw)) {
          let weightText = weightRaw.toFixed(2);
          ctx.font = 'bold 14px Cairo, Tahoma, Arial, sans-serif';
          ctx.textAlign = 'left';
          ctx.textBaseline = 'middle';
          ctx.fillStyle = '#fff'; // white
          ctx.save();
          ctx.shadowColor = 'rgba(0,0,0,0.08)';
          ctx.shadowBlur = 1;
          // Always stick weight to the left edge of the bar
          ctx.fillText(weightText, bar.base + 8, y);
          ctx.restore();
        }
      });
      ctx.restore();
    }
  });
});
