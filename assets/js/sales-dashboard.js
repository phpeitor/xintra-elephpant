(function () {
"use strict";

  const formatNumber = (n) =>
  Number(n).toLocaleString('es-PE', {
    //minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

fetch('controller/dashboard/apx_contadores.php')
  .then(res => res.json())
  .then(data => {
    const contadores = data.contadores[0];
    const grafico = data.grafico || [];
    const versus = Array.isArray(data.versus) ? data.versus : [];

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

    /* === GRAFICO ITEMS POR USUARIO === */
    const labels = versus.map(v => v.usuario);
    const series = versus.map(v => parseFloat(v.items_actuales || 0));
    const totalItems = series.reduce((a, b) => a + b, 0);

    // === NUEVO: Calcular totales anteriores y porcentaje ===
    const totalAnteriores = versus
      .map(v => parseFloat(v.items_anteriores || 0))
      .reduce((a, b) => a + b, 0);

    const porcentajeCambio = totalAnteriores > 0
      ? ((totalItems - totalAnteriores) / totalAnteriores) * 100
      : 0;

    // Mostrar en DOM
    const totalItemEl = document.querySelector('#total_item');
    const porcentajeItemEl = document.querySelector('#porcentaje_item');

    if (totalItemEl) totalItemEl.childNodes[0].textContent = formatNumber(totalItems) + " ";
    if (porcentajeItemEl) {
      const icon = porcentajeCambio >= 0
        ? `<i class="ti ti-trending-up align-middle me-1"></i>`
        : `<i class="ti ti-trending-down align-middle me-1"></i>`;
      const colorClass = porcentajeCambio >= 0 ? "text-success" : "text-danger";
      porcentajeItemEl.className = `${colorClass} text-xs ms-2 inline-flex items-center`;
      porcentajeItemEl.innerHTML = `${icon}${porcentajeCambio.toFixed(1)}%`;
    }

    if (totalItems === 0) {
      document.querySelector("#orders").innerHTML = `
        <div class="text-center text-gray-500 py-10">
          No hay datos para mostrar 📊
        </div>`;
    } else {
      const options2 = {
        series,
        labels,
        chart: { height: 280, type: "donut" },
        dataLabels: { enabled: false },
        legend: {
          show: true,
          position: "bottom",
          horizontalAlign: "center",
          height: 52,
          markers: {
            width: 8, height: 8, radius: 2, shape: "circle", size: 4, strokeWidth: 0,
          },
          offsetY: 10,
        },
        plotOptions: {
          pie: {
            donut: {
              size: "80%",
              labels: {
                show: true,
                name: { show: true, fontSize: "18px", color: "#495057", offsetY: -10 },
                value: {
                  show: true,
                  fontSize: "14px",
                  color: "#6c757d",
                  offsetY: 4,
                  formatter: (val) => formatNumber(val),
                },
                total: {
                  show: true,
                  label: "Total",
                  fontSize: "18px",
                  fontWeight: 600,
                  color: "#495057",
                  formatter: () => formatNumber(totalItems),
                },
              },
            },
          },
        },
        colors: [
          "rgba(var(--primary-rgb))",
          "rgba(227, 84, 212, 1)",
          "rgba(255, 93, 159, 1)",
          "rgba(255, 142, 111, 1)",
          "rgba(255, 199, 111, 1)",
          "rgba(99, 192, 222, 1)",
          "rgba(132, 99, 255, 1)",
          "rgba(0, 170, 91, 1)",
        ],
        tooltip: { y: { formatter: (val) => `${formatNumber(val)} ítems` } },
      };

      const chart2 = new ApexCharts(document.querySelector("#orders"), options2);
      chart2.render();
    }


    // === TABLA TOP CATEGORIES ===
    const tbody = document.querySelector(".top-categories tbody");
    tbody.innerHTML = ""; // limpiar contenido previo

    const colorClasses = ["one", "two", "three", "four", "five"];

    const totalActual = data.versus.reduce((acc, v) => acc + parseFloat(v.total_actual || 0), 0);
    const totalAnterior = data.versus.reduce((acc, v) => acc + parseFloat(v.total_anteriores || 0), 0);

    let porcentajeActual = 0;
    if (totalAnterior === 0 && totalActual > 0) {
      porcentajeActual = 100;
    } else if (totalAnterior > 0) {
      porcentajeActual = ((totalActual - totalAnterior) / totalAnterior) * 100;
    }

    const tendenciaUp = porcentajeActual >= 0;
    const icono = tendenciaUp ? "ti ti-arrow-narrow-up" : "ti ti-arrow-narrow-down";
    const color = tendenciaUp ? "text-success" : "text-danger";

    const formatCurrency = (n) =>
      `S/ ${parseFloat(n).toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })}`;

    // Actualizar spans
    document.getElementById("total_actual").textContent = formatCurrency(totalActual);

    const porcentajeSpan = document.getElementById("porcentaje_actual");
    porcentajeSpan.innerHTML = `${porcentajeActual.toFixed(1)}% <i class="${icono}"></i>`;
    porcentajeSpan.className = `${color} me-2 text-[11px]`;

    data.versus.forEach((v, i) => {
      const usuario = v.usuario;
      const actuales = parseFloat(v.items_actuales || 0);
      const anteriores = parseFloat(v.items_anteriores || 0);

      // Calcular porcentaje de variación
      let porcentaje = 0;
      if (anteriores === 0 && actuales > 0) {
        porcentaje = 100;
      } else if (anteriores > 0) {
        porcentaje = ((actuales - anteriores) / anteriores) * 100;
      }

      const tendenciaUp = porcentaje >= 0;
      const icono = tendenciaUp ? "ti ti-trending-up" : "ti ti-trending-down";
      const claseColor = tendenciaUp
        ? (porcentaje === 0 ? "bg-warning" : "bg-success")
        : "bg-danger";

      // Asignar clase de color rotativa
      const colorClass = colorClasses[i % colorClasses.length];

      const fila = `
        <tr class="border-b border-defaultborder dark:border-defaultborder/10">
          <td>
            <span class="font-medium top-category-name ${colorClass}">${usuario}</span>
          </td>
          <td>
            <span class="font-medium">${actuales}</span>
          </td>
          <td class="text-center">
            <span class="text-textmuted dark:text-textmuted/50 text-xs">${anteriores}</span>
          </td>
          <td class="!text-end">
            <span class="badge ${claseColor}">
              ${porcentaje.toFixed(1)}% <i class="${icono}"></i>
            </span>
          </td>
        </tr>
      `;

      tbody.insertAdjacentHTML("beforeend", fila);
    });


  })
  .catch(err => console.error('Error al cargar dashboard:', err));

  
})();