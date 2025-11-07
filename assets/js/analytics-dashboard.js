fetch("controller/venta/apx_mensual.php")
  .then(response => response.json())
  .then(data => {
    if (!data || data.length < 2) return;
    data.reverse();

    const seriesData = data.map(item => parseFloat(item.total));
    const categories = data.map(item => item.mes);
    const last = data[data.length - 1];

    // Mes actual formateado (Nov 2025)
    const mesEl = document.getElementById('mes_22');
    if (mesEl && last?.mes) {
      const [yy, mm] = String(last.mes).split('-');
      const d = new Date(Number(yy), Number(mm) - 1, 1);
      const fmt = new Intl.DateTimeFormat('es-PE', { month: 'short', year: 'numeric' }).format(d);
      const pretty = fmt.replace(/\./g, '').replace(/^./, s => s.toUpperCase());
      mesEl.textContent = pretty;
    }

    // Comparación mes actual vs anterior
    const prev = data[data.length - 2];
    const lastTotal = parseFloat(last.total);
    const prevTotal = parseFloat(prev.total);
    const diff = lastTotal - prevTotal;
    const pctChange = prevTotal !== 0 ? (diff / prevTotal) * 100 : 0;

    // Actualizar etiquetas
    const totalEl = document.querySelector("#total_22");
    const incEl = document.querySelector("#inc_22");
    const pctEl = document.querySelector("#pct_22");

    const isIncrease = diff >= 0;
    const icon = isIncrease ? "ti ti-arrow-narrow-up" : "ti ti-arrow-narrow-down";
    const colorClass = isIncrease ? "text-success" : "text-danger";
    const textChange = isIncrease ? "Increased By" : "Decreased By";

    totalEl.textContent = lastTotal.toLocaleString("es-PE", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
      style: "currency",
      currency: "PEN"
    });

    incEl.textContent = textChange;

    pctEl.innerHTML = `
      ${Math.abs(pctChange).toFixed(2)}% 
      <i class="${icon} text-[16px]"></i>
    `;
    pctEl.className = colorClass;

    // --- Configurar gráfico mensual ---
    var options1 = {
      series: [{ name: "Total", data: seriesData }],
      chart: {
        height: 85,
        width: 100,
        type: 'area',
        zoom: { enabled: false },
        sparkline: { enabled: true }
      },
      tooltip: {
        enabled: true,
        x: { show: true },
        y: {
          title: { formatter: () => 'Total: S/' },
          formatter: val => val.toLocaleString("es-PE", { minimumFractionDigits: 2 })
        },
        marker: { show: false }
      },
      dataLabels: { enabled: false },
      grid: { borderColor: 'transparent' },
      xaxis: { categories, labels: { show: false }, crosshairs: { show: false } },
      yaxis: { labels: { show: false } },
      colors: ["rgb(227, 84, 212)"],
      stroke: { width: [.75] },
      fill: {
        opacity: 0.001,
        type: ['gradient'],
        gradient: {
          shade: 'light',
          type: "horizontal",
          opacityFrom: 0.35,
          opacityTo: 0.05,
          stops: [0, 50, 100],
          colorStops: [
            [
              { offset: 0, color: "rgba(227, 84, 212, 0.4)", opacity: 1 },
              { offset: 55, color: "rgba(227, 84, 212, 0.1)", opacity: 0.1 },
              { offset: 100, color: "rgba(227, 84, 212, 0.5)", opacity: 0.3 }
            ]
          ]
        }
      }
    };

    new ApexCharts(document.querySelector("#chart-22"), options1).render();
  })
  .catch(error => console.error("Error al cargar los datos:", error));


// ============================
// === GRÁFICO DIARIO (23) ====
// ============================
fetch("controller/venta/apx_diario.php")
  .then(res => res.json())
  .then(data => {
    if (!Array.isArray(data) || data.length === 0) return;

    data.reverse();

    const dias = data.map(d => d.dia);
    const totales = data.map(d => parseFloat(d.total));

    const ultimo = totales[totales.length - 1];
    const penultimo = totales.length > 1 ? totales[totales.length - 2] : 0;
    const diff = ultimo - penultimo;
    const pct = penultimo > 0 ? ((diff / penultimo) * 100).toFixed(2) : 0;

    // === Etiquetas ===
    document.querySelector("#total_23").textContent = ultimo.toLocaleString("es-PE", {
      style: "currency",
      currency: "PEN"
    });

    // Última fecha (ej: Nov 11)
    const lastDate = dias[dias.length - 1];
    const [yy, mm, dd] = lastDate.split('-');
    const d = new Date(Number(yy), Number(mm) - 1, Number(dd));
    const fmtShort = new Intl.DateTimeFormat('es-PE', { month: 'short', day: '2-digit' }).format(d);
    const pretty = fmtShort.replace(/\./g, '').replace(/^(\p{L}+)\s/iu, (m) => m.toUpperCase());
    document.querySelector("#mes_23").textContent = pretty;

    // === Incremento/Decremento ===
    const inc = document.querySelector("#inc_23");
    const pctEl = document.querySelector("#pct_23");

    const isIncrease = diff >= 0;
    const icon = isIncrease ? "ti ti-arrow-narrow-up" : "ti ti-arrow-narrow-down";
    const color = isIncrease ? "text-success" : "text-danger";
    inc.textContent = isIncrease ? "Increased By" : "Decreased By";
    pctEl.className = color;
    pctEl.innerHTML = `${Math.abs(pct)}% <i class="${icon} text-[16px]"></i>`;

    // === Gráfico Diario ===
    var options2 = {
      series: [{ data: totales }],
      chart: {
        height: 85,
        width: 100,
        type: "area",
        zoom: { enabled: false },
        sparkline: { enabled: true }
      },
      tooltip: {
        enabled: true,
        x: {
          formatter: (val, opts) => {
            const fecha = dias[opts.dataPointIndex];
            const [yy, mm, dd] = fecha.split("-");
            const dateObj = new Date(Number(yy), Number(mm) - 1, Number(dd));
            return new Intl.DateTimeFormat("es-PE", { day: "2-digit", month: "short" }).format(dateObj);
          }
        },
        y: {
          title: { formatter: () => "Total: S/" },
          formatter: val =>
            val.toLocaleString("es-PE", { minimumFractionDigits: 2 })
        },
        marker: { show: false }
      },
      dataLabels: { enabled: false },
      grid: { borderColor: "transparent" },
      xaxis: { categories: dias, crosshairs: { show: false } },
      colors: ["rgb(255, 93, 159)"],
      stroke: { width: [0.75] },
      fill: {
        opacity: 0.001,
        type: ["gradient"],
        gradient: {
          shade: "light",
          type: "horizontal",
          opacityFrom: 0.35,
          opacityTo: 0.05,
          stops: [0, 50, 100],
          colorStops: [
            [
              { offset: 0, color: "rgba(255, 93, 159, 0.4)", opacity: 1 },
              { offset: 55, color: "rgba(255, 93, 159, 0.1)", opacity: 0.1 },
              { offset: 100, color: "rgba(255, 93, 159, 0.5)", opacity: 0.3 }
            ]
          ]
        }
      }
    };

    new ApexCharts(document.querySelector("#chart-23"), options2).render();
  })
  .catch(err => console.error("Error cargando datos diarios:", err));

// ============================
// === GRÁFICO CLIENTES (21) ====
// ============================
fetch("controller/venta/apx_cliente.php")
  .then(response => response.json())
  .then(data => {
    if (!Array.isArray(data) || data.length < 2) return;

    // Ordenar de más antiguo a más reciente
    data.reverse();

    const seriesData = data.map(item => parseFloat(item.cliente));
    const categories = data.map(item => item.mes);

    // Último y penúltimo registro
    const last = data[data.length - 1];
    const prev = data[data.length - 2];

    const lastTotal = parseFloat(last.cliente);
    const prevTotal = parseFloat(prev.cliente);
    const diff = lastTotal - prevTotal;
    const pctChange = prevTotal !== 0 ? (diff / prevTotal) * 100 : 0;

    // === Actualizar HTML ===
    const totalEl = document.querySelector("#total_21");
    const incEl = document.querySelector("#inc_21");
    const pctEl = document.querySelector("#pct_21");

    const isIncrease = diff >= 0;
    const icon = isIncrease ? "ti ti-arrow-narrow-up" : "ti ti-arrow-narrow-down";
    const colorClass = isIncrease ? "text-success" : "text-danger";
    const textChange = isIncrease ? "Increased By" : "Decreased By";

    totalEl.textContent = lastTotal.toLocaleString("es-PE");
    incEl.textContent = textChange;
    pctEl.innerHTML = `
      ${Math.abs(pctChange).toFixed(2)}% 
      <i class="${icon} text-[16px]"></i>
    `;
    pctEl.className = colorClass;

    // === Gráfico ===
    const options = {
      series: [{ data: seriesData }],
      chart: {
        height: 85,
        width: 100,
        type: "area",
        zoom: { enabled: false },
        sparkline: { enabled: true }
      },
      tooltip: {
        enabled: true,
        x: {
          show: true,
          formatter: (val, opts) => {
            const fecha = categories[opts.dataPointIndex];
            const [yy, mm] = fecha.split("-");
            const dateObj = new Date(Number(yy), Number(mm) - 1, 1);
            const fmt = new Intl.DateTimeFormat("es-PE", { month: "short", year: "numeric" }).format(dateObj);
            return fmt.replace(/\./g, '').replace(/^./, s => s.toUpperCase());
          }
        },
        y: {
          title: { formatter: () => "Clientes: " },
          formatter: val => val.toLocaleString("es-PE")
        },
        marker: { show: false }
      },
      dataLabels: { enabled: false },
      grid: { borderColor: "transparent" },
      xaxis: { categories, crosshairs: { show: false } },
      yaxis: { labels: { show: false } },
      colors: ["rgba(var(--primary-rgb))"],
      stroke: { width: [.75] },
      fill: {
        opacity: 0.001,
        type: ["gradient"],
        gradient: {
          shade: "light",
          type: "horizontal",
          shadeIntensity: 0.5,
          gradientToColors: ["rgba(var(--primary-rgb), 0.1)"],
          inverseColors: true,
          opacityFrom: 0.35,
          opacityTo: 0.05,
          stops: [0, 50, 100],
          colorStops: [
            [
              { offset: 0, color: "rgba(var(--primary-rgb), 0.4)", opacity: 1 },
              { offset: 55, color: "rgba(var(--primary-rgb), 0.1)", opacity: 0.1 },
              { offset: 100, color: "rgba(var(--primary-rgb), 0.5)", opacity: 0.3 }
            ]
          ]
        }
      }
    };

    new ApexCharts(document.querySelector("#chart-21"), options).render();
  })
  .catch(err => console.error("Error cargando datos de clientes:", err));
