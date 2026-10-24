(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-proveedor");
    const tableElement = document.getElementById("tabla-proveedores");
    const trigger = document.getElementById("abrir-modal-proveedor");
    const modalClose = document.querySelector('#modal-proveedor [data-hs-overlay="#modal-proveedor"]');
    const errorBox = document.getElementById("proveedor-form-error");
    const saveButton = document.getElementById("btnGuardarProveedor");
    const typeSelect = document.getElementById("proveedor-tipo-documento");
    const documentInput = document.getElementById("proveedor-documento");
    const documentStatus = document.getElementById("proveedor-documento-status");
    const nameInput = document.getElementById("proveedor-razon-social");
    const stateToggle = document.getElementById("proveedor-estado-toggle");
    const stateInput = document.getElementById("proveedor-estado");
    const reloadButton = document.getElementById("btnRecargarProveedores");
    const reloadIcon = document.getElementById("iconRecargarProveedores");
    const reloadLabel = document.getElementById("textoRecargarProveedores");
    if (!form || !tableElement) return;

    const escapeHtml = (value) => String(value ?? "").replace(/[&<>"']/g, (character) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
    }[character]));
    const tooltip = (text) => window.XintraTooltip?.attr(text) || `aria-label="${escapeHtml(text)}"`;

    const setState = (active) => {
      stateInput.value = active ? "1" : "0";
      stateToggle.classList.toggle("on", active);
      stateToggle.setAttribute("aria-checked", String(active));
    };
    stateToggle.addEventListener("click", () => setState(stateInput.value !== "1"));
    stateToggle.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        setState(stateInput.value !== "1");
      }
    });

    let lookupSequence = 0;
    let lastLookupDocument = "";
    let autoFilledDocument = "";

    const setDocumentStatus = (message = "", state = "") => {
      documentStatus.textContent = message;
      documentStatus.classList.remove("text-textmuted", "text-primary", "text-success", "text-danger");
      documentStatus.classList.add(state === "loading" ? "text-primary" : state === "success" ? "text-success" : state === "error" ? "text-danger" : "text-textmuted");
    };

    const syncDocumentRules = () => {
      const isRuc = typeSelect.value === "RUC";
      const length = isRuc ? 11 : 8;
      documentInput.maxLength = length;
      documentInput.pattern = `[0-9]{${length}}`;
      documentInput.placeholder = `${length} dígitos`;
      documentInput.value = documentInput.value.replace(/\D/g, "").slice(0, length);
    };

    const consultarDocumento = async () => {
      const tipo = typeSelect.value;
      const documento = documentInput.value;
      const longitud = tipo === "RUC" ? 11 : 8;
      if (documento.length !== longitud) {
        lastLookupDocument = "";
        setDocumentStatus("");
        return;
      }
      const queryKey = `${tipo}:${documento}`;
      if (queryKey === lastLookupDocument) return;
      lastLookupDocument = queryKey;
      const currentSequence = ++lookupSequence;
      setDocumentStatus(`Consultando ${tipo}…`, "loading");

      try {
        const endpoint = tipo === "RUC"
          ? `controller/lookup_proveedor_ruc.php?ruc=${encodeURIComponent(documento)}`
          : `config/api.php?dni=${encodeURIComponent(documento)}`;
        const response = await fetch(endpoint);
        const data = await response.json();
        if (currentSequence !== lookupSequence || documentInput.value !== documento || typeSelect.value !== tipo) return;
        if (!response.ok || data?.ok === false || data?.error) {
          throw new Error(data?.message || data?.error || `No se encontró información para ese ${tipo}.`);
        }

        const nombre = tipo === "RUC"
          ? String(data.nombre || data.name || "").trim()
          : [data.NOMBRES, data.PATERNO, data.MATERNO].filter(Boolean).join(" ").trim();
        if (!nombre) throw new Error(`La consulta no devolvió el nombre asociado al ${tipo}.`);
        nameInput.value = nombre.slice(0, 150);
        autoFilledDocument = documento;
        setDocumentStatus(`${tipo} encontrado: nombre completado automáticamente.`, "success");
      } catch (error) {
        if (currentSequence !== lookupSequence) return;
        setDocumentStatus(error.message || `No se pudo consultar el ${tipo}.`, "error");
      }
    };

    typeSelect.addEventListener("change", () => {
      lookupSequence++;
      lastLookupDocument = "";
      if (autoFilledDocument) {
        nameInput.value = "";
        autoFilledDocument = "";
      }
      syncDocumentRules();
      consultarDocumento();
    });
    documentInput.addEventListener("input", () => {
      documentInput.value = documentInput.value.replace(/\D/g, "").slice(0, Number(documentInput.maxLength));
      if (autoFilledDocument && documentInput.value !== autoFilledDocument) {
        nameInput.value = "";
        autoFilledDocument = "";
      }
      consultarDocumento();
    });

    const table = new Tabulator(tableElement, {
      layout: "fitColumns",
      pagination: "local",
      paginationSize: 10,
      paginationSizeSelector: [5, 10, 15, 20, 25],
      paginationCounter: "rows",
      movableColumns: true,
      ajaxURL: "controller/table_proveedor.php",
      ajaxResponse: (_url, _params, response) => Array.isArray(response) ? response : [],
      columns: [
        { title: "ID", field: "id", sorter: "number", width: 80 },
        { title: "Proveedor", field: "razon_social", headerFilter: "input", minWidth: 180, widthGrow: 2,
          formatter: (cell) => escapeHtml(cell.getValue()) },
        { title: "Tipo", field: "tipo_documento", headerFilter: "select", headerFilterParams: { values: { "": "Todos", RUC: "RUC", DNI: "DNI" } }, width: 95 },
        { title: "Documento", field: "documento", headerFilter: "input", width: 135 },
        { title: "Correo", field: "email", headerFilter: "input", minWidth: 160, formatter: (cell) => escapeHtml(cell.getValue() || "—") },
        { title: "Teléfono", field: "telefono", width: 130, formatter: (cell) => escapeHtml(cell.getValue() || "—") },
        { title: "Dirección", field: "direccion", minWidth: 150, widthGrow: 1, formatter: (cell) => escapeHtml(cell.getValue() || "—") },
        { title: "Estado", field: "estado", width: 105,
          formatter: (cell) => Number(cell.getValue()) === 1
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>',
          accessorDownload: (value) => Number(value) === 1 ? "Activo" : "Inactivo" },
        {
          title: "Acciones", field: "acciones", width: 135, hozAlign: "center", headerSort: false,
          formatter: (cell) => {
            const row = cell.getRow().getData();
            const active = Number(row.estado) === 1;
            return `<div class="flex items-center justify-center gap-2">
              <button type="button" class="btn-editar-proveedor ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full" data-id="${Number(row.id)}" aria-label="Editar proveedor" ${tooltip("Editar proveedor")}>
                <i class="ri-edit-2-line" aria-hidden="true"></i>
              </button>
              <button type="button" class="btn-estado-proveedor ti-btn ti-btn-icon ${active ? "bg-danger/10 text-danger hover:bg-danger hover:text-white" : "bg-success/10 text-success hover:bg-success hover:text-white"} !rounded-full" data-id="${Number(row.id)}" data-state="${active ? 0 : 1}" aria-label="${active ? "Desactivar" : "Activar"} proveedor" ${tooltip(active ? "Desactivar proveedor" : "Activar proveedor")}>
                <i class="${active ? "ri-user-unfollow-line" : "ri-user-follow-line"}" aria-hidden="true"></i>
              </button>
            </div>`;
          },
          cellClick: (event, cell) => {
            const row = cell.getRow().getData();
            if (event.target.closest(".btn-editar-proveedor")) {
              abrirFormulario(row);
              return;
            }
            const stateButton = event.target.closest(".btn-estado-proveedor");
            if (!stateButton) return;
            const nextState = Number(stateButton.dataset.state);
            const action = nextState ? "activar" : "desactivar";
            alertify.confirm(
              "Cambiar estado del proveedor",
              `¿Deseas ${action} «${row.razon_social}»?`,
              async () => {
                stateButton.disabled = true;
                try {
                  const response = await fetch("controller/estado_proveedor.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                    body: new URLSearchParams({ id: String(row.id), estado: String(nextState) }),
                  });
                  const result = await response.json();
                  if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo cambiar el estado.");
                  alertify.success(result.message);
                  await table.replaceData();
                } catch (error) {
                  alertify.error(error.message || "No se pudo cambiar el estado.");
                  stateButton.disabled = false;
                }
              },
              () => alertify.message("Acción cancelada.")
            ).set("labels", { ok: "Confirmar", cancel: "Cancelar" });
          },
        },
      ],
    });
    const initTooltips = () => window.XintraTooltip?.init(tableElement);
    table.on("tableBuilt", initTooltips);
    table.on("renderComplete", initTooltips);

    const abrirFormulario = (row = null) => {
      form.reset();
      errorBox.textContent = "";
      errorBox.classList.add("hidden");
      document.getElementById("proveedor-id").value = row?.id || "";
      typeSelect.value = row?.tipo_documento || "RUC";
      documentInput.value = row?.documento || "";
      document.getElementById("proveedor-razon-social").value = row?.razon_social || "";
      document.getElementById("proveedor-email").value = row?.email || "";
      document.getElementById("proveedor-telefono").value = row?.telefono || "";
      document.getElementById("proveedor-direccion").value = row?.direccion || "";
      documentStatus.textContent = "";
      documentStatus.classList.remove("text-primary", "text-success", "text-danger");
      documentStatus.classList.add("text-textmuted");
      lastLookupDocument = "";
      autoFilledDocument = "";
      lookupSequence++;
      setState(row ? Number(row.estado) === 1 : true);
      syncDocumentRules();
      document.getElementById("modal-proveedor-titulo").textContent = row ? "Editar proveedor" : "Nuevo proveedor";
      saveButton.textContent = row ? "Guardar cambios" : "Guardar proveedor";
      trigger.click();
      document.getElementById("proveedor-razon-social").focus();
    };

    document.getElementById("btnNuevoProveedor").addEventListener("click", () => abrirFormulario());
    reloadButton.addEventListener("click", async () => {
      reloadButton.disabled = true;
      reloadIcon.classList.remove("ri-refresh-line");
      reloadIcon.classList.add("ri-loader-4-line", "animate-spin");
      reloadLabel.textContent = "Cargando…";
      try {
        await table.replaceData();
        alertify.success("Listado actualizado.");
      } catch (_error) {
        alertify.error("No se pudo actualizar el listado.");
      } finally {
        reloadButton.disabled = false;
        reloadIcon.classList.remove("ri-loader-4-line", "animate-spin");
        reloadIcon.classList.add("ri-refresh-line");
        reloadLabel.textContent = "Actualizar";
      }
    });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      errorBox.textContent = "";
      errorBox.classList.add("hidden");
      if (!form.reportValidity()) return;
      saveButton.disabled = true;
      saveButton.classList.add("opacity-50", "cursor-not-allowed");
      const id = document.getElementById("proveedor-id").value;
      try {
        const response = await fetch(id ? "controller/upd_proveedor.php" : "controller/add_proveedor.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
          body: new URLSearchParams(new FormData(form)),
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo guardar el proveedor.");
        alertify.success(result.message);
        modalClose?.click();
        await table.replaceData();
      } catch (error) {
        errorBox.textContent = error.message || "No se pudo guardar el proveedor.";
        errorBox.classList.remove("hidden");
      } finally {
        saveButton.disabled = false;
        saveButton.classList.remove("opacity-50", "cursor-not-allowed");
      }
    });
  });
})();
