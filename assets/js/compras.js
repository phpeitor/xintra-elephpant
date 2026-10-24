(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-compra");
    const tableElement = document.getElementById("tabla-compras");
    const linesBody = document.getElementById("compra-lineas");
    const supplierSelect = document.getElementById("compra-proveedor");
    const formError = document.getElementById("compra-form-error");
    const saveButton = document.getElementById("btnGuardarCompra");
    const openFormButton = document.getElementById("abrir-modal-compra");
    const closeFormButton = document.querySelector('#modal-compra [data-hs-overlay="#modal-compra"]');
    const openDetailButton = document.getElementById("abrir-modal-detalle-compra");
    const reloadButton = document.getElementById("btnRecargarCompras");
    const dateInput = document.getElementById("date");
    if (!form || !tableElement) return;

    const datePicker = flatpickr(dateInput, {
      dateFormat: "Y-m-d",
      defaultDate: new Date(),
      maxDate: "today",
      disableMobile: true,
    });

    const money = new Intl.NumberFormat("es-PE", { style: "currency", currency: "PEN" });
    const escapeHtml = (value) => String(value ?? "").replace(/[&<>"']/g, (character) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
    }[character]));
    const setCell = (row, value, className = "") => {
      const cell = document.createElement("td");
      if (className) cell.className = className;
      cell.textContent = value;
      row.appendChild(cell);
      return cell;
    };

    let suppliers = [];
    let products = [];
    let catalogPromise;
    const lineChoices = new WeakMap();

    const getLineTotals = (line) => {
      const quantity = Number(line.querySelector(".compra-cantidad").value);
      const cost = Number(line.querySelector(".compra-costo").value);
      const rate = Number(line.querySelector(".compra-porcentaje-igv").value);
      const subtotal = Number.isFinite(quantity * cost) ? Math.round((quantity * cost + Number.EPSILON) * 100) / 100 : 0;
      const igv = Math.round((subtotal * rate / 100 + Number.EPSILON) * 100) / 100;
      return { quantity, cost, rate, subtotal, igv, total: subtotal + igv };
    };

    const updateTotals = () => {
      let subtotal = 0;
      let igv = 0;
      linesBody.querySelectorAll("tr").forEach((line) => {
        const totals = getLineTotals(line);
        line.querySelector(".compra-line-subtotal").textContent = money.format(totals.subtotal);
        line.querySelector(".compra-line-total").textContent = money.format(totals.total);
        subtotal += totals.subtotal;
        igv += totals.igv;
      });
      subtotal = Math.round((subtotal + Number.EPSILON) * 100) / 100;
      igv = Math.round((igv + Number.EPSILON) * 100) / 100;
      document.getElementById("compra-subtotal").textContent = money.format(subtotal);
      document.getElementById("compra-igv").textContent = money.format(igv);
      document.getElementById("compra-total").textContent = money.format(subtotal + igv);
    };

    const addLine = (initial = {}) => {
      const row = document.createElement("tr");
      const productCell = document.createElement("td");
      const productSelect = document.createElement("select");
      productSelect.className = "compra-producto compra-line-product ti-form-select rounded-sm";
      productSelect.required = false;
      productSelect.add(new Option("Selecciona producto", ""));
      products.forEach((product) => productSelect.add(new Option(product.nombre, String(product.id))));
      productSelect.value = initial.producto_id ? String(initial.producto_id) : "";
      productCell.appendChild(productSelect);
      row.appendChild(productCell);
      const productChoices = new Choices(productSelect, {
        searchPlaceholderValue: "Buscar producto...",
        shouldSort: false,
        allowHTML: false,
        itemSelectText: "",
        searchResultLimit: 100,
        noResultsText: "No se encontraron productos",
        noChoicesText: "No hay productos disponibles",
      });
      lineChoices.set(productSelect, productChoices);

      const clearProduct = () => {
        productChoices.removeActiveItems();
        productChoices.setChoiceByValue("");
      };
      productSelect.addEventListener("change", () => {
        const selectedProduct = productSelect.value;
        const alreadyAdded = selectedProduct && Array.from(linesBody.querySelectorAll(".compra-producto"))
          .some((otherSelect) => otherSelect !== productSelect && otherSelect.value === selectedProduct);
        if (alreadyAdded) {
          clearProduct();
          alertify.error("Ese producto ya está agregado a la compra.");
        }
        updateTotals();
      });

      const quantityCell = document.createElement("td");
      const quantityInput = document.createElement("input");
      quantityInput.type = "number";
      quantityInput.min = "1";
      quantityInput.max = "999999";
      quantityInput.step = "1";
      quantityInput.value = initial.cantidad ?? "1";
      quantityInput.required = true;
      quantityInput.className = "compra-cantidad compra-line-number ti-form-input rounded-sm";
      quantityInput.setAttribute("aria-label", "Cantidad");
      quantityCell.appendChild(quantityInput);
      row.appendChild(quantityCell);

      const costCell = document.createElement("td");
      const costInput = document.createElement("input");
      costInput.type = "number";
      costInput.min = "0";
      costInput.max = "99999999";
      costInput.step = "0.0001";
      costInput.value = initial.costo_unitario ?? "";
      costInput.required = true;
      costInput.className = "compra-costo compra-line-number ti-form-input rounded-sm";
      costInput.setAttribute("aria-label", "Costo unitario antes del IGV");
      costCell.appendChild(costInput);
      row.appendChild(costCell);

      const taxCell = document.createElement("td");
      const taxSelect = document.createElement("select");
      taxSelect.className = "compra-porcentaje-igv ti-form-select rounded-sm";
      taxSelect.setAttribute("aria-label", "Porcentaje de IGV");
      taxSelect.add(new Option("18%", "18"));
      taxSelect.add(new Option("0%", "0"));
      taxSelect.value = String(initial.porcentaje_igv ?? "18");
      taxCell.appendChild(taxSelect);
      row.appendChild(taxCell);

      const subtotalCell = document.createElement("td");
      subtotalCell.className = "compra-line-subtotal";
      subtotalCell.textContent = money.format(0);
      row.appendChild(subtotalCell);
      const totalCell = document.createElement("td");
      totalCell.className = "compra-line-total font-semibold";
      totalCell.textContent = money.format(0);
      row.appendChild(totalCell);

      const removeCell = document.createElement("td");
      const removeButton = document.createElement("button");
      removeButton.type = "button";
      removeButton.className = "ti-btn ti-btn-icon ti-btn-light !rounded-full";
      removeButton.setAttribute("aria-label", "Quitar producto");
      removeButton.title = "Quitar producto";
      removeButton.innerHTML = '<i class="ri-delete-bin-line" aria-hidden="true"></i>';
      removeButton.addEventListener("click", () => {
        if (linesBody.querySelectorAll("tr").length === 1) {
          clearProduct();
          quantityInput.value = "1";
          costInput.value = "";
          taxSelect.value = "18";
          updateTotals();
          return;
        }
        productChoices.destroy();
        lineChoices.delete(productSelect);
        row.remove();
        updateTotals();
      });
      removeCell.appendChild(removeButton);
      row.appendChild(removeCell);
      row.addEventListener("input", updateTotals);
      row.addEventListener("change", updateTotals);
      linesBody.appendChild(row);
      updateTotals();
    };

    const loadCatalogs = async () => {
      const response = await fetch("controller/catalogos_compra.php");
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || "No se pudieron cargar los catálogos.");
      suppliers = result.data.proveedores || [];
      products = result.data.productos || [];
      supplierSelect.replaceChildren(new Option("Selecciona proveedor", ""));
      suppliers.forEach((supplier) => supplierSelect.add(new Option(
        `${supplier.razon_social} · ${supplier.tipo_documento} ${supplier.documento}`,
        String(supplier.id)
      )));
    };

    const table = new Tabulator(tableElement, {
      layout: "fitColumns",
      pagination: "local",
      paginationSize: 10,
      paginationSizeSelector: [5, 10, 15, 20, 25],
      paginationCounter: "rows",
      movableColumns: true,
      ajaxURL: "controller/table_compra.php",
      ajaxResponse: (_url, _params, response) => Array.isArray(response) ? response : [],
      columns: [
        { title: "N.º", field: "id", sorter: "number", width: 80 },
        { title: "Fecha", field: "fecha", sorter: "date", width: 120 },
        { title: "Proveedor", field: "proveedor", headerFilter: "input", minWidth: 180, widthGrow: 2,
          formatter: (cell) => escapeHtml(cell.getValue()) },
        { title: "Comprobante", field: "numero", minWidth: 155,
          formatter: (cell) => {
            const row = cell.getRow().getData();
            return `${escapeHtml(row.tipo_comprobante)} ${escapeHtml(row.serie)}-${escapeHtml(row.numero)}`;
          } },
        { title: "Total", field: "total", sorter: "number", hozAlign: "right", width: 135,
          formatter: (cell) => money.format(Number(cell.getValue()) || 0) },
        { title: "Estado", field: "estado", width: 150,
          formatter: (cell) => cell.getValue() === "ANULADA"
            ? '<span class="badge bg-danger">Anulada</span>'
            : '<span class="badge bg-warning text-dark">Pendiente de ingreso</span>' },
        { title: "Detalle", field: "acciones", width: 105, hozAlign: "center", headerSort: false,
          formatter: (cell) => `<button type="button" class="btn-ver-compra ti-btn ti-btn-icon ti-btn-outline-primary !rounded-full" data-id="${Number(cell.getRow().getData().id)}" aria-label="Ver detalle" title="Ver detalle"><i class="ri-eye-line" aria-hidden="true"></i></button>`,
          cellClick: async (event, cell) => {
            if (!event.target.closest(".btn-ver-compra")) return;
            try {
              await abrirDetalle(cell.getRow().getData().id);
            } catch (error) {
              alertify.error(error.message || "No se pudo cargar el detalle de compra.");
            }
          } },
      ],
    });

    table.on("dataLoadError", () => {
      document.getElementById("compras-error").textContent = "No se pudo cargar el historial. Verifica que la migración de compras esté aplicada.";
      document.getElementById("compras-error").classList.remove("hidden");
    });

    const newPurchase = () => {
      form.reset();
      formError.textContent = "";
      formError.classList.add("hidden");
      linesBody.querySelectorAll(".compra-producto").forEach((select) => {
        lineChoices.get(select)?.destroy();
        lineChoices.delete(select);
      });
      linesBody.replaceChildren();
      if (!suppliers.length || !products.length) {
        formError.textContent = !suppliers.length
          ? "Registra y activa al menos un proveedor antes de crear una compra."
          : "Registra y activa al menos un producto de inventario antes de crear una compra.";
        formError.classList.remove("hidden");
      }
      datePicker.setDate(new Date(), true);
      addLine();
      document.getElementById("modal-compra-titulo").textContent = "Registrar compra";
      openFormButton.click();
    };

    const abrirDetalle = async (id) => {
      const response = await fetch(`controller/get_compra.php?id=${encodeURIComponent(id)}`);
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo cargar la compra.");
      const purchase = result.data;
      document.getElementById("detalle-compra-resumen").textContent = `${purchase.tipo_comprobante} ${purchase.serie}-${purchase.numero} · ${purchase.fecha} · ${purchase.estado === "ANULADA" ? "Anulada" : "Pendiente de ingreso a inventario"}`;
      document.getElementById("detalle-compra-proveedor").textContent = `${purchase.proveedor} · ${purchase.tipo_documento} ${purchase.documento}`;
      document.getElementById("detalle-compra-observaciones").textContent = purchase.observaciones || "Sin observaciones.";
      const tbody = document.getElementById("detalle-compra-lineas");
      tbody.replaceChildren();
      (purchase.items || []).forEach((item) => {
        const row = document.createElement("tr");
        setCell(row, item.descripcion);
        setCell(row, String(item.cantidad));
        setCell(row, money.format(Number(item.costo_unitario) || 0));
        setCell(row, money.format(Number(item.subtotal) || 0));
        setCell(row, money.format(Number(item.igv) || 0));
        setCell(row, money.format(Number(item.total) || 0), "font-semibold");
        tbody.appendChild(row);
      });
      document.getElementById("detalle-compra-subtotal").textContent = money.format(Number(purchase.subtotal) || 0);
      document.getElementById("detalle-compra-igv").textContent = money.format(Number(purchase.igv) || 0);
      document.getElementById("detalle-compra-total").textContent = money.format(Number(purchase.total) || 0);
      openDetailButton.click();
    };

    document.getElementById("btnNuevaCompra").addEventListener("click", async () => {
      try {
        await catalogPromise;
        newPurchase();
      } catch (error) {
        alertify.error(error.message || "No se pudieron cargar proveedores y productos.");
      }
    });
    document.getElementById("btnAgregarLineaCompra").addEventListener("click", () => addLine());
    reloadButton.addEventListener("click", async () => {
      reloadButton.disabled = true;
      document.getElementById("iconRecargarCompras").classList.add("animate-spin");
      document.getElementById("textoRecargarCompras").textContent = "Cargando…";
      try {
        await table.replaceData();
        document.getElementById("compras-error").classList.add("hidden");
      } catch (_error) {
        alertify.error("No se pudo actualizar el historial de compras.");
      } finally {
        reloadButton.disabled = false;
        document.getElementById("iconRecargarCompras").classList.remove("animate-spin");
        document.getElementById("textoRecargarCompras").textContent = "Actualizar";
      }
    });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      formError.textContent = "";
      formError.classList.add("hidden");
      if (!form.reportValidity()) return;
      const items = Array.from(linesBody.querySelectorAll("tr")).map((row) => ({
        producto_id: row.querySelector(".compra-producto").value,
        cantidad: row.querySelector(".compra-cantidad").value,
        costo_unitario: row.querySelector(".compra-costo").value,
        porcentaje_igv: row.querySelector(".compra-porcentaje-igv").value,
      }));
      if (!items.length || items.some((item) => !item.producto_id || !item.cantidad || item.costo_unitario === "")) {
        formError.textContent = "Completa el producto, la cantidad y el costo unitario de cada línea.";
        formError.classList.remove("hidden");
        return;
      }
      const payload = new URLSearchParams(new FormData(form));
      payload.set("items", JSON.stringify(items));
      saveButton.disabled = true;
      saveButton.classList.add("opacity-50", "cursor-not-allowed");
      try {
        const response = await fetch("controller/add_compra.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
          body: payload,
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo registrar la compra.");
        alertify.success(result.message);
        closeFormButton?.click();
        await table.replaceData();
      } catch (error) {
        formError.textContent = error.message || "No se pudo registrar la compra.";
        formError.classList.remove("hidden");
      } finally {
        saveButton.disabled = false;
        saveButton.classList.remove("opacity-50", "cursor-not-allowed");
      }
    });

    catalogPromise = loadCatalogs();
    catalogPromise.catch((error) => {
      document.getElementById("compras-error").textContent = error.message || "No se pudieron cargar los catálogos.";
      document.getElementById("compras-error").classList.remove("hidden");
    });
  });
})();
