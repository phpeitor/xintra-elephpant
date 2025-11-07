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
            { title: "Id",        field: "id", sorter: "number", width: 100 },
            { title: "Fecha",     field: "fecha_pedido", width: 140 },
            { title: "Personal",  field: "usuario", headerFilter: "input", width: 120},
            { title: "Cliente",   field: "cliente", headerFilter: "input", width: 180 },
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
                        <button class="btn-pdf ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full btn-wave me-5 waves-effect waves-light" data-id="${idHash}">
                            <i class="ri-file-pdf-2-line"></i>
                        </button>
                    </div>`;
                },
                cellClick: function (e, cell) {
                    const id = cell.getRow().getData().id;
                    if (e.target.closest(".btn-edit")) {
                        const idHash = e.target.closest(".btn-edit").dataset.id;
                        window.location.href = "upd_ticket.php?hash=" + idHash;
                    } else if (e.target.closest(".btn-pdf")) {
                        if (confirm("¿Deseas generar ticket pdf " + id + "?")) {
                            const idHash = e.target.closest(".btn-pdf").dataset.id;
                            alertify.confirm(
                                'Generar Ticket PDF',
                                '¿Deseas generar el ticket PDF?',
                                function () {
                                    window.open("controller/venta/tkt_pdf.php?hash=" + idHash, "_blank");
                                },
                                function () {
                                    alertify.error('Acción cancelada');
                                }
                            ).set('labels', { ok: 'Sí', cancel: 'No' });
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


