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
        alertify.error(json.message || "Usuario no encontrado");
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
        alertify.error(json.message || "Item no encontrado");
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

  async function cargarStock(hash) {
    try {
      const res = await fetch(`controller/get_item.php?hash=${hash}`);
      const json = await res.json();

      if (!json.ok) {
        alertify.error(json.message || "Item no encontrado");
        return;
      }

      const u = json.data;
      document.querySelector("#producto").textContent  = u.nombre || "";
      document.querySelector("#categoria").textContent  = u.nom_categoria || "";
      document.querySelector("#almacen").textContent  = u.stock1 || "0";
      document.querySelector("#ventas").textContent  = u.stock2 || "0";

      updateStockVisual(u.stock_final);

    } catch (err) {
      console.error("❌ Error al cargar item:", err);
    }
  }

  async function cargarTicket(hash) {

    const cargandoDiv = document.querySelector('.cargando');
    cargandoDiv?.classList.remove('!hidden');

    try {
      const res = await fetch(`controller/venta/get_ticket.php?hash=${hash}`);
      const json = await res.json();

      if (!json.ok) {
        alertify.error(json.message || "Ticket no encontrado");
        return;
      }

      const u = json.data;
      const detalle = json.detalle || [];

      if (window.itemChoices?.clienteInstance) {
        setChoiceValueWhenReady(window.itemChoices.clienteInstance, u.cliente);
      }

      if (window.itemChoices?.personalInstance) {
        setChoiceValueWhenReady(window.itemChoices.personalInstance, u.usuario);
      }

      const fechaInput = document.querySelector("#date");
      if (fechaInput) fechaInput.value = u.fecha;

      const dsctoInput = document.querySelector("#descuento");
      if (dsctoInput) dsctoInput.value = u.dscto;

      const tipoDsctoInput = document.querySelector("#promo-code");
      if (tipoDsctoInput) {
        tipoDsctoInput.value = (u.tipo_dscto === "NA" || u.tipo_dscto === "NO APLICA") ? "" : u.tipo_dscto;
      }

      const pagoSelect = document.querySelector("#pago");
      if (pagoSelect) {
        pagoSelect.value = u.pago || "EFECTIVO"; 
      }

      const cartBody = document.querySelector("#cartBody");
      if (!cartBody) return;
      cartBody.innerHTML = ""; 

      detalle.forEach((item) => {
        const {
          id_productservice: id,
          item: nombre,
          categoria,
          precio,
          cantidad,
          subtotal,
          stock_final,
          tpo: tipo
        } = item;

        const row = document.createElement("tr");
        row.setAttribute("data-id", id);
        row.setAttribute("data-tipo", tipo);

        row.innerHTML = `
          <td>
            <div class="flex items-center">
              <div class="flex-auto">
                <div class="mb-1 text-[14px] font-semibold">
                  <a href="javascript:void(0);">${categoria}: ${nombre}</a>
                </div>
                ${
                  tipo === "PRODUCTO"
                    ? `<span class="badge ${
                        stock_final > 5
                          ? "bg-success/10 text-success"
                          : "bg-danger/10 text-danger"
                      }">
                        ${stock_final > 0 ? "Stock: " + stock_final : "Sin stock"}
                      </span>`
                    : ""
                }
              </div>
            </div>
          </td>
          <td>
            <div class="font-semibold text-[14px]">S/. ${parseFloat(precio).toFixed(2)}</div>
          </td>
          <td class="product-quantity-container">
            <div class="flex items-center flex-nowrap gap-1 rounded-full cart-input-group">
              <button type="button" class="ti-btn ti-btn-icon ti-btn-sm !rounded-full bg-primary/10 text-primary border btn-minus">
                <i class="ri-subtract-line"></i>
              </button>
              <input type="number" class="form-control form-control-sm !rounded-full text-center p-0 qty-input"
                value="${cantidad}" min="0" max="${stock_final || 0}" readonly>
              <button type="button" class="ti-btn ti-btn-icon ti-btn-sm !rounded-full bg-primary/10 text-primary border btn-plus">
                <i class="ri-add-line"></i>
              </button>
            </div>
          </td>
          <td>
            <div class="text-[14px] font-semibold total-cell">S/. ${parseFloat(subtotal).toFixed(2)}</div>
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
        if (typeof reinitTooltips === "function") reinitTooltips(row);
        if (typeof updateCartEvents === "function") updateCartEvents(row);

      });

      if (typeof actualizarResumen === "function") actualizarResumen();
      if (typeof validarCarrito === "function") validarCarrito();

    } catch (err) {
      console.error("❌ Error al cargar ticket:", err);
    }finally {
      cargandoDiv?.classList.add('!hidden');
    }
  }

  function setChoiceValueWhenReady(choiceInstance, value, maxRetries = 20) {
    let retries = 0;
    const interval = setInterval(() => {
      const choice = choiceInstance._store.choices.find(
        c => String(c.value) === String(value)
      );

      if (choice) {
        choiceInstance.setChoiceByValue(String(value));
        clearInterval(interval);
      } else if (retries++ >= maxRetries) {
        clearInterval(interval);
        console.warn(`⚠️ No se encontró el valor "${value}" en Choices.`);
      }
    }, 150);
  }

  function updateStockVisual(stock) {
    const box = document.getElementById("stock-box");
    const icon = document.getElementById("stock-icon");
    const badge = document.getElementById("stock-badge");
    const s = parseInt(stock ?? 0, 10);

    box.className = "box border";
    icon.className = "ri-circle-fill p-1 leading-none text-[0.4375rem] rounded-md me-2 align-middle";
    badge.className = "badge text-white";

    if (s <= 5) {
      box.classList.add("border-primarytint2color/50");
      icon.classList.add("bg-primarytint2color/10", "text-primarytint2color");
      badge.classList.add("bg-primarytint2color");
    } else if (s <= 10) {
      box.classList.add("border-primarytint3color/50");
      icon.classList.add("bg-primarytint3color/10", "text-primarytint3color");
      badge.classList.add("bg-primarytint3color");
    } else {
      box.classList.add("border-success/50");
      icon.classList.add("bg-success/10", "text-success");
      badge.classList.add("bg-success");
    }

    badge.textContent = s;
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
      const form = document.querySelector("form.ti-custom-validation, form.ti-custom-validation-user, form.ti-custom-validation-item, form.ti-custom-validation-ticket");

      const params = new URLSearchParams(window.location.search);
      const hash = params.get("hash");

      // --- FORMULARIOS (si existe alguno) ---
      if (form) {
        attachToForm(form);

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

        if (form.classList.contains("ti-custom-validation-ticket")) {
          const inputDate = document.querySelector("#date");

          if (!inputDate.value) {
            const hoy = new Date();
            const yyyy = hoy.getFullYear();
            const mm = String(hoy.getMonth() + 1).padStart(2, "0");
            const dd = String(hoy.getDate()).padStart(2, "0");
            inputDate.value = `${yyyy}-${mm}-${dd}`; 
          }

          flatpickr(inputDate, {
            dateFormat: "Y-m-d",
            defaultDate: inputDate.value, 
            maxDate: "today",
            disableMobile: true,
          });

          if(hash){
            setTimeout(() => {
              cargarTicket(hash);
            }, 300);
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "hash";
            hidden.value = hash;
            form.appendChild(hidden);
            form.dataset.mode = "update";
          }
        }

        form.addEventListener("submit", async (e) => {
          e.preventDefault();

          const submitBtn = form.querySelector("[type='submit']");
          if (submitBtn) {
            if (submitBtn.disabled) return; 
            submitBtn.disabled = true;
            submitBtn.classList.add("opacity-50", "cursor-not-allowed");
          }

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
            actionUrl = isUpdate
              ? "controller/upd_usuario.php"
              : "controller/add_usuario.php";
            redirectUrl = "usuarios.php";
          } else if (form.classList.contains("ti-custom-validation-item")) {
            const isUpdate = form.dataset.mode === "update";
            actionUrl = isUpdate
              ? "controller/upd_item.php"
              : "controller/add_item.php";
            redirectUrl = "items.php";
          }else if (form.classList.contains("ti-custom-validation-ticket")) {
            const isUpdate = form.dataset.mode === "update";
            actionUrl = isUpdate
              ? "controller/venta/upd_ticket.php"
              : "controller/venta/add_ticket.php";
            redirectUrl = "tickets.php";
          }

          try {
            const formData = new FormData(form);
            if (form.classList.contains("ti-custom-validation-ticket")) {
              const items =
                typeof window.obtenerItemsCarrito === "function"
                  ? window.obtenerItemsCarrito()
                  : [];

              if (!items.length) {
                alertify.error("Debe agregar al menos un ítem al carrito 🛒");
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
                return;
              }

              formData.append("items", JSON.stringify(items));
            }

            const res = await fetch(actionUrl, { method: "POST", body: formData });
            const ct = res.headers.get("content-type") || "";
            const json = ct.includes("application/json")
              ? await res.json()
              : { ok: false, message: await res.text() };

            if (json.ok) {
              form.reset();
              window.location.href = redirectUrl;
            } else {
              alertify.error("Error: " + (json.message || "No se pudo guardar."));
            }
          } catch (err) {
              console.error(err);
              alertify.error("Fallo de red o excepción en JS. Revisa la consola.");
          } finally {
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
              }
          }
        });
      }

      // --- SECCIÓN STOCK (sin form) ---
      const stockContainer = document.querySelector(".ti-stock-item");
      if (hash && stockContainer && stockContainer.classList.contains("ti-stock-item")) {
          cargarStock(hash);

          const cantidadInput = document.querySelector("#deal-lead-score");
          const fechaInput = document.querySelector("#targetDate");
          const btnGuardar = document.querySelector("#btnGuardarStock");

          if (window.flatpickr && fechaInput) {
            const ahora = new Date();
            const yyyy = ahora.getFullYear();
            const mm = String(ahora.getMonth() + 1).padStart(2, "0");
            const dd = String(ahora.getDate()).padStart(2, "0");
            const hh = String(ahora.getHours()).padStart(2, "0");
            const min = String(ahora.getMinutes()).padStart(2, "0");

            const fechaHoraActual = `${yyyy}-${mm}-${dd} ${hh}:${min}`;
            fechaInput.value = fechaHoraActual;

            flatpickr(fechaInput, {
              dateFormat: "Y-m-d H:i", 
              defaultDate: fechaHoraActual,
              enableTime: true,
              time_24hr: true, 
              minTime: "08:00",
              maxTime: "22:00",
              maxDate: "today",
              disableMobile: true,
              appendTo: fechaInput.closest('.modal') || document.body,
            });
          }

          btnGuardar?.addEventListener("click", async () => {
            const stock = cantidadInput.value.trim();
            const fecha = fechaInput.value.trim();

            if (!stock || isNaN(stock) || stock <= 0) {
              alertify.error("Por favor ingrese una cantidad válida");
              return;
            }

            try {
              const res = await fetch("./controller/add_stock.php", {
                method: "POST",
                body: new URLSearchParams({ stock, fecha, id: hash })
              });

              const data = await res.json();

              if (data.ok) {
                alertify.success("Stock guardado correctamente ✅");
                location.reload(); 
              } else {
                alertify.error("Error: " + (data.message || "No se pudo guardar"));
              }
            } catch (e) {
              console.error(e);
              alertify.error("Error al guardar stock.");
            }
          });
      }
      
  });
})();
