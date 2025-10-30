let table;

document.addEventListener("DOMContentLoaded", function () {
    const filtroTipo = document.getElementById("filtroTipo");

    function crearTabla(tpo = "") {
        if (table) table.destroy();

        const columnsBase = [
            { title: "Id", field: "id", sorter: "number", width: 90 },
            { title: "Categoria", field: "nom_categoria", headerFilter: "input" },
            { title: "Nombre", field: "nombre", headerFilter: "input", widthGrow: 2, minWidth: 100 },
        ];

        if (tpo === "PRODUCTO") {
            columnsBase.push({
                title: "Stock",
                field: "stock_final",
                sorter: "number",
                formatter: (cell) => {
                    const value = parseFloat(cell.getValue() ?? 0);
                    let badgeClass = "badge !rounded-full bg-outline-info"; 

                    if (value <= 6) badgeClass = "badge !rounded-full bg-outline-danger";
                    else if (value <= 12) badgeClass = "badge !rounded-full bg-outline-warning";

                    return `<span class="${badgeClass}">${value}</span>`;
                },
                accessorDownload: (value) => value ?? 0,
            });
        }

        columnsBase.push({ title: "Precio", field: "precio", sorter: "number" });
        columnsBase.push(
            {
                title: "Estado",
                field: "estado",
                formatter: (cell) => {
                    const v = String(cell.getValue() ?? "").trim();
                    if (v === "1") return `<span class="badge bg-success">ACTIVE</span>`;
                    if (v === "0") return `<span class="badge bg-danger">SUSPENDED</span>`;
                    return `<span class="badge bg-outline-dark">NDF</span>`;
                },
                accessorDownload: (value) =>
                    String(value) === "1"
                        ? "ACTIVE"
                        : String(value) === "0"
                        ? "SUSPENDED"
                        : "NDF",
            },
            {
                title: "Fec. Registro",
                field: "fecha_creacion",
                sorter: "datetime",
                sorterParams: { format: "YYYY-MM-DD HH:mm:ss" },
            },
            {
                title: "Opciones",
                field: "acciones",
                hozAlign: "center",
                headerSort: false,
                width: 160,
                formatter: (cell) => {
                    const id = cell.getRow().getData().id;
                    const idHash = md5(id.toString());
                    return `
                        <div style="display:flex;gap:.5rem;justify-content:center;">
                            <button class="btn-edit ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full" data-id="${idHash}">
                                <i class="ri-edit-2-line"></i>
                            </button>
                            <button class="btn-delete ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full" data-id="${id}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>`;
                },
                cellClick: function (e, cell) {
                    const id = cell.getRow().getData().id;
                    if (e.target.closest(".btn-edit")) {
                        console.log("Actualizar ID:", id);
                        const idHash = e.target.closest(".btn-edit").dataset.id;
                        window.location.href = "upd_item.html?hash=" + idHash;
                    } else if (e.target.closest(".btn-delete")) {

                        if (confirm("¿Seguro que deseas eliminar el registro " + id + "?")) {
                            fetch("php/delete_item.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body: "id=" + encodeURIComponent(id),
                            })
                            .then((res) => res.json())
                            .then((json) => {
                                if (json.ok) {
                                    alert("✅ Registro suspendido correctamente");
                                    table.replaceData();
                                } else {
                                    alert("❌ Error al suspender: " + json.message);
                                }
                            })
                            .catch((err) => {
                                console.error(err);
                                alert("❌ Error de red al suspender");
                            });
                        }
                    }
                },
            }
        );

        table = new Tabulator("#download-table", {
            layout: "fitColumns",
            pagination: "local",
            paginationSize: 10,
            ajaxURL: `php/table_item.php?tpo=${tpo}`,
            columns: columnsBase,
        });
    }

    crearTabla();

    filtroTipo.addEventListener("change", (e) => {
        crearTabla(e.target.value);
    });

    document.getElementById("download-csv").addEventListener("click", function () {
        table.download("csv", "data.csv");
    });
});
