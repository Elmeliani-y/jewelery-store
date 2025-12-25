// category-bars.js
// Renders the sales by category chart using Chart.js

document.addEventListener('DOMContentLoaded', function() {
    if (typeof categoriesData === 'undefined' || !Array.isArray(categoriesData) || categoriesData.length === 0) {
        console.warn('No category data for chart.');
        return;
    }
    // Sort categoriesData by amount descending
    const sortedCategories = [...categoriesData].sort((a, b) => {
        const aAmount = (typeof a.amount !== 'undefined' && !isNaN(a.amount)) ? Number(a.amount) : 0;
        const bAmount = (typeof b.amount !== 'undefined' && !isNaN(b.amount)) ? Number(b.amount) : 0;
        return bAmount - aAmount;
    });

    const container = document.getElementById('categories_chart');
    if (!container) return;
    container.innerHTML = `
        <div id="pieChartFlexWrap" style="display:flex;flex-direction:column;align-items:center;justify-content:flex-start;">
            <canvas id="categoriesPieChart" style="display:block;margin:0 auto;width:320px;height:220px;"></canvas>
            <div id="pieChartLegendScroll" style="width:100%;margin-top:4px;max-height:220px;overflow-y:auto;"></div>
        </div>
    `;
    const canvas = document.getElementById('categoriesPieChart');
    // Set fixed size attributes to prevent Chart.js from resizing canvas
    canvas.width = 320;
    canvas.height = 220;
    const ctx = canvas.getContext('2d');
    const labels = sortedCategories.map(c => {
        const cat = c.category || '';
        const weight = (typeof c.weight !== 'undefined' && !isNaN(c.weight)) ? Number(c.weight).toFixed(2) : '';
        const count = typeof c.count !== 'undefined' ? c.count : '';
        return `${cat} (وزن: ${weight}, عدد: ${count})`;
    });
    const data = sortedCategories.map(c => (typeof c.amount !== 'undefined' && !isNaN(c.amount)) ? Number(c.amount).toFixed(2) : 0);

    const chartColors = [
        '#f59e42', '#3b82f6', '#a16207', '#be185d', '#059669', '#f43f5e', '#6366f1', '#eab308',
        '#b91c1c', '#0e7490', '#fbbf24', '#7c3aed', '#22d3ee', '#f472b6', '#84cc16', '#facc15', '#a21caf'
    ];
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'المبيعات حسب الصنف',
                data: data,
                backgroundColor: chartColors,
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false // Hide built-in legend
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const i = context.dataIndex;
                            const c = categoriesData[i];
                            const amount = (typeof c.amount !== 'undefined' && !isNaN(c.amount)) ? Number(c.amount).toFixed(2) : '';
                            const weight = (typeof c.weight !== 'undefined' && !isNaN(c.weight)) ? Number(c.weight).toFixed(2) : '';
                            return `${c.category}: المبيعات ${amount}، الوزن ${weight}، العدد ${c.count}`;
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Custom legend: always show all items
    setTimeout(() => {
        const legendDiv = document.getElementById('pieChartLegendScroll');
        if (legendDiv) {
            let html = '<ul id="customLegendList" style="list-style:none;padding:0;margin:0;direction:rtl;font-size:13px;line-height:1.2;column-count:2;column-gap:12px;">';
            sortedCategories.forEach((c, i) => {
                const color = chartColors[i % chartColors.length];
                const cat = c.category || '';
                const weight = (typeof c.weight !== 'undefined' && !isNaN(c.weight)) ? Number(c.weight).toFixed(2) : '';
                const count = typeof c.count !== 'undefined' ? c.count : '';
                html += `<li class="legend-item" data-idx="${i}" style="cursor:pointer;user-select:none;display:flex;align-items:center;padding:0 2px;margin:0;white-space:normal;word-break:break-word;">
                    <span class="legend-color" style="display:inline-block;width:12px;height:12px;background:${color};border-radius:2px;margin-left:4px;"></span>
                    <span class="legend-label">${cat} (وزن: ${weight}, عدد: ${count})</span>
                </li>`;
            });
            html += '</ul>';
            legendDiv.innerHTML = html;

            // Add click event to legend items for toggling visibility
            const chartDataset = chart.data.datasets[0];
            const legendItems = legendDiv.querySelectorAll('.legend-item');
            // Track hidden state
            if (!window._pieLegendHidden) window._pieLegendHidden = {};
            legendItems.forEach((li, idx) => {
                li.addEventListener('click', function() {
                    const i = parseInt(li.getAttribute('data-idx'));
                    // Toggle hidden state
                    window._pieLegendHidden[i] = !window._pieLegendHidden[i];
                    // Set value to 0 if hidden, restore if shown
                    if (window._pieLegendHidden[i]) {
                        chartDataset.data[i] = 0;
                        li.style.opacity = '0.4';
                        li.querySelector('.legend-label').style.textDecoration = 'line-through';
                    } else {
                        chartDataset.data[i] = (typeof sortedCategories[i].amount !== 'undefined' && !isNaN(sortedCategories[i].amount)) ? Number(sortedCategories[i].amount).toFixed(2) : 0;
                        li.style.opacity = '1';
                        li.querySelector('.legend-label').style.textDecoration = 'none';
                    }
                    chart.update();
                });
            });
        }
    }, 200);
});
