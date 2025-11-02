(async function () {
  'use strict';

  const hash = new URLSearchParams(window.location.search).get("hash");
  const contenedor = document.querySelector("#salerevenue1");
  if (!hash || !contenedor) return;

  try {

    const res = await fetch(`controller/stk_item.php?hash=${hash}`);
    const json = await res.json();

    if (!json.ok || !Array.isArray(json.data)) {
      contenedor.innerHTML = "<p style='text-align:center;color:#999;margin-top:40px'>Sin datos disponibles</p>";
      return;
    }

    const data = json.data;
    if (data.length === 0) {
      contenedor.innerHTML = "<p style='text-align:center;color:#999;margin-top:40px'>Sin movimientos registrados</p>";
      return;
    }

    const fechas = [...new Set(data.map(i => i.Date))].sort((a, b) => {
      // intenta ordenar por mes-año si el formato es "Jan-25"
      const [ma, ya] = a.split('-');
      const [mb, yb] = b.split('-');
      const meses = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
      return (parseInt(yb) - parseInt(ya)) * 12 + (meses.indexOf(mb) - meses.indexOf(ma));
    });

    // 🔢 Tipos dinámicos (Venta, Almacén, Entrada, etc.)
    const tipos = [...new Set(data.map(i => i.Tipo))];

    // 🔁 Construir series
    const series = tipos.map(tipo => {
      const valores = fechas.map(f => {
        const items = data.filter(i => i.Date === f && i.Tipo === tipo);
        return items.reduce((a, b) => a + (b.Total || 0), 0);
      });
      return { name: tipo, data: valores };
    });

    // 🎨 Configuración ApexCharts
    const options = {
      series,
      chart: {
        height: 300,
        type: 'line',
        zoom: { enabled: false },
        toolbar: { show: false }
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
      legend: {
        show: true,
        position: "top",
        horizontalAlign: "center"
      },
      colors: ["#f87171", "#60a5fa", "#34d399", "#fbbf24", "#a78bfa"], // paleta adaptable
      grid: {
        borderColor: '#f3f3f3',
        strokeDashArray: 3
      },
      xaxis: {
        categories: fechas,
        labels: { rotate: -45, style: { fontSize: '11px' } }
      },
      yaxis: {
        title: { text: "Total" },
        labels: { style: { fontSize: '11px' } }
      },
      noData: {
        text: "Sin datos para mostrar"
      }
    };

    const chart = new ApexCharts(contenedor, options);
    chart.render();

    flatpickr("#targetDate", {
        enableTime: true,
        minTime: "08:00",
        maxTime: "22:00",
        disableMobile: true
    });

  } catch (error) {
    console.error("Error al cargar gráfico:", error);
    contenedor.innerHTML = "<p style='text-align:center;color:#999;margin-top:40px'>Error al cargar datos</p>";
  }

})();
