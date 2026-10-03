(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("facturacion-ticket-form");
    if (!form) return;

    const modalOpen = document.getElementById("facturacion-ticket-open");
    const modalClose = document.querySelector('#facturacion-ticket-modal [data-hs-overlay="#facturacion-ticket-modal"]');
    const tipoInput = document.getElementById("facturacion-tipo");
    const addressGroup = document.getElementById("facturacion-datos-factura");
    const addressInput = document.getElementById("facturacion-direccion");
    const message = document.getElementById("facturacion-ticket-mensaje");
    const submitButton = document.getElementById("facturacion-ticket-submit");

    const showMessage = (text) => {
      message.textContent = text;
      message.classList.toggle("hidden", !text);
    };

    const updateDocumentType = () => {
      const isInvoice = tipoInput.value === "1";
      addressGroup.classList.toggle("hidden", !isInvoice);
      addressInput.required = isInvoice;
    };

    const postEmission = async (payload) => {
      const response = await fetch("controller/venta/emitir_comprobante.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
        body: new URLSearchParams(payload),
      });
      const result = await response.json();
      if (!response.ok || !result.ok) {
        throw new Error(result.message || "No se pudo emitir el comprobante.");
      }
      return result;
    };

    const reloadAfterResult = (result) => {
      showMessage("");
      if (result.enlace_pdf) {
        window.open(result.enlace_pdf, "_blank", "noopener,noreferrer");
      }
      window.location.reload();
    };

    window.facturacionTicket = {
      abrir(row) {
        form.reset();
        showMessage("");
        document.getElementById("facturacion-ticket-hash").value = md5(String(row.id));
        document.getElementById("facturacion-documento").value = row.cliente_documento || "";
        document.getElementById("facturacion-denominacion").value = row.cliente_denominacion || "";
        document.getElementById("facturacion-email").value = row.cliente_email || "";
        tipoInput.value = "2";
        updateDocumentType();
        modalOpen.click();
      },

      reintentar(row) {
        const hash = md5(String(row.id));
        const tipo = String(row.comprobante_tipo || "");
        alertify.confirm(
          "Reintentar emisión",
          `¿Reintentar el mismo comprobante ${row.comprobante_serie || ""}-${row.comprobante_numero || ""}?`,
          async () => {
            try {
              const result = await postEmission({ hash, tipo, reintento: "1" });
              alertify.success(result.mensaje || "Comprobante emitido correctamente.");
              reloadAfterResult(result);
            } catch (error) {
              alertify.error(error.message);
              window.setTimeout(() => window.location.reload(), 1200);
            }
          },
          () => alertify.message("Reintento cancelado.")
        ).set("labels", { ok: "Reintentar", cancel: "Cancelar" });
      },
    };

    tipoInput.addEventListener("change", updateDocumentType);

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!form.reportValidity()) return;

      const data = Object.fromEntries(new FormData(form).entries());
      alertify.confirm(
        "Confirmar emisión",
        `Se enviará ${tipoInput.value === "1" ? "una factura" : "una boleta"} al entorno configurado de NubeFact. Esta acción no se puede deshacer desde el sistema. ¿Continuar?`,
        async () => {
          submitButton.disabled = true;
          showMessage("");
          try {
            const result = await postEmission(data);
            alertify.success(result.mensaje || "Comprobante emitido correctamente.");
            modalClose?.click();
            reloadAfterResult(result);
          } catch (error) {
            showMessage(error.message);
            alertify.error(error.message);
            window.setTimeout(() => window.location.reload(), 1500);
          } finally {
            submitButton.disabled = false;
          }
        },
        () => alertify.message("Emisión cancelada.")
      ).set("labels", { ok: "Emitir", cancel: "Cancelar" });
    });
  });
})();
