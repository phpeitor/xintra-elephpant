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
    const cuotaForm = document.getElementById("form-cuota-sucursal");
    const cuotaModalTrigger = document.getElementById("abrir-modal-cuota");
    const cuotaIncrementInput = document.getElementById("cuota-incremento");
    const cuotaNuevaInput = document.getElementById("cuota-nueva");
    const cuotaError = document.getElementById("cuota-form-error");
    const cuotaSaveButton = document.getElementById("btnAumentarCuota");
    const nubefactForm = document.getElementById("form-nubefact-sucursal");
    const nubefactModalTrigger = document.getElementById("abrir-modal-nubefact");
    const nubefactModalClose = document.querySelector('#modal-nubefact-sucursal [data-hs-overlay="#modal-nubefact-sucursal"]');
    const nubefactError = document.getElementById("nubefact-form-error");
    const nubefactSaveButton = document.getElementById("btnGuardarNubefact");
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
        { title: "Tickets", field: "tickets_usados", sorter: "number", width: 105 },
        { title: "Cuota", field: "cuota", sorter: "number", width: 110, formatter: (cell) => Number(cell.getValue()) > 0 ? Number(cell.getValue()).toLocaleString("es-PE") : '<span class="text-textmuted">Sin cuota</span>' },
        {
          title: "NubeFact",
          field: "nubefact_configurada",
          width: 130,
          formatter: (cell) => {
            const row = cell.getRow().getData();
            if (Number(cell.getValue()) !== 1) return '<span class="badge bg-outline-secondary">Sin configurar</span>';
            return row.nubefact_entorno === "production"
              ? '<span class="badge bg-success">Producción</span>'
              : '<span class="badge bg-warning text-dark">Demo</span>';
          },
        },
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
          width: 235,
          hozAlign: "center",
          headerSort: false,
          formatter: (cell) => {
            const row = cell.getRow().getData();
            const state = Number(row.estado) === 1;
            return `<div class="flex items-center justify-center gap-2">
              <button type="button" class="btn-editar-sucursal ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full" data-id="${Number(row.id)}" ${tooltipAttributes("Editar sucursal")} aria-label="Editar sucursal">
                <i class="ri-edit-2-line" aria-hidden="true"></i>
              </button>
              <button type="button" class="btn-config-nubefact ti-btn ti-btn-icon bg-info/10 text-info hover:bg-info hover:text-white !rounded-full" data-id="${Number(row.id)}" ${tooltipAttributes("Configurar facturación electrónica")} aria-label="Configurar NubeFact para sucursal">
                <i class="ri-settings-3-line" aria-hidden="true"></i>
              </button>
              <button type="button" class="btn-cuota-sucursal ti-btn ti-btn-icon bg-primary/10 text-primary hover:bg-primary hover:text-white !rounded-full" data-id="${Number(row.id)}" ${tooltipAttributes("Aumentar cuota y consultar historial")} aria-label="Aumentar cuota de tickets">
                <i class="ri-add-circle-line" aria-hidden="true"></i>
              </button>
              <button type="button" class="btn-estado-sucursal ti-btn ti-btn-icon ${state ? 'bg-danger/10 text-danger hover:bg-danger hover:text-white' : 'bg-success/10 text-success hover:bg-success hover:text-white'} !rounded-full" data-id="${Number(row.id)}" data-state="${state ? 0 : 1}" ${tooltipAttributes(state ? "Dar de baja sucursal" : "Reactivar sucursal")} aria-label="${state ? "Dar de baja sucursal" : "Reactivar sucursal"}">
                <i class="${state ? 'ri-pause-circle-line' : 'ri-play-circle-line'}" aria-hidden="true"></i>
              </button>
            </div>`;
          },
          cellClick: (event, cell) => {
            const row = cell.getRow().getData();
            const editButton = event.target.closest(".btn-editar-sucursal");
            const nubefactButton = event.target.closest(".btn-config-nubefact");
            const quotaButton = event.target.closest(".btn-cuota-sucursal");
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

            if (nubefactButton) {
              window.sucursalNubefact?.abrir(row);
              return;
            }

            if (quotaButton) {
              window.sucursalCuota?.abrir(row);
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

    const historialList = document.getElementById("cuota-historial-lista");
    const historialStatus = document.getElementById("cuota-historial-estado");
    const cuotaActualLabel = document.getElementById("cuota-actual");
    const ticketsUsadosLabel = document.getElementById("cuota-tickets-usados");
    const quotaNumberFormat = new Intl.NumberFormat("es-PE");
    let cuotaActual = 0;

    const actualizarProyeccionCuota = () => {
      const incremento = Math.max(0, Number.parseInt(cuotaIncrementInput.value, 10) || 0);
      cuotaNuevaInput.value = quotaNumberFormat.format(cuotaActual + incremento);
    };

    const renderHistorialCuota = (registros) => {
      historialList.replaceChildren();
      if (!registros.length) {
        const empty = document.createElement("li");
        empty.className = "rounded-md bg-light p-3 text-sm text-textmuted";
        empty.textContent = "Aún no hay aumentos registrados para esta sucursal.";
        historialList.appendChild(empty);
        return;
      }

      registros.forEach((entry) => {
        const item = document.createElement("li");
        item.className = "rounded-md border border-defaultborder p-3 text-sm";
        const summary = document.createElement("p");
        summary.className = "font-medium";
        summary.textContent = `Cuota ${quotaNumberFormat.format(Number(entry.cuota_anterior))} → ${quotaNumberFormat.format(Number(entry.cuota_nueva))} (+${quotaNumberFormat.format(Number(entry.incremento))})`;
        const metadata = document.createElement("p");
        metadata.className = "mt-1 text-xs text-textmuted";
        metadata.textContent = `${entry.fecha || "Fecha desconocida"} · ${entry.usuario || "Usuario desconocido"}${entry.motivo ? ` · ${entry.motivo}` : ""}`;
        item.append(summary, metadata);
        historialList.appendChild(item);
      });
    };

    const cargarHistorialCuota = async (idSucursal) => {
      historialStatus.textContent = "Cargando historial…";
      try {
        const response = await fetch(`controller/historial_cuota_sucursal.php?id=${encodeURIComponent(idSucursal)}`);
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo cargar el historial.");
        renderHistorialCuota(result.data || []);
        historialStatus.textContent = "";
      } catch (error) {
        historialList.replaceChildren();
        historialStatus.textContent = "Error al cargar";
      }
    };

    window.sucursalCuota = {
      abrir(row) {
        cuotaForm.reset();
        cuotaError.textContent = "";
        cuotaError.classList.add("hidden");
        cuotaForm.dataset.idSucursal = String(row.id);
        document.getElementById("cuota-sucursal-id").value = row.id;
        document.getElementById("cuota-sucursal-nombre").textContent = row.nombre || "";
        cuotaActual = Number(row.cuota) || 0;
        cuotaActualLabel.textContent = quotaNumberFormat.format(cuotaActual);
        ticketsUsadosLabel.textContent = quotaNumberFormat.format(Number(row.tickets_usados) || 0);
        actualizarProyeccionCuota();
        cargarHistorialCuota(row.id);
        cuotaModalTrigger.click();
      },
    };

    cuotaIncrementInput.addEventListener("input", actualizarProyeccionCuota);
    cuotaForm.addEventListener("submit", (event) => {
      event.preventDefault();
      cuotaError.textContent = "";
      cuotaError.classList.add("hidden");
      if (!cuotaForm.reportValidity()) return;

      const id = cuotaForm.dataset.idSucursal;
      const incremento = Number.parseInt(cuotaIncrementInput.value, 10);
      const nuevaCuota = cuotaActual + incremento;
      alertify.confirm(
        "Confirmar aumento de cuota",
        `La cuota de la sucursal ${id} cambiará de ${quotaNumberFormat.format(cuotaActual)} a ${quotaNumberFormat.format(nuevaCuota)} tickets. Se guardará el movimiento en el historial. ¿Continuar?`,
        async () => {
          cuotaSaveButton.disabled = true;
          cuotaSaveButton.classList.add("opacity-50", "cursor-not-allowed");
          try {
            const response = await fetch("controller/aumentar_cuota_sucursal.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
              body: new URLSearchParams(new FormData(cuotaForm)),
            });
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo aumentar la cuota.");
            cuotaActual = Number(result.data.cuota_nueva) || cuotaActual;
            cuotaActualLabel.textContent = quotaNumberFormat.format(cuotaActual);
            cuotaIncrementInput.value = "";
            actualizarProyeccionCuota();
            localStorage.setItem("xintra-sucursal-cuota-updated", String(Date.now()));
            alertify.success(result.message);
            await Promise.all([recargarListado(), cargarHistorialCuota(id)]);
          } catch (error) {
            cuotaError.textContent = error.message || "No se pudo guardar el aumento.";
            cuotaError.classList.remove("hidden");
          } finally {
            cuotaSaveButton.disabled = false;
            cuotaSaveButton.classList.remove("opacity-50", "cursor-not-allowed");
          }
        },
        () => alertify.message("Aumento cancelado.")
      ).set("labels", { ok: "Aumentar", cancel: "Cancelar" });
    });

    const rellenarEntornoNubefact = (environment, config) => {
      const capitalized = environment.charAt(0).toUpperCase() + environment.slice(1);
      document.getElementById(`nubefact-${environment}-url`).value = config.url || "";
      document.getElementById(`nubefact-${environment}-serie-boleta`).value = config.serie_boleta || "";
      document.getElementById(`nubefact-${environment}-serie-factura`).value = config.serie_factura || "";
      document.getElementById(`nubefact-${environment}-num-boleta`).value = config.numero_inicial_boleta || 1;
      document.getElementById(`nubefact-${environment}-num-factura`).value = config.numero_inicial_factura || 1;
      document.getElementById(`nubefact-${environment}-token`).value = "";
      document.getElementById(`nubefact-${environment}-token-status`).textContent = config.token_configurado
        ? `Token ${capitalized} guardado; déjalo vacío para conservarlo.`
        : `Token ${capitalized} aún no configurado.`;
    };

    window.sucursalNubefact = {
      async abrir(row) {
        nubefactForm.reset();
        nubefactError.textContent = "";
        nubefactError.classList.add("hidden");
        document.getElementById("nubefact-id-sucursal").value = row.id;
        document.getElementById("nubefact-sucursal-nombre").textContent = row.nombre || "";
        document.getElementById("nubefact-entorno-activo").value = "demo";
        document.getElementById("nubefact-demo-num-boleta").value = "1";
        document.getElementById("nubefact-demo-num-factura").value = "1";
        document.getElementById("nubefact-production-num-boleta").value = "1";
        document.getElementById("nubefact-production-num-factura").value = "1";
        nubefactModalTrigger.click();

        try {
          const response = await fetch(`controller/configurar_nubefact_sucursal.php?id=${encodeURIComponent(row.id)}`);
          const result = await response.json();
          if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo cargar la configuración.");
          document.getElementById("nubefact-entorno-activo").value = result.data.entorno_activo || "demo";
          rellenarEntornoNubefact("demo", result.data.demo || {});
          rellenarEntornoNubefact("production", result.data.production || {});
        } catch (error) {
          nubefactError.textContent = error.message || "No se pudo cargar la configuración NubeFact.";
          nubefactError.classList.remove("hidden");
        }
      },
    };

    nubefactForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      nubefactError.textContent = "";
      nubefactError.classList.add("hidden");
      nubefactSaveButton.disabled = true;
      nubefactSaveButton.classList.add("opacity-50", "cursor-not-allowed");
      try {
        const response = await fetch("controller/configurar_nubefact_sucursal.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
          body: new URLSearchParams(new FormData(nubefactForm)),
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo guardar la configuración.");
        alertify.success(result.message);
        nubefactModalClose?.click();
        await recargarListado();
      } catch (error) {
        nubefactError.textContent = error.message || "No se pudo guardar la configuración NubeFact.";
        nubefactError.classList.remove("hidden");
      } finally {
        nubefactSaveButton.disabled = false;
        nubefactSaveButton.classList.remove("opacity-50", "cursor-not-allowed");
      }
    });

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
