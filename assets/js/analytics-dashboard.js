  /*  Start::Followers */
var options = {
    series: [
      {
        data: [1, 20, 15, 35, 30, 25, 55, 45, 65],
      },
    ],
    chart: {
      height: 85,
      width: 100,
      type: 'area',
      zoom: {
          enabled: false
      },
      sparkline: {
          enabled: true
      }
  },
  tooltip: {
      enabled: true,
      x: {
          show: false
      },
      y: {
          title: {
              formatter: function (seriesName) {
                  return ''
              }
          }
      },
      marker: {
          show: false
      }
  },
  dataLabels: {
      enabled: false
  },
  grid: {
      borderColor: 'transparent',
  },
  xaxis: {
      crosshairs: {
          show: false,
      }
  },
  yaxis: { 
    max: 65,
  },
  colors: ["rgba(var(--primary-rgb))"],
  stroke: {
      width: [.75],
  },
  fill: {
    opacity: 0.001,
    type: ['gradient'],
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.5,
      gradientToColors: ['rgba(var(--primary-rgb), 0.1)'],
      inverseColors: true,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [0, 50, 100],
      colorStops: [
        [
            {
                offset: 0,
                color: "rgba(var(--primary-rgb), 0.4)",
                opacity: 1
            },
            {
                offset: 55,
                color: "rgba(var(--primary-rgb), 0.1)",
                opacity: 0.1
            },
            {
                offset: 100,
                color: "rgba(var(--primary-rgb), 0.5)",
                opacity: 0.3
            }
        ],
      ]
    }
  }
  };
  var chart = new ApexCharts(document.querySelector("#chart-21"), options);
  chart.render();
    /*  End::Followers */

    /*  Start::Session Rate */
  var options1 = {
    series: [
      {
        data: [1 , 20, 15, 35, 30, 25, 55, 45, 65],
      },
    ],
    chart: {
      height: 85,
      width: 100,
      type: 'area',
      zoom: {
          enabled: false
      },
      sparkline: {
          enabled: true
      }
  },
  tooltip: {
      enabled: true,
      x: {
          show: false
      },
      y: {
          title: {
              formatter: function (seriesName) {
                  return ''
              }
          }
      },
      marker: {
          show: false
      }
  },
  dataLabels: {
      enabled: false
  },
  grid: {
      borderColor: 'transparent',
  },
  xaxis: {
      crosshairs: {
          show: false,
      }
  },
  yaxis: { 
    max: 65,
  },
  colors: ["rgb(227, 84, 212)"],
  stroke: {
      width: [.75],
  },
  fill: {
    opacity: 0.001,
    type: ['gradient'],
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.5,
      gradientToColors: ['rgba(227, 84, 212, 0.1)'],
      inverseColors: true,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [0, 50, 100],
      colorStops: [
        [
            {
                offset: 0,
                color: "rgba(227, 84, 212, 0.4)",
                opacity: 1
            },
            {
                offset: 55,
                color: "rgba(227, 84, 212, 0.1)",
                opacity: 0.1
            },
            {
                offset: 100,
                color: "rgba(227, 84, 212, 0.5)",
                opacity: 0.3
            }
        ],
      ]
    }
  }
  };

  var chart1 = new ApexCharts(document.querySelector("#chart-22"), options1);
  chart1.render();
    /*  End::Session Rate */

    /*  Start::Conversion Rate */
  var options2 = {
    series: [
      {
        data: [1, 20, 15, 35, 30, 25, 55, 45, 65],
      },
    ],
    chart: {
      height: 85,
      width: 100,
      type: 'area',
      zoom: {
          enabled: false
      },
      sparkline: {
          enabled: true
      }
  },
  tooltip: {
      enabled: true,
      x: {
          show: false
      },
      y: {
          title: {
              formatter: function (seriesName) {
                  return ''
              }
          }
      },
      marker: {
          show: false
      }
  },
  dataLabels: {
      enabled: false
  },
  grid: {
      borderColor: 'transparent',
  },
  xaxis: {
      crosshairs: {
          show: false,
      }
  },
  yaxis: { 
    max: 65,
  },
  colors: ["rgb(255, 93, 159)"],
  stroke: {
      width: [.75],
  },
  fill: {
    opacity: 0.001,
    type: ['gradient'],
    gradient: {
      shade: 'light',
      type: "horizontal",
      shadeIntensity: 0.5,
      gradientToColors: ['rgba(255, 93, 159, 0.1)'],
      inverseColors: true,
      opacityFrom: 0.35,
      opacityTo: 0.05,
      stops: [0, 50, 100],
      colorStops: [
        [
            {
                offset: 0,
                color: "rgba(255, 93, 159, 0.4)",
                opacity: 1
            },
            {
                offset: 55,
                color: "rgba(255, 93, 159, 0.1)",
                opacity: 0.1
            },
            {
                offset: 100,
                color: "rgba(255, 93, 159, 0.5)",
                opacity: 0.3
            }
        ],
      ]
    }
  }
  };
  var chart2 = new ApexCharts(document.querySelector("#chart-23"), options2);
  chart2.render();
    /*  End::Conversion Rate */

    
  