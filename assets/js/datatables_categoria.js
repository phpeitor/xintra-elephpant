(function () {
  "use strict";

  const table = new Tabulator("#download-table", {
    layout: "fitColumns",
    pagination: "local",
    paginationSize: 10,
    paginationSizeSelector: [5, 10, 15, 20, 25],
    paginationCounter: "rows",
    movableColumns: true,
    reactiveData: true,
    ajaxURL: "controller/table_categoria.php",
    ajaxResponse: function (url, params, response) {
      return response;
    },
    columns: [
      { title: "Id", field: "id", sorter: "number", width: 90 },
      { title: "Tipo", field: "tpo", headerFilter: "input" },
      { title: "Nombre", field: "nombre", headerFilter: "input", widthGrow: 2, minWidth: 120 },
      {
        title: "Estado",
        field: "estado",
        formatter: (cell) => {
          const v = String(cell.getValue() ?? "").trim();
          if (v === "1") return `<span class="badge bg-success">ACTIVE</span>`;
          if (v === "0") return `<span class="badge bg-danger">SUSPENDED</span>`;
          return `<span class="badge bg-outline-dark">NDF</span>`;
        },
        accessorDownload: (value) => (String(value) === "1" ? "ACTIVE" : String(value) === "0" ? "SUSPENDED" : "NDF"),
      },
      { title: "Fec. Registro", field: "fecha_creacion", sorter: "datetime", sorterParams: { format: "YYYY-MM-DD HH:mm:ss" } },
      {
        title: "Opciones",
        field: "acciones",
        hozAlign: "center",
        headerSort: false,
        width: 160,
        formatter: (cell) => {
          const row = cell.getRow().getData();
          const id = row.id;
          const idHash = md5(String(id));
          return `
            <div style="display:flex;gap:.5rem;justify-content:center;">
              <button class="btn-edit ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full btn-wave waves-effect waves-light" data-id="${idHash}">
                <i class="ri-edit-2-line"></i>
              </button>
              <button class="btn-delete ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full btn-wave waves-effect waves-light" data-id="${id}">
                <i class="ri-delete-bin-line"></i>
              </button>
            </div>`;
        },
        cellClick: function (e, cell) {
          const row = cell.getRow().getData();
          const id = row.id;

          if (e.target.closest(".btn-edit")) {
            const idHash = e.target.closest(".btn-edit").dataset.id;
            window.location.href = "upd_categoria.php?hash=" + idHash;
          } else if (e.target.closest(".btn-delete")) {
            if (confirm("¿Seguro que deseas eliminar el registro " + id + "?")) {
              fetch("controller/delete_categoria.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + encodeURIComponent(id),
              })
                .then((res) => res.json())
                .then((json) => {
                  if (json.ok) {
                    alertify.success("✅ Registro suspendido correctamente");
                    table.replaceData();
                  } else {
                    alertify.error("❌ Error al suspender: " + json.message);
                  }
                })
                .catch((err) => {
                  console.error(err);
                  alertify.error("❌ Error de red al suspender");
                });
            }
          }
        },
      },
    ],
  });

  document.getElementById("download-csv")?.addEventListener("click", function () {
    table.download("csv", "categorias.csv");
  });

  document.getElementById("download-json")?.addEventListener("click", function () {
    table.download("json", "categorias.json");
  });

  document.getElementById("download-xlsx")?.addEventListener("click", function () {
    table.download("xlsx", "categorias.xlsx", { sheetName: "Categorias" });
  });

  document.getElementById("download-pdf")?.addEventListener("click", function () {
    table.download("pdf", "categorias.pdf", {
      orientation: "portrait",
      title: "Reporte de Categorías",
    });
  });

  document.getElementById("download-html")?.addEventListener("click", function () {
    table.download("html", "categorias.html", { style: true });
  });
})();
