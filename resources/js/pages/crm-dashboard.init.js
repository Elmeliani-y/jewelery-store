/*
Template Name: Dusty - Responsive Bootstrap 5 Admin Dashboard
Author: Zoyothemes
Version: 1.0.0
Website: https://zoyothemes.com/
File: CRM dashboard init Js
*/

import ApexCharts from "apexcharts";
//  Total Sale
var options = {
  series: [
    {
      name: "Desktops",
      data: [10, 15, 9, 18, 22, 17, 25, 20, 15, 10, 12, 8],
    },
  ],
  chart: {
    height: 45,
    type: "area",
    sparkline: {
      enabled: true,
    },
    animations: {
      enabled: false,
    },
    dropShadow: {
      enabled: true,
      top: 10,
      left: 0,
      bottom: 10,
      blur: 2,
      color: "#f0f4f7",
      opacity: 0.3,
    },
  },
  colors: ["#c26316"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.5,
      opacityTo: 0.5,
      stops: [0, 90, 100],
    },
  },
  tooltip: {
    enabled: false,
  },
  dataLabels: {
    enabled: false,
  },
  grid: {
    show: false,
  },
  xaxis: {
    labels: {
      show: false,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  stroke: {
    curve: "smooth",
    width: 1,
  },
};
var chartOne = new ApexCharts(
  document.querySelector("#total_sales"),
  options
);
chartOne.render();


// Total Order
var options = {
  series: [
    {
      name: "Desktops",
      data: [15, 20, 16, 27, 34, 27, 35, 28, 20, 27, 32, 15],
    },
  ],
  chart: {
    height: 45,
    type: "area",
    sparkline: {
      enabled: true,
    },
    animations: {
      enabled: false,
    },
    dropShadow: {
      enabled: true,
      top: 10,
      left: 0,
      bottom: 10,
      blur: 2,
      color: "#f0f4f7",
      opacity: 0.3,
    },
  },
  colors: ["#46B277"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.5,
      opacityTo: 0.5,
      stops: [0, 90, 100],
    },
  },
  tooltip: {
    enabled: false,
  },
  dataLabels: {
    enabled: false,
  },
  grid: {
    show: false,
  },
  xaxis: {
    labels: {
      show: false,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  stroke: {
    curve: "smooth",
    width: 1,
  },
};
var chartOne = new ApexCharts(
  document.querySelector("#total_orders"),
  options
);
chartOne.render();


// Total Order
var options = {
  series: [
    {
      name: "Desktops",
      data: [12, 25, 18, 40, 28, 35, 21, 38, 32, 15, 45, 29],
    },
  ],
  chart: {
    height: 45,
    type: "area",
    sparkline: {
      enabled: true,
    },
    animations: {
      enabled: false,
    },
    dropShadow: {
      enabled: true,
      top: 10,
      left: 0,
      bottom: 10,
      blur: 2,
      color: "#f0f4f7",
      opacity: 0.3,
    },
  },
  colors: ["#E7366B"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.5,
      opacityTo: 0.5,
      stops: [0, 90, 100],
    },
  },
  tooltip: {
    enabled: false,
  },
  dataLabels: {
    enabled: false,
  },
  grid: {
    show: false,
  },
  xaxis: {
    labels: {
      show: false,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  stroke: {
    curve: "smooth",
    width: 1,
  },
};
var chartOne = new ApexCharts(
  document.querySelector("#new_customers"),
  options
);
chartOne.render();


// Total Income
var options = {
  series: [
    {
      name: "Desktops",
      data: [14, 19, 12, 24, 30, 21, 27, 23, 18, 13, 16, 11],
    },
  ],
  chart: {
    height: 45,
    type: "area",
    sparkline: {
      enabled: true,
    },
    animations: {
      enabled: false,
    },
    dropShadow: {
      enabled: true,
      top: 10,
      left: 0,
      bottom: 10,
      blur: 2,
      color: "#f0f4f7",
      opacity: 0.3,
    },
  },
  colors: ["#7168EE"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.5,
      opacityTo: 0.5,
      stops: [0, 90, 100],
    },
  },
  tooltip: {
    enabled: false,
  },
  dataLabels: {
    enabled: false,
  },
  grid: {
    show: false,
  },
  xaxis: {
    labels: {
      show: false,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  stroke: {
    curve: "smooth",
    width: 1,
  },
};
var chartOne = new ApexCharts(
  document.querySelector("#total_income"),
  options
);
chartOne.render();


// Total Return
var options = {
  series: [
    {
      name: "Desktops",
      data: [25, 30, 23, 30, 36, 27, 32, 45, 34, 34, 30, 19],
    },
  ],
  chart: {
    height: 45,
    type: "area",
    sparkline: {
      enabled: true,
    },
    animations: {
      enabled: false,
    },
    dropShadow: {
      enabled: true,
      top: 10,
      left: 0,
      bottom: 10,
      blur: 2,
      color: "#f0f4f7",
      opacity: 0.3,
    },
  },
  colors: ["#E77636"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.5,
      opacityTo: 0.5,
      stops: [0, 90, 100],
    },
  },
  tooltip: {
    enabled: false,
  },
  dataLabels: {
    enabled: false,
  },
  grid: {
    show: false,
  },
  xaxis: {
    labels: {
      show: false,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  stroke: {
    curve: "smooth",
    width: 1,
  },
};
var chartOne = new ApexCharts(
  document.querySelector("#total_returns"),
  options
);
chartOne.render();


// Top Selling Categories
var options = {
  series: [{
    name: 'Sales',
    data: [72.02, 24.53, 16.47, 18.00]
  }],
  chart: {
    type: "bar",
    height: 385,
    toolbar: {
      show: false
    }
  },
  plotOptions: {
    bar: {
      borderRadius: 8,
      horizontal: true,
      distributed: true,
      barHeight: '70%',
      dataLabels: {
        position: 'top'
      }
    },
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val.toFixed(2) + "%";
    },
    offsetX: 30,
    style: {
      fontSize: '12px',
      colors: ["#304758"]
    }
  },
  colors: ["#7168EE", "#46B277", "#963b68", "#01D4FF"],
  xaxis: {
    categories: ["Fashion", "Electronics", "Automobiles", "Property"],
    labels: {
      style: {
        colors: '#9aa0ac',
        fontSize: '12px'
      }
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: '#9aa0ac'
      }
    }
  },
  grid: {
    borderColor: '#f1f1f1',
    strokeDashArray: 3,
    xaxis: {
      lines: {
        show: true
      }
    },
    yaxis: {
      lines: {
        show: false
      }
    }
  },
  legend: {
    show: false,
  },
  tooltip: {
    theme: "light",
    y: {
      formatter: function (val) {
        return val.toFixed(2) + "%";
      }
    }
  }
};

var chart = new ApexCharts(document.querySelector("#categories_chart"), options);
chart.render();

// Sales Overtime
var options = {
  series: [
    {
      name: "Revenue",
      data: [9000, 15000, 6000, 9500, 16000, 8500, 14000, 19000, 12000, 8500, 15000, 18564]
    },
    {
      name: "Order",
      data: [5000, 3000, 13000, 5000, 9000, 13500, 19000, 9500, 3000, 14000, 10500, 8500] 
    }
  ],
  chart: {
    type: 'area',
    height: 290,
    toolbar: {
      show: false
    }
  },
  colors: ['#7168EE', '#27ebb0'],
  dataLabels: {
    enabled: false
  },
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
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    labels: {
      style: {
        colors: '#9aa0ac'
      }
    }
  },
  yaxis: {
    labels: {
      formatter: function (val) {
        return `$${val / 1000}K`;
      },
      style: {
        colors: '#9aa0ac'
      }
    },
    min: 0,
    max: 20000,
    tickAmount: 4
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: "light",
    marker: {
      show: true,
    },
    y: {
      formatter: (value) => `$${value.toLocaleString()}`,
    },
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

var chart = new ApexCharts(document.querySelector("#sales-overtime"), options);
chart.render();


// Revenue Statistics
var options = {
  series: [
    {
      name: 'Net Profit',
      type: 'line',
      data: [60, 65, 80, 85, 56, 50, 40],
    }, 
    {
      name: 'Sales',
      type: 'line',
      data: [30, 50, 40, 36, 86, 32, 90],
    }
  ],
  chart: {
    type: 'line',
    height: 200,
    toolbar: {
      show: false
    }
  },
  grid: {
    borderColor: '#f1f1f1',
    strokeDashArray: 3
  },
  colors: ["#27ebb0" ,"#E77636"],
  stroke: {
    width: 3,
    curve: 'smooth'
  },
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  markers: {
    size: 5,
    colors: ["#27ebb0" ,"#E77636"],
    strokeColors: '#fff',
    strokeWidth: 2,
    hover: {
      size: 7,
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: '#9aa0ac'
      }
    }
  },
  xaxis: {
    type: 'category',
    categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    axisBorder: {
      show: true,
      color: 'rgba(167, 180, 201 ,0.2)',
      offsetX: 0,
      offsetY: 0,
    },
    labels: {
      style: {
        colors: '#9aa0ac'
      }
    }
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: "light",
  }
};

var chart = new ApexCharts(document.querySelector("#revenueCharts"), options);
chart.render();