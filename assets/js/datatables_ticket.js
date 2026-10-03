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
                width: 290,
                formatter: (cell) => {
                    const row = cell.getRow().getData();
                    const id = row.id;
                    const idHash = md5(id.toString());
                    const comprobanteEstado = row.comprobante_estado || "";
                    let comprobanteAction = "";
                    const escapeAttribute = (value) => String(value ?? "").replace(/[&<>"']/g, (character) => ({
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': "&quot;",
                        "'": "&#39;",
                    }[character]));

                    if (comprobanteEstado === "EMITIDO") {
                        const pdfUrl = String(row.comprobante_pdf || "");
                        const safePdfUrl = /^https:\/\//i.test(pdfUrl)
                            ? escapeAttribute(pdfUrl)
                            : "";
                        const sunatNoAcepto = row.comprobante_aceptada === 0 || row.comprobante_aceptada === "0";
                        const linkClass = sunatNoAcepto
                            ? "bg-warning/10 text-warning hover:bg-warning hover:text-white"
                            : "bg-success/10 text-success hover:bg-success hover:text-white";
                        const comprobanteTitle = `${row.comprobante_serie}-${row.comprobante_numero}${sunatNoAcepto ? ` · SUNAT: ${row.comprobante_mensaje || "No aceptado"}` : " · Ver comprobante"}`;
                        comprobanteAction = safePdfUrl
                            ? `<a class="btn-comprobante-pdf ti-btn ti-btn-icon ${linkClass} !rounded-full" href="${safePdfUrl}" target="_blank" rel="noopener noreferrer" title="${escapeAttribute(comprobanteTitle)}"><i class="ri-file-list-3-line"></i></a>`
                            : `<span class="badge ${sunatNoAcepto ? "bg-warning" : "bg-success"}" title="${escapeAttribute(comprobanteTitle)}">${escapeAttribute(row.comprobante_serie)}-${escapeAttribute(row.comprobante_numero)}</span>`;
                    } else if (comprobanteEstado === "ERROR") {
                        comprobanteAction = `<button type="button" class="btn-comprobante-retry ti-btn ti-btn-sm ti-btn-outline-danger" title="${escapeAttribute(row.comprobante_mensaje || "Error de emisión")}"><i class="ri-refresh-line"></i> Reintentar</button>`;
                    } else if (comprobanteEstado === "PENDIENTE") {
                        comprobanteAction = `<button type="button" class="ti-btn ti-btn-sm ti-btn-light" disabled>Emitiendo…</button>`;
                    } else {
                        comprobanteAction = `<button type="button" class="btn-comprobante-emit ti-btn ti-btn-sm ti-btn-outline-success" title="Emitir boleta o factura"><i class="ri-bill-line"></i> Emitir</button>`;
                    }

                    return `
                    <div class="flex items-center justify-start gap-2 w-full">
                        <button class="btn-edit ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full btn-wave waves-effect waves-light" data-id="${idHash}">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn-pdf ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full btn-wave waves-effect waves-light" data-id="${idHash}">
                            <i class="ri-file-pdf-2-line"></i>
                        </button>
                        ${comprobanteAction}
                    </div>`;
                },
                cellClick: function (e, cell) {
                    const id = cell.getRow().getData().id;
                    if (e.target.closest(".btn-edit")) {
                        const idHash = e.target.closest(".btn-edit").dataset.id;
                        window.location.href = "upd_ticket.php?hash=" + idHash;
                    } else if (e.target.closest(".btn-pdf")) {
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
                    } else if (e.target.closest(".btn-comprobante-emit")) {
                        const row = cell.getRow().getData();
                        if (window.facturacionTicket) {
                            window.facturacionTicket.abrir(row);
                        } else {
                            alertify.error("No se pudo inicializar el formulario de facturación.");
                        }
                    } else if (e.target.closest(".btn-comprobante-retry")) {
                        const row = cell.getRow().getData();
                        window.facturacionTicket?.reintentar(row);
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

    document.getElementById("download-xlsx").addEventListener("click", async function () {
        const rango = document.querySelector("#daterange").value;

        if (!rango) return alertify.error("Selecciona un rango de fechas");

        const [fechaInicio, fechaFin] = rango.split(/ to | - /).map(s => s.trim());
        if (!fechaInicio || !fechaFin) return alertify.error("Rango inválido");

        const url = `controller/venta/excel.php?fecha_inicio=${encodeURIComponent(fechaInicio)}&fecha_fin=${encodeURIComponent(fechaFin)}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error("Error al obtener datos del servidor");

            const data = await res.json();
            if (!Array.isArray(data) || data.length === 0) {
                alertify.warning("No hay datos para exportar");
                return;
            }

            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Tickts");

            const nombreArchivo = `ticket_${fechaInicio}_a_${fechaFin}.xlsx`;
            XLSX.writeFile(wb, nombreArchivo);

            alertify.success("Excel generado correctamente");
        } catch (error) {
            console.error(error);
            alertify.error("Error generando el Excel");
        }
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


