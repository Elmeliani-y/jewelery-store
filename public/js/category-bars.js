// Custom category bar visualization for dashboard
// This script renders category bars with value, percentage, and total weight

// Custom category bar visualization for dashboard (for "مبيعات حسب صنف" only)
document.addEventListener('DOMContentLoaded', function () {
    console.log('categoriesData:', typeof categoriesData, categoriesData);
    const container = document.getElementById('categories_chart');
    if (!container) return;
    // Guard: If categoriesData is not an array or empty, show message and return
    if (!Array.isArray(categoriesData) || categoriesData.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-3">لا توجد بيانات لعرض الرسم البياني.</div>';
        return;
    }
    // Defensive: filter out invalid objects
    const validCategories = categoriesData.filter(c => c && typeof c === 'object' && !Array.isArray(c) && (c.value !== undefined || c.amount !== undefined));
    if (validCategories.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-3">لا توجد بيانات صالحة لعرض الرسم البياني.</div>';
        return;
    }
    container.innerHTML = '';
    // Support both 'value' and 'amount' keys for compatibility
    const getValue = c => parseFloat(c.value !== undefined ? c.value : c.amount);
    const getWeight = c => parseFloat(c.weight || 0);
    const totalSales = validCategories.reduce((sum, c) => sum + (getValue(c) || 0), 0);
    const totalWeight = validCategories.reduce((sum, c) => sum + (getWeight(c) || 0), 0);
    const maxWeight = Math.max(...validCategories.map(c => getWeight(c) || 0), 1);
    // Use CSS variables for colors
    const getVar = (v, fallback) => getComputedStyle(document.documentElement).getPropertyValue(v) || fallback;
    const colors = [
        getVar('--cat-bar-1', '#f59e42'),
        getVar('--cat-bar-2', '#3b82f6'),
        getVar('--cat-bar-3', '#a16207'),
        getVar('--cat-bar-4', '#be185d'),
        getVar('--cat-bar-5', '#059669'),
        getVar('--cat-bar-6', '#f43f5e'),
        getVar('--cat-bar-7', '#6366f1'),
        getVar('--cat-bar-8', '#eab308'),
    ];
    const textColor = getVar('--cat-bar-text', getVar('--main-text-color', '#222'));
    const borderColor = getVar('--cat-bar-border', '#ccc');
    validCategories.forEach((cat, i) => {
        const valueNum = getValue(cat) || 0;
        const weightNum = getWeight(cat) || 0;
        const percent = totalSales > 0 ? (valueNum / totalSales * 100) : 0;
        // Bar width and color intensity based on weight
        const barWidth = (weightNum / maxWeight) * 100;
        // Color intensity (opacity) based on weight percent
        const baseColor = colors[i % colors.length];
        const opacity = (weightNum > 0 && maxWeight > 0) ? (0.3 + 0.7 * (weightNum / maxWeight)) : 0.3;
        const row = document.createElement('div');
        row.style.display = 'flex';
        row.style.alignItems = 'center';
        row.style.marginBottom = '18px';
        row.style.width = '100%';
        row.style.gap = '0.5em';
        // Category name
        const name = document.createElement('div');
        name.textContent = cat.name || cat.category || '';
        name.style.width = '20%';
        name.style.textAlign = 'right';
        name.style.fontSize = 'clamp(0.9em, 2.5vw, 1.1em)';
        name.style.color = textColor;
        name.style.marginLeft = '0.5em';
        row.appendChild(name);
        // Bar container
        const barContainer = document.createElement('div');
        barContainer.style.background = 'transparent';
        barContainer.style.border = `3px solid ${borderColor}`;
        barContainer.style.borderRadius = '22px';
        barContainer.style.height = '2.2em';
        barContainer.style.width = '100%';
        barContainer.style.position = 'relative';
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
        let countStr = (cat.count !== undefined && !isNaN(cat.count)) ? cat.count : '';
        if (totalSales > 0) {
            valueText.textContent = `${valueStr} | ${percent.toFixed(2)}%${countStr !== '' ? ' | ' + countStr : ''}`;
        } else {
            valueText.textContent = `${valueStr}${countStr !== '' ? ' | ' + countStr : ''}`;
        }
        valueText.style.position = 'relative';
        valueText.style.zIndex = 2;
        valueText.style.width = '100%';
        valueText.style.textAlign = 'center';
        valueText.style.fontWeight = 'bold';
        valueText.style.color = textColor;
        valueText.style.fontSize = 'clamp(1em, 3vw, 1.3em)';
        valueText.style.letterSpacing = '0.01em';
        valueText.style.wordBreak = 'break-word';
        valueText.style.lineHeight = '1.1';
        valueText.style.display = 'flex';
        valueText.style.flexDirection = 'column';
        valueText.style.justifyContent = 'center';
        valueText.style.alignItems = 'center';
        barContainer.appendChild(valueText);
        row.appendChild(barContainer);
        // Show weight at the end
        const weight = document.createElement('div');
        let weightStr = (!isNaN(weightNum) && weightNum !== 0) ? weightNum.toLocaleString() + ' جم' : '-';
        weight.textContent = weightStr;
        weight.style.width = '18%';
        weight.style.textAlign = 'left';
        weight.style.fontSize = 'clamp(0.9em, 2.5vw, 1.1em)';
        weight.style.color = textColor;
        weight.style.marginRight = '18px';
        row.appendChild(weight);
        container.appendChild(row);
    });
});
