(function () {
  "use strict";

  const categoriaSelect = document.getElementById("categoria");
  const clienteSelect = document.getElementById("cliente");
  const itemSelect = document.getElementById("item");
  const grupoSelect = document.getElementById("grupo");

  let categoria = new Choices(categoriaSelect, {
    searchPlaceholderValue: "Buscar categoría...",
    shouldSort: false,
    allowHTML: true,
    itemSelectText: "",
  });

  let item = new Choices(itemSelect, {
    searchPlaceholderValue: "Buscar item...",
    shouldSort: false,
    allowHTML: true,
    itemSelectText: "",
  });

  let cliente = new Choices(clienteSelect, {
    searchPlaceholderValue: "Buscar cliente...",
    shouldSort: false,
    allowHTML: true,
    itemSelectText: "",
  });

  async function cargarItems(categoria) {
    try {
      item.destroy();
      item = new Choices(itemSelect, {
        searchPlaceholderValue: "Buscar item...",
        shouldSort: false,
        allowHTML: true,
        itemSelectText: "",
      });

      const response = await fetch(`controller/venta/get_item_categoria.php?categoria=${encodeURIComponent(categoria)}`);
      const data = await response.json();

      if (!Array.isArray(data) || !data.length) {
        item.setChoices(
          [{ value: "", label: "Sin items para esta categoría", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      item.setChoices(
        data.map(cat => ({
          value: String(cat.id),
          label: cat.nombre,
          customProperties: {
            stock: cat.stock_final,
            precio: cat.precio
          }
        })),
        "value",
        "label",
        false
      );
    } catch (error) {
      console.error("Error cargando items:", error);
    }
  }

  async function cargarCategorias(tipo) {
    try {
      categoria.destroy();
      categoria = new Choices(categoriaSelect, {
        searchPlaceholderValue: "Buscar categoría...",
        shouldSort: false,
        allowHTML: true,
        itemSelectText: "",
      });

      if (!tipo) {
        categoria.setChoices(
          [{ value: "", label: "Seleccione un grupo primero", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      const response = await fetch(`controller/get_categoria.php?tpo=${encodeURIComponent(tipo)}`);
      const data = await response.json();

      if (!Array.isArray(data) || !data.length) {
        categoria.setChoices(
          [{ value: "", label: "Sin categorías para este grupo", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      categoria.setChoices(
        data.map(cat => ({
          value: String(cat.id),
          label: cat.nombre,
        })),
        "value",
        "label",
        false
      );
    } catch (error) {
      console.error("Error cargando categorías:", error);
    }
  }

  // ---- CLIENTES ----
  async function cargarClientes() {
    try {
      cliente.clearChoices();

      const response = await fetch(`controller/table_cliente.php`);
      const data = await response.json();

      if (!Array.isArray(data) || !data.length) {
        cliente.setChoices(
          [{ value: "", label: "No hay clientes registrados", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      const clientesUnicos = data.filter(
        (cli, index, self) =>
          index ===
          self.findIndex(
            (t) =>
              t.nombre_completo?.trim().toLowerCase() ===
              cli.nombre_completo?.trim().toLowerCase()
          )
      );

      cliente.setChoices(
        clientesUnicos.map((cli) => ({
          value: String(cli.id),
          label: cli.nombre_completo,
        })),
        "value",
        "label",
        false
      );
    } catch (error) {
      console.error("Error cargando clientes:", error);
      cliente.setChoices(
        [{ value: "", label: "Error al cargar clientes", disabled: true }],
        "value",
        "label",
        false
      );
    }
  }

  // ---- EVENTOS ----
  grupoSelect.addEventListener("change", (e) => {
    const tipo = e.target.value;
    cargarCategorias(tipo);

    item.destroy();
    item = new Choices(itemSelect, {
      searchPlaceholderValue: "Buscar item...",
      shouldSort: false,
      allowHTML: true,
      itemSelectText: "",
    });

  });

  categoriaSelect.addEventListener("change", (e) => {
    const categoria = e.target.value;
    cargarItems(categoria);
  });


  const cartBody = document.getElementById("cartBody");
  const btnAddToCart = document.getElementById("btnAddToCart");

  // --- Evento: agregar al carrito ---
  btnAddToCart.addEventListener("click", () => {
    const selected = item.getValue();
    if (!selected || !selected.value) {
      alert("Seleccione un item primero.");
      return;
    }

    const { value: id, label: nombre, customProperties } = selected;
    const precio = parseFloat(customProperties?.precio) || 0;
    const stock = parseInt(customProperties?.stock) || 0;


    if (cartBody.querySelector(`tr[data-id="${id}"]`)) {
      alert("Este item ya está en el carrito.");
      return;
    }

    if (stock <= 0) {
      alert(`El producto "${nombre}" no tiene stock disponible.`);
      return;
    }

    const row = document.createElement("tr");
    row.setAttribute("data-id", id);
    row.innerHTML = `
      <td>
        <div class="flex items-center">
          <div class="flex-auto">
            <div class="mb-1 text-[14px] font-semibold">
              <a href="javascript:void(0);">${nombre}</a>
            </div>
            <span class="badge ${stock > 5 ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'}">
              ${stock > 0 ? 'Stock: ' + stock : 'Sin stock'}
            </span>
          </div>
        </div>
      </td>
      <td>
        <div class="font-semibold text-[14px]">S/. ${precio.toFixed(2)}</div>
      </td>
      <td class="product-quantity-container">
        <div class="flex items-center flex-nowrap gap-1 rounded-full cart-input-group">
          <button type="button" class="ti-btn ti-btn-icon ti-btn-sm !rounded-full bg-primary/10 text-primary border btn-minus">
            <i class="ri-subtract-line"></i>
          </button>
          <input type="number" class="form-control form-control-sm !rounded-full text-center p-0 qty-input" value="1" min="0" max="${stock}" readonly>
          <button type="button" class="ti-btn ti-btn-icon ti-btn-sm !rounded-full bg-primary/10 text-primary border btn-plus">
            <i class="ri-add-line"></i>
          </button>
        </div>
      </td>
      <td>
        <div class="text-[14px] font-semibold total-cell">S/. ${precio.toFixed(2)}</div>
      </td>
      <td>
        <div class="flex items-center">
          <a href="#" class="ti-btn ti-btn-icon bg-primary text-white ti-btn-sm me-1 hs-tooltip-toggle"> 
            <i class="ri-heart-line"></i> 
          </a>
          <div class="hs-tooltip ti-main-tooltip ltr:[--placement:left] rtl:[--placement:right]"> 
            <a href="javascript:void(0);" class="ti-btn ti-btn-icon bg-danger text-white ti-btn-sm btn-remove waves-effect waves-light">
              <i class="ri-delete-bin-line"></i> 
              <span class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm hidden" role="tooltip"> Eliminar</span> 
            </a> 
          </div>
        </div>
      </td>
    `;

    cartBody.appendChild(row);
    reinitTooltips(row);
    updateCartEvents(row);
    actualizarResumen();
  });

  function reinitTooltips(container = document) {
    if (window.HSTooltip && typeof window.HSTooltip.autoInit === "function") {
      window.HSTooltip.autoInit(container);
    }
  }

  function updateCartEvents(row) {
    const btnMinus = row.querySelector(".btn-minus");
    const btnPlus = row.querySelector(".btn-plus");
    const qtyInput = row.querySelector(".qty-input");
    const totalCell = row.querySelector(".total-cell");
    const btnRemove = row.querySelector(".btn-remove");

    const precio = parseFloat(row.querySelector("td:nth-child(2)").innerText.replace("S/.", "")) || 0;

    const updateTotal = () => {
      const qty = parseInt(qtyInput.value) || 1;
      totalCell.innerText = `S/.${(qty * precio).toFixed(2)}`;
      actualizarResumen(); 
    };

    btnMinus.addEventListener("click", () => {
      let val = parseInt(qtyInput.value) || 1;
      if (val > 1) {
        qtyInput.value = val - 1;
        updateTotal();
      }
      actualizarResumen(); 
    });

    btnPlus.addEventListener("click", () => {
      let val = parseInt(qtyInput.value) || 1;
      const max = parseInt(qtyInput.getAttribute("max")) || 99;
      if (val < max) {
        qtyInput.value = val + 1;
        updateTotal();
      }
      actualizarResumen(); 
    });

    qtyInput.addEventListener("input", updateTotal);

    btnRemove.addEventListener("click", () => {
      row.remove();
      actualizarResumen();
    });
  }

  function actualizarResumen() {
    const rows = cartBody.querySelectorAll("tr");
    let total = 0;

    rows.forEach(row => {
      const totalCell = row.querySelector(".total-cell");
      if (totalCell) {
        const valor = parseFloat(totalCell.innerText.replace("S/.", "").trim()) || 0;
        total += valor;
      }
    });

    // Si el total es 0, todo en cero
    if (total === 0) {
      document.getElementById("subtotal").innerText = "S/. 0.00";
      document.getElementById("igv").innerText = "S/. 0.00";
      document.getElementById("total").innerText = "S/. 0.00";
      return;
    }

    // Calcular desde el total
    const subtotal = total / 1.18; // base imponible
    const igv = total - subtotal;  // IGV (18%)

    document.getElementById("subtotal").innerText = `S/. ${subtotal.toFixed(2)}`;
    document.getElementById("igv").innerText = `S/. ${igv.toFixed(2)}`;
    document.getElementById("total").innerText = `S/. ${total.toFixed(2)}`;
  }

  // ---- CARGA INICIAL ----
  cargarClientes();

  window.itemChoices = {
    cargarCategorias,
    cargarClientes,
    categoriaInstance: categoria,
    clienteInstance: cliente,
  };
})();
