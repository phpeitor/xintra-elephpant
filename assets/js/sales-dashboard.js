(function () {
"use strict";

  const formatNumber = (n) =>
  Number(n).toLocaleString('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

fetch('controller/dashboard/apx_contadores.php')
  .then(res => res.json())
  .then(data => {
    const contadores = data.contadores[0];
    const grafico = data.grafico;

    // === CONTADORES ===
    document.getElementById('total_usuario').textContent  = formatNumber(contadores.total_personal);
    document.getElementById('total_producto').textContent = formatNumber(contadores.total_productos);
    document.getElementById('total_servicio').textContent = formatNumber(contadores.total_servicios);
    document.getElementById('total_ticket').textContent   = formatNumber(contadores.total_pedidos);

    // === DATOS PARA GRÁFICO ===
    const meses = grafico.map(g => g.mes).reverse();

    const growthData = grafico.map(g => parseFloat(g.total)).reverse();
    const salesData = grafico.map(g => g.producto).reverse();
    const profitData = grafico.map(g => g.servicio).reverse();

    // === GRAFICO SALES OVERVIEW ===
    const options = {
      series: [
        {
          name: 'Total',
          type: 'column',
          data: growthData,
        },
        {
          name: 'Servicios',
          type: 'area',
          data: profitData,
        },
        {
          name: 'Productos',
          type: 'line',
          data: salesData,
        },
      ],
      chart: {
        redrawOnWindowResize: true,
        height: 318,
        type: 'bar',
        toolbar: { show: false },
        dropShadow: {
          enabled: true,
          top: 7,
          left: 0,
          blur: 1,
          color: ['transparent', 'transparent', 'rgb(227, 84, 212)'],
          opacity: 0.05,
        },
      },
      plotOptions: {
        bar: { horizontal: false, columnWidth: '18%', borderRadius: 2 },
      },
      grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
      dataLabels: { enabled: false },
      stroke: { width: [0, 2, 2], curve: 'smooth' },
      legend: {
        show: true,
        fontSize: '12px',
        position: 'bottom',
        horizontalAlign: 'center',
        fontWeight: 500,
        height: 40,
        offsetY: 10,
        labels: { colors: '#9ba5b7' },
        markers: {
          width: 7,
          height: 7,
          shape: 'circle',
          size: 3.5,
          strokeWidth: 0,
          strokeColor: '#fff',
          radius: 12,
        },
      },
      colors: ['rgba(var(--primary-rgb))', 'rgba(24, 220, 162, 0.05)', 'rgb(227, 84, 212)'],
      yaxis: {
        labels: {
          formatter: (y) => 'S/. ' + formatNumber(y),
          style: { colors: '#8c9097', fontSize: '11px', fontWeight: 600 },
        },
      },
      xaxis: {
        categories: meses,
        labels: {
          rotate: -90,
          style: { colors: '#8c9097', fontSize: '11px', fontWeight: 600 },
        },
        axisBorder: { color: 'rgba(119, 119, 142, 0.05)' },
        axisTicks: { color: 'rgba(119, 119, 142, 0.05)' },
      },
      tooltip: {
        shared: true,
        intersect: false,
        y: {
          formatter: (y) => (typeof y !== 'undefined' ? 'S/. ' + formatNumber(y) : y),
        },
      },
      fill: {
        opacity: 1,
        type: ['solid', 'gradient', 'solid'],
        gradient: {
          shade: 'light',
          type: 'horizontal',
          shadeIntensity: 0.5,
          gradientToColors: ['#fdc530'],
          inverseColors: true,
          opacityFrom: 0.35,
          opacityTo: 0.05,
          stops: [0, 50, 100],
        },
      },
    };

    const chart = new ApexCharts(document.querySelector('#sales-overview'), options);
    chart.render();
  })
  .catch(err => console.error('Error al cargar dashboard:', err));

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

})();