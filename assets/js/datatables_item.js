(function () {
    "use strict";
    /* Start::Choices JS */
    document.addEventListener('DOMContentLoaded', function () {
        var genericExamples = document.querySelectorAll('[data-trigger]');
        for (let i = 0; i < genericExamples.length; ++i) {
            var element = genericExamples[i];
            new Choices(element, {
                allowHTML: false,
            });
        }
    });

    var table = new Tabulator("#download-table", {
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
        paginationSizeSelector: [5, 10, 15, 20, 25],
        paginationCounter: "rows",
        movableColumns: true,
        reactiveData: true,
        ajaxURL: "php/table_item.php",

        ajaxResponse: function(url, params, response) {
            return response;
        },

        columns: [
            { title: "Id", field: "id", sorter: "number", width: 90 },
            { title: "Categoria", field: "nom_categoria", headerFilter: "input" },
            { title: "Nombre", field: "nombre", headerFilter: "input", widthGrow: 2, minWidth: 100 },
            { title: "Precio", field: "precio"},
            {
                title: "Estado",
                field: "estado",
                formatter: (cell) => {
                    const v = String(cell.getValue() ?? "").trim();
                    if (v === "1") {
                    return `
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <span class="badge bg-success">ACTIVE</span>
                        </div>`;
                    }
                    if (v === "0") {
                    return `
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <span class="badge bg-danger">SUSPENDED</span>
                        </div>`;
                    }
                    return `<span class="badge bg-outline-dark dark:!text-defaulttextcolor/70">NDF</span>`;
                },
                accessorDownload: (value) => (String(value) === "1" ? "ACTIVE" : String(value) === "0" ? "SUSPENDED" : "NDF"),
            },
            { title: "Fec. Registro", field: "fecha_creacion", sorter: "datetime",
            sorterParams:{format:"YYYY-MM-DD HH:mm:ss"} },
            {
                title: "Opciones",
                field: "acciones",
                hozAlign: "center",
                headerSort: false,
                width: 160,
                formatter: (cell) => {
                    const id = cell.getRow().getData().id;
                    const row = cell.getRow().getData();
                    const idHash = md5(row.id.toString()); 
                    return `
                    <div style="display:flex;gap:.5rem;justify-content:center;">
                        <button class="btn-edit ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full btn-wave  waves-effect waves-light" data-id="${idHash}">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn-delete ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full btn-wave  me-5 waves-effect waves-light" data-id="${id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>`;
                },
                cellClick: function (e, cell) {
                    const id = cell.getRow().getData().id;
                    if (e.target.closest(".btn-edit")) {
                        console.log("Actualizar ID:", id);
                        const idHash = e.target.closest(".btn-edit").dataset.id;
                        window.location.href = "upd_usuario.html?hash=" + idHash;
                    } else if (e.target.closest(".btn-delete")) {

                        if (confirm("¿Seguro que deseas eliminar el registro " + id + "?")) {
                            fetch("php/delete_usuario.php", {
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
            },
        ],
    });

    //trigger download of data.csv file
    document.getElementById("download-csv").addEventListener("click", function () {
        table.download("csv", "data.csv");
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


