/*
Template Name: Dusty - Responsive Bootstrap 5 Admin Dashboard
Author: Zoyothemes
Version: 1.0.0
Website: https://zoyothemes.com/
File: Widgets Js
*/

// 
// Chart Boarding Opened
// 
import ApexCharts from 'apexcharts/dist/apexcharts'
window.ApexCharts = ApexCharts

var options = {
    series: [60],
    chart: {
        height: 90,
        width: 90,
        parentHeightOffset: 0,
        type: 'radialBar'
    },
    plotOptions: {
        radialBar: {
            endAngle: 360,
            offsetX: 20,
            offsetY: [0],
            hollow: {
                margin: 0,
                size: '40%',
            },
            dataLabels: {
                show: false,
            },
            track: {
                show: true,
                background: '#adb5bd'
            },
        },
    },
    stroke: {
        lineCap: "round",
    }
};
var chart = new ApexCharts(document.querySelector("#chart-boarding-opened"), options);
chart.render();

// 
// Chart Boarding clicked
// 
var options = {
    series: [72],
    chart: {
        height: 90,
        width: 90,
        parentHeightOffset: 0,
        type: 'radialBar'
    },
    plotOptions: {
        radialBar: {
            endAngle: 360,
            offsetX: 20,
            offsetY: [0],
            hollow: {
                margin: 0,
                size: '40%',
            },
            dataLabels: {
                show: false,
            },
            track: {
                show: true,
                background: '#adb5bd'
            },
        },
    },
    stroke: {
        lineCap: "round",
    }
};
var chart = new ApexCharts(document.querySelector("#chart-boarding-clicked"), options);
chart.render();


// 
// Chart Boarding converted
// 
var options = {
    chart: {
        type: "line",
        height: 25,
        width: 90,
        sparkline: {
            enabled: true
        },
        animations: {
            enabled: false
        },
    },
    fill: {
        opacity: 1,
    },
    stroke: {
        width: [3],
        lineCap: "round",
        curve: "straight",
    },
    series: [{
        name: "May",
        data: [40, 40, 115, 90, 65, 85, 50, 75, 90, 119]
    }],
    tooltip: {
        theme: 'light'
    },
    xaxis: {
        labels: {
            padding: 0,
        },
        tooltip: {
            enabled: false
        },
        type: 'datetime',
    },
    yaxis: {
        labels: {
            padding: 4
        },
    },
    labels: [
        '2020-06-21', '2020-06-22', '2020-06-23', '2020-06-24', '2020-06-25', '2020-06-26', '2020-06-27', '2020-06-28', '2020-06-29',
    ],
    colors: ["#f59440"],
    legend: {
        show: false,
    },
};
var chart = new ApexCharts(document.querySelector("#chart-boarding-converted"), options);
chart.render();

// 
// Chart Boarding Delivered
//
var options = {
    chart: {
        type: "line",
        height: 25,
        width: 90,
        sparkline: {
            enabled: true
        },
        animations: {
            enabled: false
        },
    },
    fill: {
        opacity: 1,
    },
    stroke: {
        width: [3],
        lineCap: "round",
        curve: "straight",
    },
    series: [{
        name: "May",
        data: [40, 40, 115, 90, 65, 85, 50, 75, 90, 119]
    }],
    tooltip: {
        theme: 'light'
    },
    xaxis: {
        labels: {
            padding: 0,
        },
        tooltip: {
            enabled: false
        },
        type: 'datetime',
    },
    yaxis: {
        labels: {
            padding: 4
        },
    },
    labels: [
        '2020-06-21', '2020-06-22', '2020-06-23', '2020-06-24', '2020-06-25', '2020-06-26', '2020-06-27', '2020-06-28', '2020-06-29',
    ],
    colors: ["#522c8f"],
    legend: {
        show: false,
    },
};
var chart = new ApexCharts(document.querySelector("#chart-boarding-delivered"), options);
chart.render();


// Sales by Region
// var options = {
//     series: [{
//         name: 'India',
//         data: [80, 50, 30, 40, 100, 20],
//     }, {
//         name: 'Australia',
//         data: [20, 30, 40, 80, 20, 80],
//     }, {
//         name: 'Canada',
//         data: [44, 76, 78, 13, 43, 10],
//     }],
//     chart: {
//         type: 'radar',
//         height: 305,
//         parentHeightOffset: 0,
//         dropShadow: {
//             enabled: true,
//             blur: 1,
//             left: 1,
//             top: 1
//         },
//         toolbar: {
//             show: false
//         },
//     },
//     stroke: {
//         width: 1,
//     },
//     fill: {
//         opacity: 0.1
//     },
//     markers: {
//         size: 0,
//         hover: {
//             size: 10
//         }
//     },
//     yaxis: {
//         stepSize: 20
//     },
//     xaxis: {
//         categories: ['2019', '2020', '2021', '2022', '2023', '2024'],
//         labels: {
//             show: true,
//             style: {
//                 colors: ["#001b2f", "#001b2f", "#001b2f", "#001b2f", "#001b2f", "#001b2f"],
//                 fontSize: "13px",
//             }
//         }
//     },
//     colors: ["#963b68", "#f59440", "#2786f1"],
//     dataLabels: {
//         enabled: false,
//         background: {
//             enabled: true,
//         }
//     }
// };
// var chart = new ApexCharts(document.querySelector("#sales-region"), options);
// chart.render();
