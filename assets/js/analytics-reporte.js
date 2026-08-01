async function cargarUsuarios() {
  try {
    const res = await fetch("controller/venta/apx_usuario.php");
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) return;

    // === Formateador de moneda peruana con decimales ===
    const formatCurrency = (n) =>
      "S/ " + Number(n).toLocaleString("es-PE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

    const formatMonth = window.XintraTooltip.formatMonth;

    const tooltipCell = (content, month, color, className = "") => {
      const cellClass = className ? ` class="${className}"` : "";
      return `<td${cellClass} ${window.XintraTooltip.attr(formatMonth(month), color)}>${content}</td>`;
    };

    const userColors = [
      "rgb(227, 84, 212)",
      "rgba(var(--primary-rgb))",
      "rgb(255, 93, 159)",
      "rgb(255, 142, 111)",
      "rgb(158, 92, 247)",
      "rgb(102, 204, 153)",
      "rgb(255, 193, 7)",
      "rgb(99, 102, 241)",
    ];

    const currentTotals = data.map((u) => parseFloat(u.total_actual || 0));
    const maxTotal = Math.max(...currentTotals);
    const minTotal = Math.min(...currentTotals);

    const getVariation = (u) => {
      const totalActual = parseFloat(u.total_actual || 0);
      const totalAnterior = parseFloat(u.total_anteriores || 0);

      if (totalAnterior === 0 && totalActual > 0) return 100;
      if (totalAnterior > 0) return ((totalActual - totalAnterior) / totalAnterior) * 100;
      return 0;
    };

    const emojiByUser = new Map(
      [...data]
        .sort((a, b) => getVariation(b) - getVariation(a))
        .map((u, rank, list) => {
          let emoji = "😐";

          if (rank === 0) {
            emoji = "👑";
          } else if (rank === list.length - 1) {
            emoji = "😢";
          } else {
            const emojis = ["😊", "😐", "😡"];
            emoji = emojis[Math.min(rank - 1, emojis.length - 1)];
          }

          return [u.usuario, { emoji, rank: rank + 1 }];
        })
    );

    // === Llenar tabla ===
    const tbody = document.querySelector(".ti-custom-table tbody");
    tbody.innerHTML = "";

    data.forEach((u, i) => {
      const userColor = userColors[i % userColors.length];
      const totalActual = parseFloat(u.total_actual || 0);
      const totalAnterior = parseFloat(u.total_anteriores || 0);
      const ticketsActuales = parseFloat(u.tickets_actuales || 0);
      const ticketsAnteriores = parseFloat(u.tickets_anteriores || 0);
      const itemsActuales = parseFloat(u.items_actuales || 0);
      const itemsAnteriores = parseFloat(u.items_anteriores || 0);

      let totalClass = "";
      if (totalActual === maxTotal) totalClass = "font-semibold text-success";
      else if (totalActual === minTotal) totalClass = "text-danger font-semibold";

      const variation = getVariation(u);
      const userEmoji = emojiByUser.get(u.usuario) || { emoji: "😐", rank: i + 1 };

      const tendenciaUp = variation >= 0;
      const badgeClass = tendenciaUp ? "bg-success" : "bg-danger";
      const iconClass = tendenciaUp ? "ti ti-arrow-narrow-up" : "ti ti-arrow-narrow-down";

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>
          <div class="font-medium">${u.usuario}</div>
          <div class="text-[11px] text-textmuted dark:text-textmuted/50">
            ${formatMonth(u.mes_actual)} vs ${formatMonth(u.mes_anterior)}
          </div>
        </td>
        <td class="text-center text-lg leading-none">
          <span class="performance-emoji" data-rank="${userEmoji.rank}">${userEmoji.emoji}</span>
        </td>
        ${tooltipCell(formatCurrency(totalActual), u.mes_actual, userColor, totalClass)}
        ${tooltipCell(formatCurrency(totalAnterior), u.mes_anterior, userColor)}
        ${tooltipCell(ticketsActuales, u.mes_actual, userColor)}
        ${tooltipCell(ticketsAnteriores, u.mes_anterior, userColor)}
        ${tooltipCell(itemsActuales, u.mes_actual, userColor)}
        ${tooltipCell(itemsAnteriores, u.mes_anterior, userColor)}
        <td><span class="badge ${badgeClass}">${variation.toFixed(1)}% <i class="${iconClass}"></i></span></td>
      `;
      tbody.appendChild(tr);
    });

    window.XintraTooltip.init(tbody);

    // === Configurar gráficos DONUT ===
    const createDonutOptions = ({ series, title, formatter }) => ({
      series,
      chart: {
        height: 290,
        type: "donut",
      },
      labels: data.map((u) => u.usuario),
      dataLabels: {
        enabled: false,
      },
      legend: {
        show: false,
      },
      colors: userColors,
      tooltip: {
        y: {
          formatter,
        },
      },
      plotOptions: {
        pie: {
          expandOnClick: false,
          donut: {
            size: "75%",
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: "18px",
                color: "#495057",
                offsetY: -4,
              },
                value: {
                  show: true,
                  fontSize: "16px",
                  offsetY: 8,
                  formatter,
                },
                total: {
                  show: true,
                  showAlways: true,
                  label: title,
                  fontSize: "22px",
                  fontWeight: 600,
                  color: "#495057",
                  formatter: function (w) {
                    const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    return formatter(sum);
                  },
                },
            },
          },
        },
      },
    });

    const numberFormatter = (n) => Number(n).toLocaleString("es-PE", {
      maximumFractionDigits: 0,
    });

    new ApexCharts(
      document.querySelector("#referrals-chart"),
      createDonutOptions({
        series: data.map((u) => parseFloat(u.total_actual || 0)),
        title: "Total",
        formatter: formatCurrency,
      })
    ).render();

    new ApexCharts(
      document.querySelector("#tickets-chart"),
      createDonutOptions({
        series: data.map((u) => parseFloat(u.tickets_actuales || 0)),
        title: "Tickets",
        formatter: numberFormatter,
      })
    ).render();

    new ApexCharts(
      document.querySelector("#items-chart"),
      createDonutOptions({
        series: data.map((u) => parseFloat(u.items_actuales || 0)),
        title: "Items",
        formatter: numberFormatter,
      })
    ).render();
  } catch (err) {
    console.error("Error al cargar usuarios:", err);
  }
}

cargarUsuarios();

async function cargarTopProductos() {
  try {
    const res = await fetch("controller/venta/apx_item.php");
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) return;

    // Paleta de colores (rotan si hay más productos)
    const colores = [
      "bg-primary",
      "bg-primarytint1color",
      "bg-primarytint2color",
      "bg-primarytint3color",
      "bg-info",
    ];

    // Formateador de moneda (S/. con separador de miles y 2 decimales)
    const formatCurrency = (n) =>
      "S/ " + Number(n).toLocaleString("es-PE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

    // Encontrar el total máximo (para calcular % de barra)
    const maxTotal = Math.max(...data.map((d) => parseFloat(d.total)));
    const contenedor = document.querySelector(".bar-graf");
    contenedor.innerHTML = ""; // limpiamos para reconstruir

    data.forEach((item, i) => {
      const total = parseFloat(item.total);
      const porcentaje = ((total / maxTotal) * 100).toFixed(1);
      const color = colores[i % colores.length];

      const div = document.createElement("div");
      div.classList.add("mb-3");
      div.innerHTML = `
        <div class="flex mb-1">
          <span>${item.item}</span>
          <span class="ms-auto text-[14px] font-semibold">${formatCurrency(total)}</span>
        </div>
        <div class="progress progress-md p-1">
          <div
            class="progress-bar progress-bar-striped progress-bar-animated ${color}"
            role="progressbar"
            style="width: ${porcentaje}%;"
            aria-valuenow="${porcentaje}"
            aria-valuemin="0"
            aria-valuemax="100"
          ></div>
        </div>
      `;
      contenedor.appendChild(div);
    });
  } catch (err) {
    console.error("Error al cargar top productos:", err);
  }
}

cargarTopProductos();
