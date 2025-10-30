(function () {
  "use strict";

  const categoriaSelect = document.getElementById("categoria");
  const grupoSelect = document.getElementById("grupo");
  const stockInput = document.getElementById("stock");
  const stockContainer = stockInput.closest(".space-y-2"); 

  let categoria = new Choices(categoriaSelect, {
    searchPlaceholderValue: "Buscar categoría...",
    shouldSort: false,
    itemSelectText: "",
  });

  function toggleStockField(tipo) {
    if (tipo === "PRODUCTO") {
      stockContainer.classList.remove("hidden");
      stockInput.setAttribute(
        "data-rules",
        "required|numeric|min_value:1|max_value:500"
      );
    } else if (tipo === "SERVICIO") {
      stockContainer.classList.add("hidden");
      stockInput.removeAttribute("data-rules");
      stockInput.value = ""; // limpia valor anterior
    } else {
      stockContainer.classList.add("hidden");
      stockInput.removeAttribute("data-rules");
      stockInput.value = "";
    }
  }

  async function cargarCategorias(tipo) {
    try {
      categoria.destroy();
      categoria = new Choices(categoriaSelect, {
        searchPlaceholderValue: "Buscar categoría...",
        shouldSort: false,
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

      categoria.setChoices(
        [{ value: "", label: "Cargando categorías...", disabled: true }],
        "value",
        "label",
        false
      );

      const response = await fetch(`php/get_categoria.php?tpo=${encodeURIComponent(tipo)}`);
      if (!response.ok) throw new Error("Error al obtener categorías");

      const data = await response.json();

      categoria.clearChoices();

      if (!data || data.length === 0) {
        categoria.setChoices(
          [{ value: "", label: "Sin categorías disponibles", disabled: true }],
          "value",
          "label",
          false
        );
        return;
      }

      categoria.setChoices(
        data.map((cat) => ({
          value: cat.id,
          label: cat.nombre,
        })),
        "value",
        "label",
        false
      );
    } catch (error) {
      console.error("Error cargando categorías:", error);
      categoria.destroy();
      categoria = new Choices(categoriaSelect, {
        searchPlaceholderValue: "Buscar categoría...",
        shouldSort: false,
        itemSelectText: "",
      });
      categoria.setChoices(
        [{ value: "", label: "Error al cargar", disabled: true }],
        "value",
        "label",
        false
      );
    }
  }

  grupoSelect.addEventListener("change", (e) => {
    const tipo = e.target.value;
    toggleStockField(tipo); 

    categoria.destroy();
    categoria = new Choices(categoriaSelect, {
      searchPlaceholderValue: "Buscar categoría...",
      shouldSort: false,
      itemSelectText: "",
    });

    cargarCategorias(tipo);
  });

  toggleStockField("");
})();
