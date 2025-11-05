(function () {
  "use strict";

  const categoriaSelect = document.getElementById("categoria");
  const clienteSelect = document.getElementById("cliente");
  const personalSelect = document.getElementById("personal");
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

  let personal = new Choices(personalSelect, {
    searchPlaceholderValue: "Buscar personal...",
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
            precio: cat.precio,
            tipo: cat.tpo
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

  // ---- PERSONAL ----
  async function cargarPersonal() {
    try {
      personal.clearChoices();
      const response = await fetch(`controller/table_usuario.php`);
      const data = await response.json();

      if (!Array.isArray(data) || !data.length) {
        personal.setChoices(
          [{ value: "", label: "No hay usuarios registrados", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      const activos = data.filter(u => parseInt(u.IDESTADO) === 1);

      if (!activos.length) {
        personal.setChoices(
          [{ value: "", label: "No hay usuarios activos", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      const usuariosUnicos = activos.filter(
        (cli, index, self) =>
          index ===
          self.findIndex(
            (t) =>
              t.nombre_completo?.trim().toLowerCase() ===
              cli.nombre_completo?.trim().toLowerCase()
          )
      );

      personal.setChoices(
        usuariosUnicos.map((cli) => ({
          value: String(cli.IDPERSONAL),
          label: cli.nombre_completo,
        })),
        "value",
        "label",
        false
      );
    } catch (error) {
      console.error("Error cargando usuarios:", error);
      personal.setChoices(
        [{ value: "", label: "Error al cargar usuarios", disabled: true }],
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
      alertify.error("Seleccione un item primero 📦");
      return;
    }

    const { value: id, label: nombre, customProperties } = selected;
    const precio = parseFloat(customProperties?.precio) || 0;
    const stock = parseInt(customProperties?.stock) || 0;
    const tipo = customProperties?.tipo;


    if (cartBody.querySelector(`tr[data-id="${id}"]`)) {
      alertify.error("Este item ya está en el carrito 🛒");
      return;
    }

    if (tipo === "PRODUCTO" && stock <= 0 ) {
      const hash = md5(id); 
      const enlace = `<a href="stk_item.php?hash=${hash}" 
                    target="_blank" 
                    style="color:#fff; text-decoration:underline; font-weight:500;">
                    Ver stock
                  </a>`;
      alertify.error(`🚨 El producto "${nombre}" no tiene stock disponible. ${enlace}`);
      return;
    }

    const row = document.createElement("tr");
    row.setAttribute("data-id", id);
    row.setAttribute("data-tipo", tipo); 
    row.innerHTML = `
      <td>
        <div class="flex items-center">
          <div class="flex-auto">
            <div class="mb-1 text-[14px] font-semibold">
              <a href="javascript:void(0);">${nombre}</a>
            </div>
            ${
              tipo === "PRODUCTO"
                ? `<span class="badge ${
                    stock > 5
                      ? 'bg-success/10 text-success'
                      : 'bg-danger/10 text-danger'
                  }">
                    ${stock > 0 ? 'Stock: ' + stock : 'Sin stock'}
                  </span>`
                : ''
            }
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
    validarCarrito();
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
      validarCarrito();
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

    if (total === 0) {
      document.getElementById("subtotal").innerText = "S/. 0.00";
      document.getElementById("igv").innerText = "S/. 0.00";
      document.getElementById("total").innerText = "S/. 0.00";
      return;
    }

    const subtotal = total / 1.18; 
    const igv = total - subtotal;  

    document.getElementById("subtotal").innerText = `S/. ${subtotal.toFixed(2)}`;
    document.getElementById("igv").innerText = `S/. ${igv.toFixed(2)}`;
    document.getElementById("total").innerText = `S/. ${total.toFixed(2)}`;
  }

  function validarCarrito() {
    const cartContainer = document.getElementById("cart-container-delete");
    const cartEmpty = document.getElementById("cart-empty-cart");
    const cartBody = document.getElementById("cartBody");
    const tieneItems = cartBody.children.length > 0;

    if (tieneItems) {
      cartContainer.classList.remove("hidden", "!hidden");
      cartEmpty.classList.add("hidden");
    } else {
      cartContainer.classList.add("hidden");
      cartEmpty.classList.remove("hidden", "!hidden");
    }
  }

  // ---- CARGA INICIAL ----
  cargarClientes();
  cargarPersonal();
  validarCarrito();

  window.itemChoices = {
    cargarCategorias,
    cargarClientes,
    categoriaInstance: categoria,
    clienteInstance: cliente,
  };

  window.obtenerItemsCarrito = function () {
    const rows = document.querySelectorAll("#cartBody tr");
    const items = [];

    rows.forEach((row) => {
      const id = row.getAttribute("data-id");
      const tipo = row.getAttribute("data-tipo");
      const precioText = row.querySelector("td:nth-child(2)").innerText.replace("S/.", "").trim();
      const precio = parseFloat(precioText) || 0;
      const cantidad = parseFloat(row.querySelector(".qty-input").value) || 0;
      const subtotalText = row.querySelector(".total-cell").innerText.replace("S/.", "").trim();
      const subtotal = parseFloat(subtotalText) || 0;

      if (id && cantidad > 0) {
        items.push({ id, precio, cantidad, subtotal, tipo });
      }
    });

    return items;
  };

})();
