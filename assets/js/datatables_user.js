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

    async function cargarCatalogosAsignacion(row, button) {
        const sucursalSelect = document.getElementById("asignacion-sucursal");
        const cargoSelect = document.getElementById("asignacion-cargo");
        const error = document.getElementById("asignacion-usuario-error");
        sucursalSelect.disabled = true;
        cargoSelect.disabled = true;
        button.disabled = true;
        try {
            const response = await fetch("controller/catalogos_usuario.php");
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || "No se pudieron cargar las opciones.");

            const fillSelect = (select, rows, selectedId) => {
                select.replaceChildren();
                rows.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = String(item.id);
                    option.textContent = item.nombre;
                    select.appendChild(option);
                });
                select.value = String(selectedId ?? "");
            };

            fillSelect(sucursalSelect, result.data.sucursales || [], row.IDSUCURSAL);
            fillSelect(cargoSelect, result.data.cargos || [], row.CARGO);
        } catch (loadError) {
            error.textContent = loadError.message || "No se pudieron cargar las opciones.";
            error.classList.remove("hidden");
        } finally {
            sucursalSelect.disabled = false;
            cargoSelect.disabled = false;
            button.disabled = false;
        }
    }

    var table = new Tabulator("#download-table", {
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
        paginationSizeSelector: [5, 10, 15, 20, 25],
        paginationCounter: "rows",
        movableColumns: true,
        reactiveData: true,
        ajaxURL: "controller/table_usuario.php?estado=ACTIVOS",

        ajaxResponse: function(url, params, response) {
            return response;
        },

        columns: [
            { title: "Id", field: "IDPERSONAL", sorter: "number", width: 90 },
            { title: "Nombre Completo", field: "nombre_completo", headerFilter: "input", widthGrow: 2, minWidth: 100 },
            { title: "Usuario", field: "USUARIO", headerFilter: "input", minWidth: 130 },
            {
                title: "Password",
                field: "password_configurada",
                width: 145,
                formatter: (cell) => {
                    const configured = Number(cell.getValue()) === 1;
                    const message = configured
                        ? "Hash MD5 guardado. No se expone en el navegador por seguridad."
                        : "El usuario no tiene contraseña configurada.";
                    const tooltip = window.XintraTooltip?.attr(message) || `aria-label="${message}"`;
                    return `<span class="badge ${configured ? "bg-success/10 text-success" : "bg-warning/10 text-warning"}" ${tooltip}>${configured ? "Configurada" : "No configurada"}</span>`;
                },
                accessorDownload: (value) => Number(value) === 1 ? "Configurada" : "No configurada",
            },
            { title: "Documento", field: "DOC", headerFilter: "input" , minWidth: 130},
            { title: "Cargo", field: "CARGO_NOMBRE", headerFilter: "input", width: 130 },
            {
                title: "Sexo",
                field: "SEXO",
                formatter: (cell) => {
                    const v = String(cell.getValue() ?? "").trim();
                    if (v === "1") {
                    return `
                        <div style="display:flex;align-items:center;gap:.5rem;">
                        <img src="assets/images/faces/10.jpg" alt="Masculino"
                            style="height:32px;width:32px;border-radius:9999px;object-fit:cover;" />
                        <span class="badge bg-primary">Masculino</span>
                        </div>`;
                    }
                    if (v === "2") {
                    return `
                        <div style="display:flex;align-items:center;gap:.5rem;">
                        <img src="assets/images/faces/2.jpg" alt="Femenino"
                            style="height:32px;width:32px;border-radius:9999px;object-fit:cover;" />
                        <span class="badge bg-primarytint1color">Femenino</span>
                        </div>`;
                    }
                    return `<span class="badge bg-outline-dark dark:!text-defaulttextcolor/70">Otro</span>`;
                },
                accessorDownload: (value) => (String(value) === "1" ? "Masculino" : String(value) === "2" ? "Femenino" : "Otro"), minWidth: 130
            },
            {
                title: "Estado",
                field: "IDESTADO",
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
                accessorDownload: (value) => (String(value) === "1" ? "ACTIVE" : String(value) === "0" ? "SUSPENDED" : "NDF"), minWidth: 130
            },
            { title: "Fec. Registro", field: "fecha_registro", sorter: "datetime",
            sorterParams:{format:"YYYY-MM-DD HH:mm:ss"} , minWidth: 150},
            {
                title: "Opciones",
                field: "acciones",
                hozAlign: "center",
                headerSort: false,
                width: 320,
                formatter: (cell) => {
                    const row = cell.getRow().getData();
                    const id = row.IDPERSONAL;
                    const estado = String(row.IDESTADO ?? "").trim();
                    const puedeEliminar = estado !== "0" && estado !== "SUSPENDED";
                    const usuarioActual = Number(document.getElementById("download-table")?.dataset.currentUserId || 0);
                    const idHash = md5(row.IDPERSONAL.toString()); 
                    return `
                     <div style="display:flex;align-items:center;justify-content:flex-start;gap:.5rem;width:100%;">
                        <button class="btn-schedule ti-btn ti-btn-icon bg-info/10 text-info hover:bg-info hover:text-white !rounded-full btn-wave waves-effect waves-light" data-id="${idHash}" aria-label="Configurar horario" ${window.XintraTooltip.attr("Horario")}>
                            <i class="ri-time-line"></i>
                        </button>
                        <button class="btn-edit ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full btn-wave waves-effect waves-light" data-id="${idHash}" aria-label="Editar usuario" ${window.XintraTooltip.attr("Editar")}>
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn-sucursal-cargo ti-btn ti-btn-icon bg-info/10 text-info hover:bg-info hover:text-white !rounded-full btn-wave waves-effect waves-light" data-id="${id}" aria-label="Cambiar sucursal y cargo" ${window.XintraTooltip.attr("Sucursal y cargo")}>
                            <i class="ri-building-2-line"></i>
                        </button>
                        ${Number(id) !== usuarioActual ? `<button class="btn-permisos ti-btn ti-btn-icon bg-warning/10 text-warning hover:bg-warning hover:text-white !rounded-full btn-wave waves-effect waves-light" data-id="${id}" aria-label="Permisos" ${window.XintraTooltip.attr("Permisos")}><i class="ri-shield-user-line"></i></button>` : ""}
                        ${puedeEliminar ? `<button class="btn-delete ti-btn ti-btn-icon bg-danger/10 text-danger hover:bg-danger hover:text-white !rounded-full btn-wave waves-effect waves-light" data-id="${id}" aria-label="Eliminar usuario" ${window.XintraTooltip.attr("Eliminar")}><i class="ri-delete-bin-line"></i></button>` : ""}
                    </div>`;
                },
                cellClick: function (e, cell) {
                    const row = cell.getRow().getData();
                    const id = row.IDPERSONAL;
                    if (e.target.closest(".btn-edit")) {
                        console.log("Actualizar ID:", id);
                        const idHash = e.target.closest(".btn-edit").dataset.id;
                        window.location.href = "upd_usuario.php?hash=" + idHash;
                    } else if (e.target.closest(".btn-schedule")) {
                        const idHash = e.target.closest(".btn-schedule").dataset.id;
                        window.location.href = "horario.php?hash=" + idHash;
                    } else if (e.target.closest(".btn-sucursal-cargo")) {
                        const assignmentButton = e.target.closest(".btn-sucursal-cargo");
                        const assignmentModal = document.getElementById("abrir-modal-asignacion-usuario");
                        const assignmentError = document.getElementById("asignacion-usuario-error");
                        document.getElementById("asignacion-usuario-id").value = String(id);
                        document.getElementById("asignacion-usuario-nombre").textContent = cell.getRow().getData().nombre_completo || "";
                        assignmentError.textContent = "";
                        assignmentError.classList.add("hidden");
                        assignmentModal.click();
                        cargarCatalogosAsignacion(cell.getRow().getData(), assignmentButton);
                    } else if (e.target.closest(".btn-permisos")) {
                        abrirPermisosUsuario(cell.getRow().getData());
                    } else if (e.target.closest(".btn-delete")) {
                        alertify.confirm(
                            "Eliminar usuario",
                            "¿Seguro que deseas eliminar el registro " + id + "?",
                            function () {
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
                            },
                            function () {
                                alertify.message("Acción cancelada");
                            }
                        ).set("labels", { ok: "Aceptar", cancel: "Cancelar" });
                    }
                },
            },
        ],
    });

    table.on("renderComplete", () => {
        window.XintraTooltip?.init(document.querySelector("#download-table"));
    });

    const assignmentForm = document.getElementById("form-asignacion-usuario");
    const assignmentSaveButton = document.getElementById("btnGuardarAsignacionUsuario");
    assignmentForm?.addEventListener("submit", (event) => {
        event.preventDefault();
        const assignmentError = document.getElementById("asignacion-usuario-error");
        assignmentError.textContent = "";
        assignmentError.classList.add("hidden");
        if (!assignmentForm.reportValidity()) return;

        const payload = Object.fromEntries(new FormData(assignmentForm).entries());
        alertify.confirm(
            "Confirmar sucursal y cargo",
            `Se actualizarán la sucursal y el cargo del usuario #${payload.id}. Si la sucursal cambia, se comprobará que no tenga tickets ni asistencias históricas. ¿Continuar?`,
            async () => {
                assignmentSaveButton.disabled = true;
                assignmentSaveButton.classList.add("opacity-50", "cursor-not-allowed");
                try {
                    const response = await fetch("controller/asignar_sucursal_cargo.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                        body: new URLSearchParams(payload),
                    });
                    const result = await response.json();
                    if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo cambiar la sucursal y el cargo.");
                    alertify.success(result.message);
                    document.querySelector('#modal-asignacion-usuario [data-hs-overlay="#modal-asignacion-usuario"]')?.click();
                    table.replaceData();
                } catch (error) {
                    assignmentError.textContent = error.message || "No se pudo cambiar la sucursal y el cargo.";
                    assignmentError.classList.remove("hidden");
                } finally {
                    assignmentSaveButton.disabled = false;
                    assignmentSaveButton.classList.remove("opacity-50", "cursor-not-allowed");
                }
            },
            () => alertify.message("Cambio cancelado.")
        ).set("labels", { ok: "Guardar", cancel: "Cancelar" });
    });

    const permissionForm = document.getElementById("form-permisos-usuario");
    const permissionSaveButton = document.getElementById("btnGuardarPermisosUsuario");
    async function abrirPermisosUsuario(row) {
        const error = document.getElementById("permisos-usuario-error");
        const list = document.getElementById("permisos-usuario-lista");
        document.getElementById("permisos-usuario-id").value = String(row.IDPERSONAL);
        document.getElementById("permisos-usuario-nombre").textContent = row.nombre_completo || "";
        error.textContent = "";
        error.classList.add("hidden");
        list.textContent = "Cargando permisos…";
        document.getElementById("abrir-modal-permisos-usuario").click();
        try {
            const response = await fetch(`controller/permisos_usuario.php?id=${encodeURIComponent(row.IDPERSONAL)}`);
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || "No se pudieron cargar los permisos.");
            const selected = new Set(result.data.permisos);
            list.replaceChildren();
            Object.entries(result.data.catalogo).forEach(([key, label]) => {
                const wrapper = document.createElement("label");
                wrapper.className = "flex items-center gap-2 p-2 border border-defaultborder rounded-sm";
                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "permisos[]";
                checkbox.value = key;
                checkbox.checked = selected.has(key);
                checkbox.className = "ti-form-checkbox";
                const text = document.createElement("span");
                text.textContent = label;
                wrapper.append(checkbox, text);
                list.appendChild(wrapper);
            });
        } catch (loadError) {
            list.replaceChildren();
            error.textContent = loadError.message || "No se pudieron cargar los permisos.";
            error.classList.remove("hidden");
        }
    }

    permissionForm?.addEventListener("submit", async (event) => {
        event.preventDefault();
        const error = document.getElementById("permisos-usuario-error");
        error.textContent = "";
        error.classList.add("hidden");
        permissionSaveButton.disabled = true;
        try {
            const response = await fetch("controller/permisos_usuario.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                body: new URLSearchParams(new FormData(permissionForm)),
            });
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || "No se pudieron guardar los permisos.");
            alertify.success(result.message);
            document.querySelector('#modal-permisos-usuario [data-hs-overlay="#modal-permisos-usuario"]')?.click();
        } catch (saveError) {
            error.textContent = saveError.message || "No se pudieron guardar los permisos.";
            error.classList.remove("hidden");
        } finally {
            permissionSaveButton.disabled = false;
        }
    });

    document.querySelector("#user-status-filter")?.addEventListener("change", (event) => {
        const estado = encodeURIComponent(event.target.value);
        table.setData(`controller/table_usuario.php?estado=${estado}`);
    });

    //trigger download of data.xlsx file
    document.getElementById("download-xlsx")?.addEventListener("click", function () {
        table.download("xlsx", "data.xlsx", { sheetName: "My Data" });
    });

    //trigger download of data.pdf file
    document.getElementById("download-pdf")?.addEventListener("click", function () {
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


