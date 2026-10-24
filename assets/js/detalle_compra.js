document.getElementById("btnImprimirCompra")?.addEventListener("click", () => window.print());

document.getElementById("btnConfirmarRecepcion")?.addEventListener("click", (event) => {
  const button = event.currentTarget;
  const hash = button.dataset.hash;
  let resolved = false;
  alertify.confirm(
    "Confirmar recepción de compra",
    "Se sumarán al inventario las cantidades de todos los productos de esta compra. Confirma solo si ya recibiste los productos.",
    async () => {
      if (resolved) return;
      resolved = true;
      button.disabled = true;
      try {
        const response = await fetch("controller/recibir_compra.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
          body: new URLSearchParams({ hash }),
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || "No se pudo confirmar la recepción.");
        alertify.success(result.message);
        window.setTimeout(() => window.location.reload(), 650);
      } catch (error) {
        alertify.error(error.message || "No se pudo confirmar la recepción.");
        button.disabled = false;
      }
    },
    () => {
      resolved = true;
      alertify.message("Recepción cancelada. No se modificó el inventario.");
    }
  ).set("labels", { ok: "Confirmar entrada", cancel: "Cancelar" });
});
