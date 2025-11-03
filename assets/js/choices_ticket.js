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

  // ---- CARGA INICIAL ----
  cargarClientes();

  window.itemChoices = {
    cargarCategorias,
    cargarClientes,
    categoriaInstance: categoria,
    clienteInstance: cliente,
  };
})();
