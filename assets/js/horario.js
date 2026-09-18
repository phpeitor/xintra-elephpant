(() => {
  "use strict";

  const hash = new URLSearchParams(window.location.search).get("hash");
  const days = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
  const rows = document.querySelector("#schedule-rows");
  const form = document.querySelector("#schedule-form");

  const defaultSchedule = (day) => ({
    activo: day < 7 ? 1 : 0,
    hora_inicio: day < 7 ? "09:00" : "",
    hora_fin: day === 6 ? "13:00" : day < 6 ? "20:00" : "",
  });

  days.forEach((name, index) => {
    const day = index + 1;
    const value = defaultSchedule(day);
    rows.insertAdjacentHTML("beforeend", `<tr>
      <td class="font-medium">${name}</td>
      <td><label class="inline-flex items-center gap-2"><input type="checkbox" class="schedule-active ti-form-checkbox" data-day="${day}" ${value.activo ? "checked" : ""}><span class="text-xs">Activo</span></label></td>
      <td><input type="time" class="form-control schedule-start" data-day="${day}" value="${value.hora_inicio}" aria-label="Entrada ${name}"><small class="schedule-error" data-error="${day}-start"></small></td>
      <td><input type="time" class="form-control schedule-end" data-day="${day}" value="${value.hora_fin}" aria-label="Salida ${name}"><small class="schedule-error" data-error="${day}-end"></small></td>
    </tr>`);
  });

  const getField = (type, day) => document.querySelector(`.schedule-${type}[data-day="${day}"]`);
  const setError = (day, type, message) => {
    const field = getField(type, day);
    const error = document.querySelector(`[data-error="${day}-${type}"]`);
    field?.classList.toggle("schedule-invalid", Boolean(message));
    if (error) error.textContent = message || "";
  };

  const setDayState = (day) => {
    const active = getField("active", day).checked;
    ["start", "end"].forEach((type) => {
      const field = getField(type, day);
      field.disabled = !active;
      field.setCustomValidity("");
    });
    if (!active) {
      setError(day, "start", "");
      setError(day, "end", "");
    }
  };

  const validateDay = (day) => {
    const active = getField("active", day).checked;
    const start = getField("start", day);
    const end = getField("end", day);
    setError(day, "start", "");
    setError(day, "end", "");
    if (!active) return true;
    if (!start.value) {
      setError(day, "start", `${days[day - 1]}: indica la hora de entrada.`);
      return false;
    }
    if (!end.value) {
      setError(day, "end", `${days[day - 1]}: indica la hora de salida.`);
      return false;
    }
    if (start.value >= end.value) {
      setError(day, "start", "La entrada debe ser menor que la salida.");
      setError(day, "end", "La salida debe ser mayor que la entrada.");
      return false;
    }
    return true;
  };

  document.querySelectorAll(".schedule-active").forEach((input) => {
    input.addEventListener("change", () => setDayState(input.dataset.day));
    setDayState(input.dataset.day);
  });
  document.querySelectorAll(".schedule-start, .schedule-end").forEach((input) => {
    input.addEventListener("change", () => validateDay(input.dataset.day));
  });
  document.querySelector("#back-to-users")?.addEventListener("click", () => {
    window.location.href = "usuarios.php";
  });

  if (!hash) {
    alertify.error("No se identificó el usuario. Regresa a la lista e inténtalo nuevamente.");
    return;
  }

  fetch(`controller/horario.php?hash=${encodeURIComponent(hash)}`)
    .then((response) => response.json())
    .then((json) => {
      if (!json.ok) throw new Error(json.message || "No se encontró el usuario.");
      document.querySelector("#employee-name").textContent = `Horario de ${json.nombre}`;
      (json.data || []).forEach((item) => {
        if (!item.dia_semana) return;
        const day = item.dia_semana;
        getField("active", day).checked = Number(item.activo) === 1;
        getField("start", day).value = item.hora_inicio || "";
        getField("end", day).value = item.hora_fin || "";
        setDayState(day);
      });
    })
    .catch((error) => alertify.error(error.message));

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const valid = days.map((_, index) => validateDay(index + 1)).every(Boolean);
    if (!valid) {
      alertify.error("Revisa los horarios marcados antes de guardar.");
      return;
    }

    const horarios = days.map((_, index) => {
      const day = index + 1;
      const active = getField("active", day).checked;
      return {
        dia_semana: day,
        activo: active ? 1 : 0,
        hora_inicio: active ? getField("start", day).value : "",
        hora_fin: active ? getField("end", day).value : "",
      };
    });
    const body = new URLSearchParams({ hash, horarios: JSON.stringify(horarios) });
    fetch("controller/horario.php", { method: "POST", body })
      .then((response) => response.json())
      .then((json) => json.ok ? alertify.success(json.message) : alertify.error(json.message))
      .catch(() => alertify.error("No se pudo guardar el horario. Intenta nuevamente."));
  });
})();
