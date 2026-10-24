(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-compra");
    const linesBody = document.getElementById("compra-lineas");
    const supplierSelect = document.getElementById("compra-proveedor");
    const productPicker = document.getElementById("compra-product-picker");
    const emptyState = document.getElementById("compra-lineas-vacias");
    const formError = document.getElementById("compra-form-error");
    const saveButton = document.getElementById("btnGuardarCompra");
    const dateInput = document.getElementById("date");
    const money = new Intl.NumberFormat("es-PE", { style: "currency", currency: "PEN" });
    let products = [];
    let productChoices;

    flatpickr(dateInput, {
      dateFormat: "Y-m-d",
      defaultDate: new Date(),
      maxDate: "today",
      disableMobile: true,
      onReady: (_dates, _date, instance) => instance.calendarContainer.classList.add("compras-datepicker"),
    });

    const refreshProductPicker = () => {
      if (!productChoices) return;
      const usedIds = new Set(Array.from(linesBody.querySelectorAll(".compra-producto-id"), (input) => input.value));
      productChoices.setChoices([
        { value: "", label: "Buscar producto para agregar...", selected: true },
        ...products.map((product) => ({
          value: String(product.id),
          label: product.nombre,
          disabled: usedIds.has(String(product.id)),
        })),
      ], "value", "label", true);
      productChoices.setChoiceByValue("");
    };

    const recalcularResumen = () => {
      let subtotalTotal = 0;
      let igvTotal = 0;
      const rows = Array.from(linesBody.querySelectorAll("tr"));
      rows.forEach((row) => {
        const cantidad = Number(row.querySelector(".compra-cantidad").value);
        const costo = Number(row.querySelector(".compra-costo").value);
        const rate = Number(row.querySelector(".compra-porcentaje-igv").value);
        const subtotal = Math.round((cantidad * costo + Number.EPSILON) * 100) / 100 || 0;
        const igv = Math.round((subtotal * rate / 100 + Number.EPSILON) * 100) / 100;
        row.querySelector(".compra-line-subtotal").textContent = money.format(subtotal);
        row.querySelector(".compra-line-total").textContent = money.format(subtotal + igv);
        subtotalTotal += subtotal;
        igvTotal += igv;
      });
      subtotalTotal = Math.round((subtotalTotal + Number.EPSILON) * 100) / 100;
      igvTotal = Math.round((igvTotal + Number.EPSILON) * 100) / 100;
      document.getElementById("compra-cantidad-productos").textContent = String(rows.length);
      document.getElementById("compra-subtotal").textContent = money.format(subtotalTotal);
      document.getElementById("compra-igv").textContent = money.format(igvTotal);
      document.getElementById("compra-total").textContent = money.format(subtotalTotal + igvTotal);
      emptyState.classList.toggle("hidden", rows.length > 0);
    };

    const addLine = (product) => {
      const row = document.createElement("tr");
      const productCell = document.createElement("td");
      const productId = document.createElement("input");
      productId.type = "hidden";
      productId.className = "compra-producto-id";
      productId.value = String(product.id);
      const productName = document.createElement("span");
      productName.className = "font-medium";
      productName.textContent = product.nombre;
      productCell.append(productId, productName);
      row.appendChild(productCell);

      const quantityCell = document.createElement("td");
      const quantity = document.createElement("input");
      quantity.type = "number";
      quantity.min = "1";
      quantity.max = "999999";
      quantity.step = "1";
      quantity.value = "1";
      quantity.required = true;
      quantity.className = "compra-cantidad compra-line-number ti-form-input rounded-sm";
      quantity.setAttribute("aria-label", "Cantidad entera");
      quantityCell.appendChild(quantity);
      row.appendChild(quantityCell);

      const costCell = document.createElement("td");
      const cost = document.createElement("input");
      cost.type = "number";
      cost.min = "0";
      cost.max = "99999999";
      cost.step = "0.0001";
      cost.required = true;
      cost.className = "compra-costo compra-line-number ti-form-input rounded-sm";
      cost.setAttribute("aria-label", "Costo unitario antes del IGV");
      costCell.appendChild(cost);
      row.appendChild(costCell);

      const taxCell = document.createElement("td");
      const tax = document.createElement("select");
      tax.className = "compra-porcentaje-igv ti-form-select rounded-sm";
      tax.setAttribute("aria-label", "Porcentaje de IGV");
      tax.add(new Option("18%", "18"));
      tax.add(new Option("0%", "0"));
      taxCell.appendChild(tax);
      row.appendChild(taxCell);

      const subtotal = document.createElement("td");
      subtotal.className = "compra-line-subtotal";
      subtotal.textContent = money.format(0);
      row.appendChild(subtotal);
      const total = document.createElement("td");
      total.className = "compra-line-total font-semibold";
      total.textContent = money.format(0);
      row.appendChild(total);

      const removeCell = document.createElement("td");
      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "ti-btn ti-btn-icon ti-btn-light !rounded-full";
      remove.title = "Quitar producto";
      remove.setAttribute("aria-label", "Quitar producto");
      remove.innerHTML = '<i class="ri-delete-bin-line" aria-hidden="true"></i>';
      remove.addEventListener("click", () => {
        row.remove();
        refreshProductPicker();
        recalcularResumen();
      });
      removeCell.appendChild(remove);
      row.appendChild(removeCell);
      row.addEventListener("input", recalcularResumen);
      row.addEventListener("change", recalcularResumen);
      linesBody.appendChild(row);
      refreshProductPicker();
      recalcularResumen();
    };

    const loadCatalogs = async () => {
      const response = await fetch("controller/catalogos_compra.php");
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || "No se pudieron cargar proveedores y productos.");
      products = result.data.productos || [];
      supplierSelect.replaceChildren(new Option("Selecciona proveedor", ""));
      (result.data.proveedores || []).forEach((supplier) => supplierSelect.add(new Option(
        `${supplier.razon_social} · ${supplier.tipo_documento} ${supplier.documento}`,
        String(supplier.id)
      )));
      productPicker.replaceChildren(new Option("Buscar producto para agregar...", ""));
      products.forEach((product) => productPicker.add(new Option(product.nombre, String(product.id))));
      productChoices = new Choices(productPicker, {
        searchPlaceholderValue: "Escribe para buscar productos...",
        shouldSort: false,
        allowHTML: false,
        itemSelectText: "",
        searchResultLimit: 100,
        noResultsText: "No se encontraron productos",
        noChoicesText: "No hay productos disponibles",
        position: "bottom",
      });
    };

    document.getElementById("btnAgregarLineaCompra").addEventListener("click", () => {
      const selectedId = productPicker.value;
      const product = products.find((entry) => String(entry.id) === selectedId);
      if (!product) {
        alertify.error("Busca y selecciona un producto antes de agregarlo.");
        return;
      }
      if (Array.from(linesBody.querySelectorAll(".compra-producto-id"), (input) => input.value).includes(selectedId)) {
        alertify.error("Ese producto ya está incluido en la compra.");
        refreshProductPicker();
        return;
      }
      addLine(product);
    });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      formError.textContent = "";
      formError.classList.add("hidden");
      if (!form.reportValidity()) return;
      const items = Array.from(linesBody.querySelectorAll("tr")).map((row) => ({
        producto_id: row.querySelector(".compra-producto-id").value,
        cantidad: row.querySelector(".compra-cantidad").value,
        costo_unitario: row.querySelector(".compra-costo").value,
        porcentaje_igv: row.querySelector(".compra-porcentaje-igv").value,
      }));
      if (!items.length) {
        formError.textContent = "Agrega al menos un producto a la compra.";
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
        window.location.href = `detalle_compra.php?id=${encodeURIComponent(result.id)}`;
      } catch (error) {
        formError.textContent = error.message || "No se pudo registrar la compra.";
        formError.classList.remove("hidden");
      } finally {
        saveButton.disabled = false;
        saveButton.classList.remove("opacity-50", "cursor-not-allowed");
      }
    });

    loadCatalogs().catch((error) => {
      formError.textContent = error.message || "No se pudieron cargar los catálogos.";
      formError.classList.remove("hidden");
      saveButton.disabled = true;
    });
  });
})();
