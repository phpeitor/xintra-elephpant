(function () {
  "use strict";

  const categoriaSelect = document.getElementById("categoria");
  const clienteSelect = document.getElementById("cliente");
  const grupoSelect = document.getElementById("grupo");

  let categoria = new Choices(categoriaSelect, {
    searchPlaceholderValue: "Buscar categoría...",
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

  // ---- CATEGORÍAS ----
  async function cargarCategorias(tipo) {
    try {
      // 🔹 Destruir instancia anterior completamente (el truco para limpiar)
      categoria.destroy();

      // 🔹 Crear una nueva instancia limpia
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
  });

  // ---- CARGA INICIAL ----
  cargarClientes();

  // ---- Exportar referencias globales (opcional) ----
  window.itemChoices = {
    cargarCategorias,
    cargarClientes,
    categoriaInstance: categoria,
    clienteInstance: cliente,
  };
})();
