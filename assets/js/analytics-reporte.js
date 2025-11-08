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

    // === Calcular el máximo y mínimo total con precisión numérica ===
    const totals = data.map((d) => parseFloat(d.total));
    const maxTotal = Math.max(...totals);
    const minTotal = Math.min(...totals);

    // === Llenar tabla ===
    const tbody = document.querySelector(".ti-custom-table tbody");
    tbody.innerHTML = "";

    data.forEach((u) => {
      const total = parseFloat(u.total);
      let totalClass = "";
      if (total === maxTotal) totalClass = "font-semibold text-success";
      else if (total === minTotal) totalClass = "text-danger font-semibold";

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${u.usuario}</td>
        <td class="${totalClass}">${formatCurrency(total)}</td>
        <td>${u.tickets}</td>
        <td>${u.items}</td>
      `;
      tbody.appendChild(tr);
    });

    // === Configurar gráfico DONUT ===
    const options = {
      series: data.map((u) => parseFloat(u.total)),
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
      colors: [
        "rgb(227, 84, 212)",
        "rgba(var(--primary-rgb))",
        "rgb(255, 93, 159)",
        "rgb(255, 142, 111)",
        "rgb(158, 92, 247)",
        "rgb(102, 204, 153)",
        "rgb(255, 193, 7)",
        "rgb(99, 102, 241)",
      ],
      tooltip: {
        y: {
          formatter: (val) => formatCurrency(val),
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
                formatter: (val) => formatCurrency(val),
              },
              total: {
                show: true,
                showAlways: true,
                label: "Total",
                fontSize: "22px",
                fontWeight: 600,
                color: "#495057",
                formatter: function (w) {
                  const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                  return formatCurrency(sum);
                },
              },
            },
          },
        },
      },
    };

    const chart = new ApexCharts(
      document.querySelector("#referrals-chart"),
      options
    );
    chart.render();
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
