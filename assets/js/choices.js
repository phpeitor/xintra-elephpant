(function () {
  "use strict";

  const categoriaSelect = document.getElementById("categoria");
  const grupoSelect = document.getElementById("grupo");
  const stockInput = document.getElementById("stock");
  const stockContainer = stockInput.closest(".space-y-2");

  let categoria = new Choices(categoriaSelect, {
    searchPlaceholderValue: "Buscar categoría...",
    shouldSort: false,
    allowHTML: true, 
    itemSelectText: "",
  });

  async function cargarCategorias(tipo) {
    try {
      categoria.destroy();
      categoria = new Choices(categoriaSelect, {
        searchPlaceholderValue: "Buscar categoría...",
        shouldSort: false,
        allowHTML: true, 
        itemSelectText: "",
      });

      window.itemChoices.categoriaInstance = categoria; // 👈 Guardar referencia global

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

      categoria.clearChoices();
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

  function toggleStockField(tipo) {
    if (tipo === "PRODUCTO") {
      stockContainer.classList.remove("hidden");
      stockInput.setAttribute("data-rules", "required|numeric|min_value:1|max_value:500");
    } else {
      stockContainer.classList.add("hidden");
      stockInput.removeAttribute("data-rules");
      stockInput.value = "";
    }
  }

  grupoSelect.addEventListener("change", (e) => {
    const tipo = e.target.value;
    toggleStockField(tipo);
    cargarCategorias(tipo);
  });

  window.itemChoices = { toggleStockField, cargarCategorias, categoriaInstance: categoria };
})();
