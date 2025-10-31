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
    numeric: (el) => /^-?\d+(\.\d+)?$/.test((el.value || "").trim()) || "Solo números (o decimales).",
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

  async function cargarUsuario(hash) {
    try {
      const res = await fetch(`controller/get_usuario.php?hash=${hash}`);
      const json = await res.json();

      if (!json.ok) {
        alert(json.message || "Usuario no encontrado");
        return;
      }

      const u = json.data;
      document.querySelector('#firstName').value = u.NOMBRES || '';
      document.querySelector('#lastName').value = u.APELLIDOS || '';
      document.querySelector('#documento').value = u.DOC || '';
      document.querySelector('#email').value = u.EMAIL || '';
      document.querySelector('#phone').value = u.TLF || '';

      document.querySelectorAll('input[name="sexo"]').forEach(r => {
        r.checked = parseInt(r.value) === parseInt(u.SEXO);
      });

      const toggle = document.querySelector('#estadoToggle');
      const estadoInput = document.querySelector('#estadoInput');

      if (toggle && estadoInput) {
        if (parseInt(u.IDESTADO) === 1) {
          toggle.classList.add('on');
          estadoInput.value = '1';
        } else {
          toggle.classList.remove('on');
          estadoInput.value = '0';
        }
      }

      inicializarToggle();

    } catch (err) {
      console.error("❌ Error al cargar usuario:", err);
    }
  }

  async function cargarItem(hash) {
    try {
      const res = await fetch(`controller/get_item.php?hash=${hash}`);
      const json = await res.json();

      if (!json.ok) {
        alert(json.message || "Item no encontrado");
        return;
      }

      const u = json.data;
      const grupoSelect = document.querySelector("#grupo");
      grupoSelect.value = u.grupo || "";
      window.itemChoices.toggleStockField(u.grupo);

      await window.itemChoices.cargarCategorias(u.grupo);
      await new Promise((resolve) => setTimeout(resolve, 150));

      const choices = window.itemChoices.categoriaInstance;
      const categoriaValor = String(u.categoria);
      const opciones = choices._currentState.choices.map((c) => String(c.value));

      if (opciones.includes(categoriaValor)) {
        choices.setChoiceByValue(categoriaValor);

        const sel = document.querySelector("#categoria");
        sel.value = categoriaValor;
        sel.dispatchEvent(new Event("change", { bubbles: true }));
      } else {
        console.warn("⚠️ Categoría no encontrada en las opciones cargadas");
      }

      document.querySelector("#nombre").value = u.nombre || "";
      document.querySelector("#precio").value = u.precio || "";
      document.querySelector("#stock").value = u.stock_final || "";

      const toggle = document.querySelector("#estadoToggle");
      const estadoInput = document.querySelector("#estadoInput");
      if (toggle && estadoInput) {
        if (parseInt(u.estado) === 1) {
          toggle.classList.add("on");
          estadoInput.value = "1";
        } else {
          toggle.classList.remove("on");
          estadoInput.value = "0";
        }
      }

      inicializarToggle();
    } catch (err) {
      console.error("❌ Error al cargar item:", err);
    }
  }

  function inicializarToggle() {
    const toggle = document.querySelector("#estadoToggle");
    const estadoInput = document.querySelector("#estadoInput");

    if (!toggle || !estadoInput) return;

    toggle.replaceWith(toggle.cloneNode(true));

    const nuevoToggle = document.querySelector("#estadoToggle");
    nuevoToggle.addEventListener("click", () => {
      nuevoToggle.classList.toggle("on");
      estadoInput.value = nuevoToggle.classList.contains("on") ? "1" : "0";
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
      const form = document.querySelector("form.ti-custom-validation, form.ti-custom-validation-user, form.ti-custom-validation-item");
      if (!form) return;
      attachToForm(form);

      const params = new URLSearchParams(window.location.search);
      const hash = params.get("hash");
      
      if (hash && form.classList.contains("ti-custom-validation-user")) {
        cargarUsuario(hash);
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "hash";
        hidden.value = hash;
        form.appendChild(hidden);
        form.dataset.mode = "update";
      }

      if (hash && form.classList.contains("ti-custom-validation-item")) {
        cargarItem(hash);
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "hash";
        hidden.value = hash;
        form.appendChild(hidden);
        form.dataset.mode = "update";
      }

      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const okCustom = typeof validateForm === "function" ? validateForm(form) : true;
        if (!okCustom) {
          form.reportValidity?.(); 
          return; 
        }

        if (!form.checkValidity()) {
          form.reportValidity(); 
          return; 
        }

        let actionUrl = "";
        let redirectUrl = "";

        if (form.classList.contains("ti-custom-validation")) {
          actionUrl = "controller/add_cliente.php";
          redirectUrl = "clientes.php";
        } else if (form.classList.contains("ti-custom-validation-user")) {
          const isUpdate = form.dataset.mode === "update";
          actionUrl = isUpdate ? "controller/upd_usuario.php" : "controller/add_usuario.php";
          redirectUrl = "usuarios.php";
        }else if (form.classList.contains("ti-custom-validation-item")) {
          const isUpdate = form.dataset.mode === "update";
          actionUrl = isUpdate ? "controller/upd_item.php" : "controller/add_item.php";
          redirectUrl = "items.php";
        }

        try {
          const formData = new FormData(form);
          const res = await fetch(actionUrl, { method: "POST", body: formData });
          const ct = res.headers.get("content-type") || "";
          const json = ct.includes("application/json")
            ? await res.json()
            : { ok: false, message: await res.text() };

          if (json.ok) {
            form.reset();
            window.location.href = redirectUrl;
          } else {
            alert("Error: " + (json.message || "No se pudo guardar."));
          }
        } catch (err) {
          console.error(err);
          alert("Fallo de red o excepción en JS. Revisa la consola.");
        }
    });
  });
})();
