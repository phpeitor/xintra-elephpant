(function () {
  "use strict";

 /* Sales Overview */
 var options = {
  series: [{
    name: 'Growth',
    type: "column",
    data: [140, 120, 190, 364, 140, 230, 166, 340, 260, 260, 120, 320]
  }, {
    name: "Profit",
    type: "area",
    data: [180, 620, 476, 220, 520, 680, 435, 515, 638, 454, 525, 230],
  }, {
    name: "Sales",
    type: "line",
    data: [200, 330, 110, 130, 380, 420, 580, 335, 375, 638, 454, 480],
  }],
  chart: {
    redrawOnWindowResize: true,
    height: 318,
    type: 'bar',
    toolbar: {
      show: false
    },
    dropShadow: {
      enabled: true,
      enabledOnSeries: undefined,
      top: 7,
      left: 0,
      blur: 1,
      color: ["transparent", 'transparent', 'rgb(227, 84, 212)'],
      opacity: 0.05,
    },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '18%',
      borderRadius: 2
    },
  },
  grid: {
    borderColor: '#f1f1f1',
    strokeDashArray: 3
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    width: [0, 2, 2],
    curve: "smooth",
  },
  legend: {
    show: true,
    fontSize: "12px",
    position: 'bottom',
    horizontalAlign: 'center',
    fontWeight: 500,
    height: 40,
    offsetX: 0,
    offsetY: 10,
    labels: {
      colors: '#9ba5b7',
    },
    markers: {
      width: 7,
      height: 7,
      shape: "circle",
      size: 3.5,
      strokeWidth: 0,
      strokeColor: '#fff',
      fillColors: undefined,
      radius: 12,
      offsetX: 0,
      offsetY: 0
    },
  },
  colors: ['rgba(var(--primary-rgb))', "rgba(119, 119, 142, 0.05)", 'rgb(227, 84, 212)'],
  yaxis: {
    title: {
      style: {
        color: '#adb5be',
        fontSize: '14px',
        fontFamily: 'poppins, sans-serif',
        fontWeight: 600,
        cssClass: 'apexcharts-yaxis-label',
      },
    },
    labels: {
      formatter: function (y) {
        return y.toFixed(0) + "";
      },
      show: true,
      style: {
        colors: "#8c9097",
        fontSize: '11px',
        fontWeight: 600,
        cssClass: 'apexcharts-xaxis-label',
      },
    }
  },
  xaxis: {
    type: "month",
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Dec'],
    axisBorder: {
      show: true,
      color: 'rgba(119, 119, 142, 0.05)',
      offsetX: 0,
      offsetY: 0,
    },
    axisTicks: {
      show: true,
      borderType: 'solid',
      color: 'rgba(119, 119, 142, 0.05)',
      width: 6,
      offsetX: 0,
      offsetY: 0
    },
    labels: {
      rotate: -90,
      style: {
        colors: "#8c9097",
        fontSize: '11px',
        fontWeight: 600,
        cssClass: 'apexcharts-xaxis-label',
      },
    }
  },
  fill: {
    opacity: 1
  },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: function (y) {
        if (typeof y !== "undefined") {
          return y.toFixed(0) + "%";
        }
        return y;
      },
    },
  },
  fill: {
    colors: undefined,
    opacity: 0.025,
    type: ['solid', 'solid'],
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.5,
      gradientToColors: ['#fdc530'],
      inverseColors: true,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [0, 50, 100],
      colorStops: ['#fdc530']
    }
  }
}

var chart = new ApexCharts(document.querySelector("#sales-overview"), options);
chart.render();
/* Sales Overview */

   /* Order Statistics */
   var options = {
    series: [1754, 634, 878, 470],
    labels: ["Delivered", "Cancelled", "Pending", "Returned"],
    chart: {
      height: 207,
      type: 'donut',
    },
    dataLabels: {
      enabled: false,
    },
    legend: {
      show: true,
      position: 'bottom',
      horizontalAlign: 'center',
      height: 52,
      markers: {
        width: 8,
        height: 8,
        radius: 2,
        shape: "circle",
        size: 4,
        strokeWidth: 0
      },
      offsetY: 10,
    },
    stroke: {
      show: true,
      curve: 'smooth',
      lineCap: 'round',
      colors: "#fff",
      width: 0,
      dashArray: 0,
    },
    plotOptions: {
      pie: {
        startAngle: -90,
        endAngle: 90,
        offsetY: 10,
        expandOnClick: false,
        donut: {
          size: '80%',
          background: 'transparent',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '20px',
              color: '#495057',
              offsetY: -25
            },
            value: {
              show: true,
              fontSize: '15px',
              color: undefined,
              offsetY: -20,
              formatter: function (val) {
                return val + "%"
              }
            },
            total: {
              show: true,
              showAlways: true,
              label: 'Total',
              fontSize: '22px',
              fontWeight: 600,
              color: '#495057',
            }

          }
        }
      }
    },
    grid: {
      padding: {
        bottom: -100
      }
    },
    colors: ["rgba(var(--primary-rgb))", "rgba(227, 84, 212, 1)", "rgba(255, 93, 159, 1)", "rgba(255, 142, 111, 1)"],
  };
  var chart = new ApexCharts(document.querySelector("#orders"), options);
  chart.render();
  /* Order Statistics */

  /* Sales Statistics */
  var options = {
    series: [{
      name: 'Total',
      type: 'bar',
      data: [80, 90, 59, 86, 120, 165, 115]
    }, {
      name: 'This Year',
      type: 'bar',
      data: [55, 25, 25, 165, 75, 64, 70]
    }, {
      name: 'Last Year',
      type: 'bar',
      data: [71, 97, 72, 52, 73, 51, 71]
    }
    ],
    chart: {
      height: 265,
      type: 'line',
      stacked: {
        enabled: true,
      },
      toolbar: {
        show: false,
      }
    },
    grid: {
      borderColor: '#f1f1f1',
      strokeDashArray: 3
    },
    legend: {
      show: true,
      position: 'bottom',
      horizontalAlign: 'center',
      markers: {
        shape: "circle",
        size: 4,
        strokeWidth: 0
      },
    },
    stroke: {
      curve: 'smooth',
      width: [0],
    },
    plotOptions: {
      bar: {
        columnWidth: "30%",
        borderRadius: [3],
        borderRadiusWhenStacked: "all",
      }
    },
    colors: ["rgba(var(--primary-rgb))", "rgba(227, 84, 212, 1)", "rgba(255, 142, 111, 1)"],
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  };
  var chart1 = new ApexCharts(document.querySelector("#sales-statistics"), options);
  chart1.render();
  /* Sales Statistics */

   // linegraph1
   var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 30, 10, 35, 26, 31, 14, 22, 40, 12]
    }],
    yaxis: {
        min: 0
    },
    colors: ['rgb(126, 103, 221)'],
};
var chart = new ApexCharts(document.querySelector("#line-graph1"), options);
chart.render();

//linegarph2
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 20, 15, 25, 15, 25, 6, 25, 32, 15]
    }],
    yaxis: {
        min: 0
    },
    colors: ['rgb(227, 84, 212)'],
};
var chart = new ApexCharts(document.querySelector("#line-graph2"), options);
chart.render();

//linegraph3
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 10, 30, 12, 16, 25, 4, 35, 26, 15]
    }],
    yaxis: {
        min: 0
    },
    colors: ['rgb(255, 93, 159)'],
};
var chart = new ApexCharts(document.querySelector("#line-graph3"), options);
chart.render();

//linegraph4
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 12, 19, 26, 10, 18, 8, 17, 35, 14]
    }],
    yaxis: {
        min: 0
    },
    colors: ['rgb(255, 142, 111)'],
};
var chart = new ApexCharts(document.querySelector("#line-graph4"), options);
chart.render();

//linegraph5
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 12, 19, 17, 35, 14, 26, 10, 18, 8]
    }],
    yaxis: {
        min: 0
    },
    colors: ['rgb(158, 92, 247)'],
};
var chart = new ApexCharts(document.querySelector("#line-graph5"), options);
chart.render();

})();