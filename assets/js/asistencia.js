(() => {
  "use strict";

  const calendarElement = document.querySelector("#attendance-calendar");
  const userSelect = document.querySelector("#attendance-user");
  const entryButton = document.querySelector("#mark-entry");
  const exitButton = document.querySelector("#mark-exit");
  const activity = document.querySelector("#attendance-activity");
  const emptyState = document.querySelector("#attendance-empty");
  const errorState = document.querySelector("#attendance-error");
  let calendar;
  let userChoices;

  const setState = (state, message = "") => {
    emptyState.classList.toggle("hidden", state !== "empty");
    errorState.classList.toggle("hidden", state !== "error");
    if (message) errorState.textContent = message;
  };
  const selectedUser = () => userChoices?.getValue(true) || "";

  const formatAttendanceDate = (value) => {
    const match = String(value || "").match(/^(\d{4})-(\d{2})-(\d{2})T?(\d{2}):(\d{2}):(\d{2})/);
    return match ? `${match[3]}/${match[2]}/${match[1]} ${match[4]}:${match[5]}:${match[6]}` : value;
  };

  const refreshStatus = () => {
    const id = selectedUser();
    entryButton.disabled = !id;
    exitButton.disabled = true;
    if (!id) return;
    fetch(`controller/asistencia.php?action=status&usuario=${encodeURIComponent(id)}`)
      .then((response) => response.json())
      .then((json) => {
        if (!json.ok) throw new Error(json.message);
        entryButton.disabled = json.estado === "ENTRADA";
        exitButton.disabled = json.estado !== "ENTRADA";
      })
      .catch((error) => alertify.error(error.message));
  };

  const loadActivity = () => {
    const query = selectedUser() ? `&usuario=${encodeURIComponent(selectedUser())}` : "";
    fetch(`controller/asistencia.php?action=activity${query}`)
      .then((response) => response.json())
      .then((json) => {
        if (!json.ok) throw new Error(json.message);
        activity.innerHTML = json.data.length
          ? json.data.map((item) => `<li class="attendance-activity-item ${item.tipo === "ENTRADA" ? "is-entry" : "is-exit"}"><span class="attendance-activity-dot"></span><div><strong>${item.tipo === "ENTRADA" ? "Entrada" : "Salida"}</strong><span>${item.usuario}</span><time>${formatAttendanceDate(item.start)}</time></div></li>`).join("")
          : '<li class="text-textmuted dark:text-textmuted/50 text-sm py-3">No hay registros recientes.</li>';
      })
      .catch((error) => alertify.error(error.message));
  };

  const mark = (tipo) => {
    const body = new URLSearchParams({ action: "mark", usuario: selectedUser(), tipo });
    entryButton.disabled = true;
    exitButton.disabled = true;
    fetch("controller/asistencia.php", { method: "POST", body })
      .then((response) => response.json())
      .then((json) => {
        if (!json.ok) throw new Error(json.message);
        alertify.success(json.message);
        calendar?.refetchEvents();
        loadActivity();
        refreshStatus();
      })
      .catch((error) => { alertify.error(error.message); refreshStatus(); });
  };

  const loadUsers = () => fetch("controller/asistencia.php?action=users")
    .then((response) => response.json())
    .then((json) => {
      if (!json.ok) throw new Error(json.message);
      json.data.forEach((user) => userSelect.insertAdjacentHTML("beforeend", `<option value="${user.id}">${user.nombre}</option>`));
      userChoices = new Choices(userSelect, {
        searchEnabled: true,
        searchPlaceholderValue: "Buscar usuario...",
        itemSelectText: "",
        shouldSort: false,
        allowHTML: false,
      });
    });

  const buildCalendar = () => {
    calendar = new FullCalendar.Calendar(calendarElement, {
      locale: "es", timeZone: "UTC", firstDay: 1, initialView: "dayGridMonth", height: "auto", expandRows: true, nowIndicator: true, navLinks: true, dayMaxEvents: 2,
      headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,listWeek" },
      buttonText: { today: "Hoy", month: "Mes", week: "Semana", list: "Lista" },
      events: (fetchInfo, successCallback, failureCallback) => {
        const params = new URLSearchParams({ action: "events", start: fetchInfo.startStr, end: fetchInfo.endStr });
        if (selectedUser()) params.set("usuario", selectedUser());
        fetch(`controller/asistencia.php?${params}`)
          .then((response) => response.json())
          .then((json) => {
            if (!json.ok) throw new Error(json.message);
            setState(json.data.length ? "ready" : "empty");
            successCallback(json.data);
          })
          .catch((error) => { setState("error", error.message); failureCallback(error); });
      },
      eventClick: (info) => alertify.message(`${info.event.extendedProps.tipo}: ${info.event.extendedProps.usuario}<br>${formatAttendanceDate(info.event.extendedProps.fecha)}`),
    });
    calendar.render();
  };

  userSelect.addEventListener("change", () => { refreshStatus(); loadActivity(); calendar?.refetchEvents(); });
  entryButton.addEventListener("click", () => mark("ENTRADA"));
  exitButton.addEventListener("click", () => mark("SALIDA"));
  loadUsers().then(() => { buildCalendar(); loadActivity(); refreshStatus(); }).catch((error) => setState("error", error.message));
})();
