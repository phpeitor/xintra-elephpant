(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const element = document.getElementById("tabla-compras");
    const searchInput = document.getElementById("compras-search");
    const statusFilter = document.getElementById("compras-estado-filter");
    const reloadButton = document.getElementById("btnRecargarCompras");
    const money = new Intl.NumberFormat("es-PE", { style: "currency", currency: "PEN" });
    const escapeHtml = (value) => String(value ?? "").replace(/[&<>"']/g, (character) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
    }[character]));
    if (!element) return;

    const table = new Tabulator(element, {
      layout: "fitColumns",
      pagination: "local",
      paginationSize: 10,
      paginationSizeSelector: [5, 10, 15, 20, 25],
      paginationCounter: "rows",
      movableColumns: true,
      ajaxURL: "controller/table_compra.php",
      ajaxResponse: (_url, _params, response) => Array.isArray(response) ? response : [],
      placeholder: "Aún no hay compras registradas. Usa «Crear compra» para añadir la primera.",
      columns: [
        { title: "Documento", field: "numero", minWidth: 190,
          formatter: (cell) => {
            const row = cell.getRow().getData();
            return `<div class="compra-document-cell"><span class="compra-document-icon"><i class="ri-file-text-line" aria-hidden="true"></i></span><div><strong>${escapeHtml(row.tipo_comprobante)} ${escapeHtml(row.serie)}-${escapeHtml(row.numero)}</strong><small>Compra #${Number(row.id)}</small></div></div>`;
          } },
        { title: "Proveedor", field: "proveedor", minWidth: 190, widthGrow: 2,
          formatter: (cell) => `<div><strong>${escapeHtml(cell.getValue())}</strong><small>${escapeHtml(cell.getRow().getData().tipo_documento)} ${escapeHtml(cell.getRow().getData().documento)}</small></div>` },
        { title: "Fecha", field: "fecha", sorter: "date", width: 130 },
        { title: "Total", field: "total", sorter: "number", hozAlign: "right", width: 145,
          formatter: (cell) => `<strong>${money.format(Number(cell.getValue()) || 0)}</strong>` },
        { title: "Estado", field: "estado", width: 180,
          formatter: (cell) => cell.getValue() === "ANULADA"
            ? '<span class="compra-status-pill is-cancelled"><i class="ri-close-circle-line" aria-hidden="true"></i>Anulada</span>'
            : '<span class="compra-status-pill is-pending"><i class="ri-time-line" aria-hidden="true"></i>Pendiente de recepción</span>' },
        { title: "Acción", field: "acciones", width: 125, hozAlign: "right", headerSort: false,
          formatter: (cell) => `<a class="ti-btn ti-btn-light ti-btn-sm" href="detalle_compra.php?id=${Number(cell.getRow().getData().id)}">Ver detalle<i class="ri-arrow-right-line ms-1" aria-hidden="true"></i></a>` },
      ],
    });

    const updateSummary = (rows) => {
      const pending = rows.filter((row) => row.estado === "PENDIENTE");
      const total = rows.reduce((sum, row) => sum + (Number(row.total) || 0), 0);
      document.getElementById("compra-kpi-total").textContent = String(rows.length);
      document.getElementById("compra-kpi-pendientes").textContent = String(pending.length);
      document.getElementById("compra-kpi-monto").textContent = money.format(total);
    };
    const filterRows = () => {
      const query = searchInput.value.trim().toLocaleLowerCase("es-PE");
      const status = statusFilter.value;
      table.setFilter((row) => {
        const searchable = [row.proveedor, row.tipo_comprobante, row.serie, row.numero, row.id]
          .join(" ").toLocaleLowerCase("es-PE");
        return (!query || searchable.includes(query)) && (!status || row.estado === status);
      });
    };

    table.on("dataLoaded", updateSummary);
    table.on("dataLoadError", () => {
      const error = document.getElementById("compras-error");
      error.textContent = "No se pudo cargar el historial. Verifica que las migraciones de proveedores y compras estén aplicadas.";
      error.classList.remove("hidden");
    });
    searchInput.addEventListener("input", filterRows);
    statusFilter.addEventListener("change", filterRows);
    reloadButton.addEventListener("click", async () => {
      reloadButton.disabled = true;
      document.getElementById("iconRecargarCompras").classList.add("animate-spin");
      document.getElementById("textoRecargarCompras").textContent = "Cargando…";
      try {
        await table.replaceData();
        document.getElementById("compras-error").classList.add("hidden");
        filterRows();
      } catch (_error) {
        const error = document.getElementById("compras-error");
        error.textContent = "No se pudo actualizar el historial de compras.";
        error.classList.remove("hidden");
      } finally {
        reloadButton.disabled = false;
        document.getElementById("iconRecargarCompras").classList.remove("animate-spin");
        document.getElementById("textoRecargarCompras").textContent = "Actualizar";
      }
    });
  });
})();
