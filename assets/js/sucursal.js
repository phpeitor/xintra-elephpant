(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const scrollToTop = document.querySelector(".scrollToTop");
    const actualizarScrollToTop = () => {
      if (scrollToTop) scrollToTop.style.display = window.scrollY > 100 ? "flex" : "none";
    };
    window.addEventListener("scroll", actualizarScrollToTop, { passive: true });
    scrollToTop?.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
    actualizarScrollToTop();

    const form = document.getElementById("form-sucursal");
    const modalTrigger = document.getElementById("abrir-modal-sucursal");
    const modalClose = document.querySelector('#modal-sucursal [data-hs-overlay="#modal-sucursal"]');
    const formError = document.getElementById("sucursal-form-error");
    const saveButton = document.getElementById("btnGuardarSucursal");
    const reloadButton = document.getElementById("btnRecargarSucursales");
    const reloadIcon = document.getElementById("iconRecargarSucursales");
    const reloadLabel = document.getElementById("textoRecargarSucursales");
    const tableElement = document.getElementById("tabla-sucursales");
    if (!form || !tableElement) return;

    const escapeAttribute = (value) => String(value ?? "").replace(/[&<>"']/g, (character) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[character]));
    const tooltipAttributes = (text) => window.XintraTooltip
      ? window.XintraTooltip.attr(text)
      : `aria-label="${escapeAttribute(text)}"`;

    const table = new Tabulator(tableElement, {
      layout: "fitColumns",
      pagination: "local",
      paginationSize: 10,
      paginationSizeSelector: [5, 10, 15, 20, 25],
      paginationCounter: "rows",
      movableColumns: true,
      ajaxURL: "controller/table_sucursal.php",
      columns: [
        { title: "ID", field: "id", sorter: "number", width: 85 },
        { title: "Sucursal", field: "nombre", headerFilter: "input", minWidth: 150, widthGrow: 2 },
        { title: "Distrito", field: "distrito", headerFilter: "input", minWidth: 120 },
        { title: "Departamento", field: "departamento", headerFilter: "input", minWidth: 130 },
        { title: "Dirección", field: "direccion", headerFilter: "input", minWidth: 180, widthGrow: 2 },
        { title: "Teléfono", field: "telefono", width: 125 },
        {
          title: "Estado",
          field: "estado",
          width: 110,
          formatter: (cell) => Number(cell.getValue()) === 1
            ? '<span class="badge bg-success">Activa</span>'
            : '<span class="badge bg-danger">Inactiva</span>',
          accessorDownload: (value) => Number(value) === 1 ? "Activa" : "Inactiva",
        },
        {
          title: "Opciones",
          field: "acciones",
          width: 145,
          hozAlign: "center",
          headerSort: false,
          formatter: (cell) => {
            const row = cell.getRow().getData();
            const state = Number(row.estado) === 1;
            return `<div class="flex items-center justify-center gap-2">
              <button type="button" class="btn-editar-sucursal ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full" data-id="${Number(row.id)}" ${tooltipAttributes("Editar sucursal")} aria-label="Editar sucursal">
                <i class="ri-edit-2-line" aria-hidden="true"></i>
              </button>
              <button type="button" class="btn-estado-sucursal ti-btn ti-btn-icon ${state ? 'bg-danger/10 text-danger hover:bg-danger hover:text-white' : 'bg-success/10 text-success hover:bg-success hover:text-white'} !rounded-full" data-id="${Number(row.id)}" data-state="${state ? 0 : 1}" ${tooltipAttributes(state ? "Dar de baja sucursal" : "Reactivar sucursal")} aria-label="${state ? "Dar de baja sucursal" : "Reactivar sucursal"}">
                <i class="${state ? 'ri-pause-circle-line' : 'ri-play-circle-line'}" aria-hidden="true"></i>
              </button>
            </div>`;
          },
          cellClick: (event, cell) => {
            const row = cell.getRow().getData();
            const editButton = event.target.closest(".btn-editar-sucursal");
            const stateButton = event.target.closest(".btn-estado-sucursal");

            if (editButton) {
              form.reset();
              formError.textContent = "";
              formError.classList.add("hidden");
              document.getElementById("sucursal-id").value = row.id;
              document.getElementById("sucursal-nombre").value = row.nombre || "";
              document.getElementById("sucursal-distrito").value = row.distrito || "";
              document.getElementById("sucursal-departamento").value = row.departamento || "";
              document.getElementById("sucursal-direccion").value = row.direccion || "";
              document.getElementById("sucursal-telefono").value = row.telefono || "";
              document.getElementById("sucursal-estado").value = String(row.estado);
              document.getElementById("modal-sucursal-titulo").textContent = "Editar sucursal";
              modalTrigger.click();
              return;
            }

            if (stateButton) {
              const nextState = Number(stateButton.dataset.state);
              const action = nextState === 1 ? "reactivar" : "dar de baja";
              alertify.confirm(
                "Cambiar estado de sucursal",
                `¿Confirmas ${action} «${row.nombre}»?`,
                async () => {
                  stateButton.disabled = true;
                  try {
                    const response = await fetch("controller/estado_sucursal.php", {
                      method: "POST",
                      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                      body: new URLSearchParams({ id: String(row.id), estado: String(nextState) }),
                    });
                    const result = await response.json();
                    if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo cambiar el estado.");
                    alertify.success(result.message);
                    await recargarListado();
                  } catch (error) {
                    alertify.error(error.message);
                    stateButton.disabled = false;
                  }
                },
                () => alertify.message("Acción cancelada.")
              ).set("labels", { ok: "Confirmar", cancel: "Cancelar" });
            }
          },
        },
      ],
    });

    const initTooltips = () => window.XintraTooltip?.init(tableElement);
    table.on("tableBuilt", initTooltips);
    table.on("renderComplete", initTooltips);

    const recargarListado = async (mostrarExito = false) => {
      if (reloadButton.disabled) return;
      reloadButton.disabled = true;
      reloadButton.setAttribute("aria-busy", "true");
      reloadIcon.classList.remove("ri-refresh-line");
      reloadIcon.classList.add("ri-loader-4-line", "animate-spin");
      reloadLabel.textContent = "Cargando...";
      try {
        await table.replaceData();
        if (mostrarExito) alertify.success("Listado de sucursales actualizado.");
      } catch (error) {
        alertify.error("No se pudo actualizar el listado de sucursales.");
      } finally {
        reloadButton.disabled = false;
        reloadButton.removeAttribute("aria-busy");
        reloadIcon.classList.remove("ri-loader-4-line", "animate-spin");
        reloadIcon.classList.add("ri-refresh-line");
        reloadLabel.textContent = "Recargar listado";
      }
    };

    document.getElementById("btnNuevaSucursal")?.addEventListener("click", () => {
      form.reset();
      formError.textContent = "";
      formError.classList.add("hidden");
      document.getElementById("sucursal-id").value = "";
      document.getElementById("sucursal-estado").value = "1";
      document.getElementById("modal-sucursal-titulo").textContent = "Nueva sucursal";
      modalTrigger.click();
      document.getElementById("sucursal-nombre").focus();
    });

    reloadButton?.addEventListener("click", () => recargarListado(true));

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      formError.textContent = "";
      formError.classList.add("hidden");
      if (!form.reportValidity()) return;

      const id = document.getElementById("sucursal-id").value.trim();
      const endpoint = id ? "controller/upd_sucursal.php" : "controller/add_sucursal.php";
      saveButton.disabled = true;
      saveButton.classList.add("opacity-50", "cursor-not-allowed");
      try {
        const response = await fetch(endpoint, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
          body: new URLSearchParams(new FormData(form)),
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo guardar la sucursal.");
        alertify.success(result.message);
        modalClose?.click();
        await recargarListado();
      } catch (error) {
        formError.textContent = error.message || "No se pudo guardar la sucursal.";
        formError.classList.remove("hidden");
      } finally {
        saveButton.disabled = false;
        saveButton.classList.remove("opacity-50", "cursor-not-allowed");
      }
    });
  });
})();
