// Custom category bar visualization for dashboard
// This script renders category bars with value, percentage, and total weight

// Custom category bar visualization for dashboard (for "مبيعات حسب صنف" only)
document.addEventListener('DOMContentLoaded', function () {
    if (typeof categoriesData === 'undefined' || !Array.isArray(categoriesData)) return;
    const container = document.getElementById('categories_chart');
    if (!container) return;
    container.innerHTML = '';
    const totalSales = categoriesData.reduce((sum, c) => sum + (parseFloat(c.value) || 0), 0);
    const totalWeight = categoriesData.reduce((sum, c) => sum + (parseFloat(c.weight) || 0), 0);
    const maxWeight = Math.max(...categoriesData.map(c => parseFloat(c.weight) || 0), 1);
    const colors = ['#f59e42', '#3b82f6', '#a16207', '#be185d', '#059669', '#f43f5e', '#6366f1', '#eab308'];
    categoriesData.forEach((cat, i) => {
        const valueNum = parseFloat(cat.value) || 0;
        const weightNum = parseFloat(cat.weight) || 0;
        const percent = totalSales > 0 ? (valueNum / totalSales * 100) : 0;
        // Bar width and color intensity based on weight
        const barWidth = (weightNum / maxWeight) * 100;
        // Color intensity (opacity) based on weight percent
        const baseColor = colors[i % colors.length];
        const opacity = (weightNum > 0 && maxWeight > 0) ? (0.3 + 0.7 * (weightNum / maxWeight)) : 0.3;
        const color = baseColor + (baseColor.startsWith('#') ? Math.round(opacity * 255).toString(16).padStart(2, '0') : '');
        const row = document.createElement('div');
        row.style.display = 'flex';
        row.style.alignItems = 'center';
        row.style.marginBottom = '28px';
        // Category name
        const name = document.createElement('div');
        name.textContent = cat.name;
        name.style.width = '70px';
        name.style.textAlign = 'right';
        name.style.fontSize = '1.2em';
        name.style.color = '#fff';
        name.style.marginLeft = '18px';
        row.appendChild(name);
        // Bar container
        const barContainer = document.createElement('div');
        barContainer.style.background = 'transparent';
        barContainer.style.border = '4px solid #fff';
        barContainer.style.borderRadius = '22px';
        barContainer.style.height = '44px';
        barContainer.style.width = '320px';
        barContainer.style.position = 'relative';
        barContainer.style.margin = '0 18px';
        barContainer.style.display = 'flex';
        barContainer.style.alignItems = 'center';
        // Filled bar
        const barFill = document.createElement('div');
        barFill.style.background = baseColor;
        barFill.style.opacity = opacity;
        barFill.style.height = '100%';
        barFill.style.width = barWidth + '%';
        barFill.style.borderRadius = '22px';
        barFill.style.position = 'absolute';
        barFill.style.left = 0;
        barFill.style.top = 0;
        barFill.style.zIndex = 1;
        barContainer.appendChild(barFill);
        // Value and percent text
        const valueText = document.createElement('div');
        let valueStr = (!isNaN(valueNum) && valueNum !== 0) ? valueNum.toLocaleString() : '0';
        valueText.textContent = `${valueStr} | ${percent.toFixed(2)}%`;
        valueText.style.position = 'relative';
        valueText.style.zIndex = 2;
        valueText.style.width = '100%';
        valueText.style.textAlign = 'center';
        valueText.style.fontWeight = 'bold';
        valueText.style.color = '#fff';
        valueText.style.fontSize = '1.3em';
        barContainer.appendChild(valueText);
        row.appendChild(barContainer);
        // Total weight
        const weight = document.createElement('div');
        let weightStr = (!isNaN(weightNum) && weightNum !== 0) ? weightNum.toLocaleString() + ' جم' : '-';
        weight.textContent = weightStr;
        weight.style.width = '80px';
        weight.style.textAlign = 'left';
        weight.style.fontSize = '1.2em';
        weight.style.color = '#fff';
        weight.style.marginRight = '18px';
        row.appendChild(weight);
        container.appendChild(row);
    });
});
