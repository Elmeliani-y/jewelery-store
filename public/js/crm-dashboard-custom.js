console.log('Loaded crm-dashboard-custom.js - version 2025-12-22');
// CRM Dashboard Charts - Updated Version
// Include this file after ApexCharts library

document.addEventListener('DOMContentLoaded', function() {
    
// Total Sales
var totalSalesOptions = {
    series: [{
        name: "Sales",
        data: (Array.isArray(monthlyRevenueData) && monthlyRevenueData.length) ? monthlyRevenueData.map(m => parseFloat(m.sales)) : [10, 15, 9, 18, 22, 17, 25, 20, 15, 10, 12, 8]
    }],
    chart: {
        height: 45,
        type: "area",
        sparkline: { enabled: true },
        animations: { enabled: false },
        dropShadow: {
            enabled: true,
            top: 10,
            left: 0,
            bottom: 10,
            blur: 2,
            color: "#f0f4f7",
            opacity: 0.3
        }
    },
    colors: ["#c26316"],
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.5,
            stops: [0, 90, 100]
        }
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: true, style: { colors: ['#7168EE'] } },
    grid: { show: false },
    xaxis: {
        labels: { show: false },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { show: false },
    stroke: { curve: "smooth", width: 1 }
};
if(document.querySelector("#total_sales")) {
    new ApexCharts(document.querySelector("#total_sales"), totalSalesOptions).render();
}

// Total Orders
var totalOrdersOptions = {
    series: [{
        name: "Orders",
        data: (Array.isArray(monthlyRevenueData) && monthlyRevenueData.length) ? monthlyRevenueData.map(m => parseFloat(m.sales) - parseFloat(m.expenses)) : [15, 20, 16, 27, 34, 27, 35, 28, 20, 27, 32, 15]
    }],
    chart: {
        height: 45,
        type: "area",
        sparkline: { enabled: true },
        animations: { enabled: false },
        dropShadow: {
            enabled: true,
            top: 10,
            left: 0,
            bottom: 10,
            blur: 2,
            color: "#f0f4f7",
            opacity: 0.3
        }
    },
    colors: ["#46B277"],
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.5,
            stops: [0, 90, 100]
        }
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: true, style: { colors: ['#7168EE'] } },
    grid: { show: false },
    xaxis: {
        labels: { show: false },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { show: false },
    stroke: { curve: "smooth", width: 1 }
};
if(document.querySelector("#total_orders")) {
    new ApexCharts(document.querySelector("#total_orders"), totalOrdersOptions).render();
}

// New Customers
var newCustomersOptions = {
    series: [{
        name: "Customers",
        data: (Array.isArray(monthlyRevenueData) && monthlyRevenueData.length) ? monthlyRevenueData.map(m => parseFloat(m.sales)) : [12, 25, 18, 40, 28, 35, 21, 38, 32, 15, 45, 29]
    }],
    chart: {
        height: 45,
        type: "area",
        sparkline: { enabled: true },
        animations: { enabled: false },
        dropShadow: {
            enabled: true,
            top: 10,
            left: 0,
            bottom: 10,
            blur: 2,
            color: "#f0f4f7",
            opacity: 0.3
        }
    },
    colors: ["#E7366B"],
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.5,
            stops: [0, 90, 100]
        }
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: true, style: { colors: ['#7168EE'] } },
    grid: { show: false },
    xaxis: {
        labels: { show: false },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { show: false },
    stroke: { curve: "smooth", width: 1 }
};
if(document.querySelector("#new_customers")) {
    new ApexCharts(document.querySelector("#new_customers"), newCustomersOptions).render();
}

// Total Income
var totalIncomeOptions = {
    series: [{
        name: "Income",
        data: (Array.isArray(monthlyRevenueData) && monthlyRevenueData.length) ? monthlyRevenueData.map(m => parseFloat(m.sales) - parseFloat(m.expenses)) : [14, 19, 12, 24, 30, 21, 27, 23, 18, 13, 16, 11]
    }],
    chart: {
        height: 45,
        type: "area",
        sparkline: { enabled: true },
        animations: { enabled: false },
        dropShadow: {
            enabled: true,
            top: 10,
            left: 0,
            bottom: 10,
            blur: 2,
            color: "#f0f4f7",
            opacity: 0.3
        }
    },
    colors: ["#7168EE"],
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.5,
            stops: [0, 90, 100]
        }
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: true, style: { colors: ['#7168EE'] } },
    grid: { show: false },
    xaxis: {
        labels: { show: false },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { show: false },
    stroke: { curve: "smooth", width: 1 }
};
if(document.querySelector("#total_income")) {
    new ApexCharts(document.querySelector("#total_income"), totalIncomeOptions).render();
}

// Total Returns
var totalReturnsOptions = {
    series: [{
        name: "Returns",
        data: (typeof monthlyRevenueData !== 'undefined' && monthlyRevenueData.length) ? monthlyRevenueData.map(() => (typeof salesAmount !== 'undefined' ? (parseFloat(salesAmount) * 0.02) : 0)) : [25, 30, 23, 30, 36, 27, 32, 45, 34, 34, 30, 19]
    }],
    chart: {
        height: 45,
        type: "area",
        sparkline: { enabled: true },
        animations: { enabled: false },
        dropShadow: {
            enabled: true,
            top: 10,
            left: 0,
            bottom: 10,
            blur: 2,
            color: "#f0f4f7",
            opacity: 0.3
        }
    },
    colors: ["#E77636"],
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.5,
            stops: [0, 90, 100]
        }
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: true, style: { colors: ['#7168EE'] } },
    grid: { show: false },
    xaxis: {
        labels: { show: false },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { show: false },
    stroke: { curve: "smooth", width: 1 }
};
if(document.querySelector("#total_returns")) {
    new ApexCharts(document.querySelector("#total_returns"), totalReturnsOptions).render();
}


// Sales Overtime - KEPT AS AREA CHART
if(Array.isArray(dailySalesData) && dailySalesData.length > 0) {
    // Helper to reduce x-axis label overlap
    function getXAxisLabels(dates) {
        const maxLabels = 10;
        if (dates.length <= maxLabels) return dates;
        const step = Math.ceil(dates.length / maxLabels);
        return dates.map((d, i) => (i % step === 0 ? d : ''));
    }
    var salesOvertimeOptions = {
        series: [
            {
                name: "المبيعات",
                data: dailySalesData.map(item => parseFloat(item.amount))
            }
        ],
        chart: {
            type: 'area',
            height: 290,
            toolbar: { show: false }
        },
            colors: ['#7168EE'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: getXAxisLabels(dailySalesData.map(item => {
                const date = new Date(item.date);
                return date.getDate() + '/' + (date.getMonth() + 1);
            })),
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                    style: { colors: '#7168EE' },
                rotate: -45,
                rotateAlways: false
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return val.toLocaleString('fr-MA');
                },
                    style: { colors: '#7168EE' }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            theme: "light",
            marker: { show: true },
            y: {
                formatter: (value) => value.toLocaleString('fr-MA') + ''
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            markers: {
                width: 12,
                height: 12,
                radius: 12
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3
        }
    };
    if(document.querySelector("#sales-overtime")) {
        new ApexCharts(document.querySelector("#sales-overtime"), salesOvertimeOptions).render();
    }
}

// Revenue Statistics - Sales vs Expenses (12 months)
if(typeof monthlyRevenueData !== 'undefined') {
    // If empty, fill with 12 months of zeros for both sales and expenses
    let data = monthlyRevenueData;
    if (!Array.isArray(data) || data.length === 0) {
        // Generate last 12 months labels
        let months = [];
        let now = new Date();
        for (let i = 11; i >= 0; i--) {
            let d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            months.push(d.toLocaleString('ar-EG', { month: 'short', year: 'numeric' }));
        }
        data = months.map(m => ({ month: m, sales: 0, expenses: 0 }));
    }
    var revenueOptions = {
        series: [
            { name: 'المبيعات', type: 'line', data: Array.isArray(data) ? data.map(item => parseFloat(item.sales) || 0) : [] },
            { name: 'المصروفات', type: 'line', data: Array.isArray(data) ? data.map(item => parseFloat(item.expenses) || 0) : [] }
        ],
        chart: { type: 'line', height: 200, toolbar: { show: false } },
        grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
        colors: ["#27ebb0", "#E77636"],
        stroke: { width: 3, curve: 'smooth' },
        dataLabels: { enabled: false },
        legend: { show: true, position: 'top', horizontalAlign: 'right' },
        markers: {
            size: 6,
            colors: ["#27ebb0", "#E77636"],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: { size: 8 }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return val.toLocaleString('fr-MA');
                },
                    style: { colors: '#7168EE' }
            }
        },
        xaxis: {
            categories: data.map(item => item.month),
                labels: { style: { colors: '#9aa0ac' }, rotate: -45, rotateAlways: false }
        },
        tooltip: {
            shared: true,
            intersect: false,
            theme: "light",
            y: {
                    formatter: function(val) {
                        try { return Number(val).toLocaleString('ar-EG'); } catch(e) { return val; }
                    }
            }
        }
    };
    if(document.querySelector("#revenueCharts")) {
        new ApexCharts(document.querySelector("#revenueCharts"), revenueOptions).render();
    }
}

});
