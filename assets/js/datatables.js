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

    //Download Data from Tabulator
    //Build Tabulator
    var table = new Tabulator("#download-table", {
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
        paginationSizeSelector: [5, 10, 15, 20, 25],
        paginationCounter: "rows",
        movableColumns: true,
        reactiveData: true, 
        ajaxURL: "php/table_cliente.php",
        columns: [
            { title: "Id", field: "id", sorter: "number", width: 90 },
            { title: "Nombre Completo", field: "nombre_completo", headerFilter: "input", widthGrow: 2, minWidth: 100 },
            { title: "Documento", field: "documento", headerFilter: "input"},
            { title: "Contacto", field: "contacto"},
            {
                title: "Sexo",
                field: "sexo",
                formatter: (cell) => {
                    const v = String(cell.getValue()).trim();
                    const src = (v === "1") ? "assets/images/faces/10.jpg"
                                            : "assets/images/faces/2.jpg";
                    return `<img src="${src}" alt="sexo ${v}" style="height:32px;width:32px;border-radius:9999px;object-fit:cover;" />`;
                },
                accessorDownload: (value) => value,
            },
            { title: "Fec. Registro", field: "fecha_creacion", sorter: "datetime",
            sorterParams:{format:"YYYY-MM-DD HH:mm:ss"} },
        ],
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

    //trigger download of data.html file
    document.getElementById("download-html").addEventListener("click", function () {
        table.download("html", "data.html", { style: true });
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


