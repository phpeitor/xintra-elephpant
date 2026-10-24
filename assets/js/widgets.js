(async function () {
  "use strict";

  const hash = new URLSearchParams(window.location.search).get("hash");
  const chartContainer = document.querySelector("#salerevenue1");
  const timelineList = document.getElementById("stock-timeline-list");
  const timelineEmpty = document.getElementById("stock-timeline-empty");
  if (!hash || !chartContainer) return;

  const numberFormat = new Intl.NumberFormat("es-PE", { maximumFractionDigits: 2 });
  const setEmptyTimeline = (message) => {
    timelineList?.replaceChildren();
    if (!timelineEmpty) return;
    timelineEmpty.textContent = message;
    timelineEmpty.classList.remove("hidden");
  };

  const renderTimeline = (timeline) => {
    if (!timelineList || !timelineEmpty) return;
    timelineList.replaceChildren();
    if (!Array.isArray(timeline) || timeline.length === 0) {
      setEmptyTimeline("Todavía no hay movimientos de stock para este producto.");
      return;
    }

    timelineEmpty.classList.add("hidden");
    const dateFormat = new Intl.DateTimeFormat("es-PE", {
      weekday: "short", day: "2-digit", month: "short", year: "numeric",
    });
    const timeFormat = new Intl.DateTimeFormat("es-PE", { hour: "2-digit", minute: "2-digit" });
    timeline.forEach((day) => {
      const item = document.createElement("li");
      item.className = "stock-timeline-item";

      const marker = document.createElement("span");
      marker.className = "stock-timeline-marker";
      marker.setAttribute("aria-hidden", "true");
      marker.innerHTML = '<i class="ri-exchange-funds-line"></i>';
      item.appendChild(marker);

      const card = document.createElement("article");
      card.className = "stock-timeline-entry";
      const header = document.createElement("div");
      header.className = "stock-timeline-entry-header";
      const dayDate = new Date(`${day.fecha}T12:00:00`);
      const title = document.createElement("h3");
      title.className = "stock-timeline-date";
      title.textContent = dateFormat.format(dayDate);
      const lastTime = document.createElement("span");
      lastTime.className = "stock-timeline-time";
      lastTime.textContent = `Último movimiento ${timeFormat.format(new Date(String(day.ultimo_movimiento).replace(" ", "T")))}`;
      header.append(title, lastTime);
      card.appendChild(header);

      const metrics = document.createElement("div");
      metrics.className = "stock-timeline-metrics";
      const entries = Number(day.entradas) || 0;
      const exits = Number(day.salidas) || 0;
      const createMetric = (className, icon, label, amount) => {
        const metric = document.createElement("div");
        metric.className = `stock-timeline-metric ${className}`;
        const iconElement = document.createElement("i");
        iconElement.className = icon;
        iconElement.setAttribute("aria-hidden", "true");
        const text = document.createElement("span");
        text.textContent = `${label} ${amount}`;
        metric.append(iconElement, text);
        return metric;
      };
      if (entries > 0) metrics.appendChild(createMetric("is-entry", "ri-arrow-down-circle-line", "Entradas", `+${numberFormat.format(entries)}`));
      if (exits > 0) metrics.appendChild(createMetric("is-exit", "ri-arrow-up-circle-line", "Salidas", `−${numberFormat.format(exits)}`));
      card.appendChild(metrics);

      const count = document.createElement("p");
      count.className = "stock-timeline-count";
      const movementCount = Number(day.movimientos) || 0;
      count.textContent = `${movementCount} ${movementCount === 1 ? "movimiento" : "movimientos"} registrados en esta fecha`;
      card.appendChild(count);
      item.appendChild(card);
      timelineList.appendChild(item);
    });
  };

  try {
    const response = await fetch(`controller/stk_item.php?hash=${encodeURIComponent(hash)}`);
    const json = await response.json();
    if (!response.ok || !json.ok) throw new Error(json.message || "No se pudieron consultar los movimientos.");

    renderTimeline(json.timeline || []);
    const data = Array.isArray(json.data) ? json.data : [];
    if (data.length === 0) {
      chartContainer.innerHTML = '<p class="stock-chart-empty">Sin movimientos para graficar.</p>';
      return;
    }

    const fechas = [...new Set(data.map((movement) => movement.Date))].sort((a, b) => {
      const [monthA, yearA] = a.split("-");
      const [monthB, yearB] = b.split("-");
      const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
      return (Number(yearA) - Number(yearB)) * 12 + months.indexOf(monthA) - months.indexOf(monthB);
    });

    const tipos = [...new Set(data.map((movement) => movement.Tipo))];
    const series = tipos.map((tipo) => ({
      name: tipo,
      data: fechas.map((fecha) => data
        .filter((movement) => movement.Date === fecha && movement.Tipo === tipo)
        .reduce((sum, movement) => sum + (Number(movement.Total) || 0), 0)),
    }));

    const options = {
      series,
      chart: { height: 300, type: "line", zoom: { enabled: false }, toolbar: { show: false } },
      dataLabels: { enabled: false },
      stroke: { curve: "smooth", width: 2 },
      legend: { show: true, position: "top", horizontalAlign: "center" },
      colors: ["#f87171", "#60a5fa", "#34d399", "#fbbf24", "#a78bfa"],
      grid: { borderColor: "#f3f3f3", strokeDashArray: 3 },
      xaxis: { categories: fechas, labels: { rotate: -45, style: { fontSize: "11px" } } },
      yaxis: { title: { text: "Unidades" }, labels: { style: { fontSize: "11px" } } },
      noData: { text: "Sin datos para mostrar" },
    };
    new ApexCharts(chartContainer, options).render();
  } catch (error) {
    console.error("Error al cargar historial de stock:", error);
    chartContainer.innerHTML = '<p class="stock-chart-empty">No se pudo cargar el gráfico de stock.</p>';
    setEmptyTimeline("No se pudo cargar el timeline de movimientos.");
  }
})();
