(function () {
  "use strict";

  const ruleDefs = {
    required: (el) => {
      if (el.type === "checkbox") return el.checked || "Este campo es obligatorio.";
      if (el.type === "radio") {
        const group = el.name;
        const anyChecked = !!el.form.querySelector(`input[type="radio"][name="${group}"]:checked`);
        return anyChecked || "Selecciona una opción.";
      }
      return (el.value || "").trim() ? true : "Este campo es obligatorio.";
    },
    email:   (el) => /\S+@\S+\.\S+/.test((el.value || "").trim()) || "Correo inválido.",
    numeric: (el) => /^-?\d+$/.test((el.value || "").trim()) || "Solo números.",
    min:     (n)  => (el) => ((el.value || "").trim().length >= +n) || `Mínimo ${n} caracteres.`,
    max:     (n)  => (el) => ((el.value || "").trim().length <= +n) || `Máximo ${n} caracteres.`,
    length:  (n)  => (el) => ((el.value || "").trim().length === +n) || `Debe tener ${n} caracteres.`,
 
  };

  function findErrorEl(el) {
    const key = el.type === "radio" ? el.name : (el.id || el.name);
    if (!key) return null;
    return el.form.querySelector(`[data-error-for="${CSS.escape(key)}"]`);
  }

  function setError(el, message) {
    const errorEl = findErrorEl(el);
    el.setAttribute("aria-invalid", "true");
    el.classList.add("ring-1", "ring-red-500", "focus:ring-red-500");
    if (errorEl) {
      errorEl.textContent = message || "";
      errorEl.classList.toggle("hidden", !message);
    }
    // Para usar también el Constraint Validation API si prefieres reportValidity():
    el.setCustomValidity(message || "");
  }

  function clearError(el) {
    const errorEl = findErrorEl(el);
    el.removeAttribute("aria-invalid");
    el.classList.remove("ring-1", "ring-red-500", "focus:ring-red-500");
    if (errorEl) {
      errorEl.textContent = "";
      errorEl.classList.add("hidden");
    }
    el.setCustomValidity("");
  }

  // ====== Ejecutor de reglas por campo ======
  function validateElement(el) {
    const raw = (el.getAttribute("data-rules") || "").trim();
    if (!raw) { clearError(el); return true; }

    const parts = raw.split("|").map(s => s.trim()).filter(Boolean);

    for (const part of parts) {
      const [name, arg] = part.split(":");
      const def = ruleDefs[name];
      if (!def) continue;

      const fn = typeof def === "function" && arg === undefined ? def : def(arg);

      const res = fn(el);
      const ok = res === true;
      if (!ok) {
        const msg = typeof res === "string" ? res : "Valor inválido.";
        setError(el, msg);
        return false;
      }
    }

    clearError(el);
    return true;
  }

  function validateForm(form) {
    const controls = Array.from(form.querySelectorAll("[data-rules]"));
    let ok = true;
    for (const el of controls) {
      if (el.type === "radio") {
        const firstInGroup = form.querySelector(`input[type="radio"][name="${el.name}"]`);
        if (el !== firstInGroup) continue;
      }
      ok = validateElement(el) && ok;
    }
    return ok;
  }

  function attachToForm(form) {
    form.addEventListener("submit", (e) => {
      const ok = validateForm(form);
      if (!ok) {
        e.preventDefault();
        const firstInvalid = form.querySelector("[aria-invalid='true']");
        firstInvalid?.scrollIntoView({ behavior: "smooth", block: "center" });
        form.reportValidity?.();
      }
    });

    form.addEventListener("input", (e) => {
      const el = e.target;
      if (!(el instanceof HTMLElement)) return;
      if (!el.matches("[data-rules]")) return;

      if (el.type === "radio") {
        const firstInGroup = form.querySelector(`input[type="radio"][name="${el.name}"]`);
        validateElement(firstInGroup);
      } else {
        validateElement(el);
      }
    });

    form.addEventListener("change", (e) => {
      const el = e.target;
      if (!(el instanceof HTMLElement)) return;
      if (el.matches("[data-rules]")) validateElement(el);
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
      const form = document.querySelector('form.ti-custom-validation');
      if (!form) {
        console.error("❌ No encontré <form.ti-custom-validation>");
        return;
      }

      // Si tienes attachToForm (tu validador), mantenlo
      document.querySelectorAll("form.ti-custom-validation").forEach(attachToForm);

      form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Opción A: tu validador (recomendada si ya lo tienes)
      const okCustom = typeof validateForm === "function" ? validateForm(form) : true;
      if (!okCustom) {
        form.reportValidity?.(); // opcional
        return; // NO hacer fetch
      }

      // Opción B (extra): validación nativa por si no tienes la A
      if (!form.checkValidity()) {
        form.reportValidity(); // muestra tooltips nativos
        return; // NO hacer fetch
      }

      // Si llegaste aquí, puedes enviar:
      try {
        const formData = new FormData(form);
        const res = await fetch('php/add_cliente.php', { method: 'POST', body: formData });
        const ct = res.headers.get('content-type') || '';
        const json = ct.includes('application/json') ? await res.json() : { ok:false, message: await res.text() };

        if (json.ok) {
          //alert('Cliente guardado con éxito, ID: ' + json.id);
          form.reset();
          window.location.href = "clientes.html"
        } else {
          alert('Error: ' + (json.message || 'No se pudo guardar.'));
        }
      } catch (err) {
        console.error(err);
        alert('Fallo de red o excepción en JS. Revisa la consola.');
      }
    });
  });
})();
