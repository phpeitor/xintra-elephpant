(function () {
    "use strict";
    document.addEventListener('DOMContentLoaded', function () {
        var genericExamples = document.querySelectorAll('[data-trigger]');
        for (let i = 0; i < genericExamples.length; ++i) {
            var element = genericExamples[i];
            new Choices(element, {
                allowHTML: false,
            });
        }
    });

    const formatDateLocal = (d) => {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, "0");
        const dd = String(d.getDate()).padStart(2, "0");
        return `${yyyy}-${mm}-${dd}`;
    };

    const hoy = new Date();
    const hace7dias = new Date();
    hace7dias.setDate(hoy.getDate() - 7);

    const urlInicial =
    `controller/venta/table_ticket.php?fecha_inicio=${encodeURIComponent(formatDateLocal(hace7dias))}&fecha_fin=${encodeURIComponent(formatDateLocal(hoy))}`;


    var table = new Tabulator("#download-table", {
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
        paginationSizeSelector: [5, 10, 15, 20, 25],
        paginationCounter: "rows",
        movableColumns: true,
        reactiveData: true,
        ajaxURL: urlInicial,
        ajaxResponse: function (url, params, response) {
        return response;
        },

        columns: [
            { title: "Id", field: "id", sorter: "number", width: 100 },
            { title: "Fecha", field: "fecha_pedido", width: 140 },
            { title: "Personal", field: "usuario", headerFilter: "input", width: 120},
            { title: "Cliente", field: "cliente", headerFilter: "input", width: 180 },
            { title: "Items",     field: "productos",  formatter: "html", cssClass: "wrap" },
            { title: "Subtotal",  field: "precioxcant",formatter: "html", cssClass: "wrap" },
            { title: "Total",     field: "total",      formatter: "html", cssClass: "wrap", width: 120 },
            {
            title: "Opciones",
            field: "acciones",
            hozAlign: "center",
            headerSort: false,
            width: 160,
            formatter: (cell) => {
                const row = cell.getRow().getData();
                const id = row.id;
                const idHash = md5(id.toString());
                return `
                <div style="display:flex;gap:.5rem;justify-content:center;">
                    <button class="btn-edit ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full btn-wave waves-effect waves-light" data-id="${idHash}">
                    <i class="ri-edit-2-line"></i>
                    </button>
                    <button class="btn-delete ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full btn-wave me-5 waves-effect waves-light" data-id="${id}">
                    <i class="ri-delete-bin-line"></i>
                    </button>
                </div>`;
            },
            cellClick: function (e, cell) {
                const id = cell.getRow().getData().id;
                if (e.target.closest(".btn-edit")) {
                const idHash = e.target.closest(".btn-edit").dataset.id;
                window.location.href = "upd_ticket.php?hash=" + idHash;
                } else if (e.target.closest(".btn-delete")) {
                if (confirm("¿Seguro que deseas eliminar el registro " + id + "?")) {
                    fetch("controller/delete_usuario.php", {
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

    const btnBuscar = document.querySelector(".input-group button i.ri-search-line").closest("button");
    btnBuscar.addEventListener("click", () => {
        const rango = document.querySelector("#daterange").value;
        if (!rango) return alertify.error("Selecciona un rango de fechas");

        const [fechaInicio, fechaFin] = rango.split(/ to | - /).map(s => s.trim());
        if (!fechaInicio || !fechaFin) return alertify.error("Rango inválido");

        const url = `controller/venta/table_ticket.php?fecha_inicio=${encodeURIComponent(fechaInicio)}&fecha_fin=${encodeURIComponent(fechaFin)}`;
        table.setData(url).catch(err => {
            console.error("Error recargando datos:", err);
            alertify.error("No se pudo cargar el rango seleccionado");
        });
    });

    //trigger download of data.csv file
    document.getElementById("download-csv").addEventListener("click", function () {
        table.download("csv", "data.csv");
    });

    //trigger download of data.json file
    document.getElementById("download-json").addEventListener("click", function () {
        table.download("json", "data.json");
    });

    //trigger download of data.xlsx file
    document.getElementById("download-xlsx").addEventListener("click", function () {
        table.download("xlsx", "data.xlsx", { sheetName: "My Data" });
    });

    //trigger download of data.pdf file
    document.getElementById("download-pdf").addEventListener("click", function () {
        table.download("pdf", "data.pdf", {
            orientation: "portrait", //set page orientation to portrait
            title: "Example Report", //add title to report
        });
    });


    document.querySelector("#switcher-rtl").addEventListener("click",()=>{
        document.querySelectorAll(".tabulator").forEach((ele)=>{
            ele.classList.add("tabulator-rtl")
        })
    })

    document.querySelector("#switcher-ltr").addEventListener("click",()=>{
        document.querySelectorAll(".tabulator").forEach((ele)=>{
            ele.classList.remove("tabulator-rtl")
        })
    })

    document.querySelector("#reset-all").addEventListener("click",()=>{
        document.querySelectorAll(".tabulator").forEach((ele)=>{
            ele.classList.remove("tabulator-rtl")
        })
    })

})();


