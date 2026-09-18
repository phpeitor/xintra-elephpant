(() => {
  "use strict";

  const calendarElement = document.querySelector("#attendance-calendar");
  const userSelect = document.querySelector("#attendance-user");
  const entryButton = document.querySelector("#mark-entry");
  const exitButton = document.querySelector("#mark-exit");
  const activity = document.querySelector("#attendance-activity");
  const emptyState = document.querySelector("#attendance-empty");
  const errorState = document.querySelector("#attendance-error");
  const moreModal = document.querySelector("#attendance-more-modal");
  const moreTitle = document.querySelector("#attendance-more-title");
  const moreList = document.querySelector("#attendance-more-list");
  let calendar;
  let userChoices;

  const setState = (state, message = "") => {
    emptyState.classList.toggle("hidden", state !== "empty");
    errorState.classList.toggle("hidden", state !== "error");
    if (message) errorState.textContent = message;
  };
  const selectedUser = () => userChoices?.getValue(true) || "";

  const formatAttendanceDate = (value) => {
    const match = String(value || "").match(/^(\d{4})-(\d{2})-(\d{2})[ T]?(\d{2}):(\d{2}):(\d{2})/);
    return match ? `${match[3]}/${match[2]}/${match[1]} ${match[4]}:${match[5]}:${match[6]}` : value;
  };

  const formatDayTitle = (value) => {
    const match = String(value || "").match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!match) return "Registros del día";
    const date = new Date(Date.UTC(Number(match[1]), Number(match[2]) - 1, Number(match[3])));
    return date.toLocaleDateString("es-PE", { day: "numeric", month: "long", year: "numeric", timeZone: "UTC" });
  };

  const closeMoreModal = () => moreModal.classList.add("hidden");
  const openMoreModal = (events) => {
    if (!events.length) return;
    moreTitle.textContent = formatDayTitle(events[0].extendedProps.fecha);
    moreList.replaceChildren();
    events.forEach((event) => {
      const item = document.createElement("div");
      item.className = `attendance-more-item ${event.extendedProps.tipo === "ENTRADA" ? "is-entry" : "is-exit"}`;
      const time = document.createElement("strong");
      time.textContent = formatAttendanceDate(event.extendedProps.fecha).split(" ")[1] || "";
      const label = document.createElement("span");
      label.textContent = `${event.extendedProps.tipo === "ENTRADA" ? "Entrada" : "Salida"} · ${event.extendedProps.usuario}`;
      item.append(time, label);
      moreList.appendChild(item);
    });
    moreModal.classList.remove("hidden");
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
          ? json.data.map((item) => `<li class="attendance-activity-item ${item.tipo === "ENTRADA" ? "is-entry" : "is-exit"}"><span class="attendance-activity-dot"></span><div><strong>${item.tipo === "ENTRADA" ? "Entrada" : "Salida"}</strong><span>${item.usuario}</span><time>${formatAttendanceDate(item.fecha)}</time></div></li>`).join("")
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
      locale: "es", firstDay: 1, initialView: "dayGridMonth", height: "auto", expandRows: true, nowIndicator: true, navLinks: true, dayMaxEvents: 2,
      headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,listWeek" },
      buttonText: { today: "Hoy", month: "Mes", week: "Semana", list: "Lista" },
      moreLinkClick: (info) => {
        openMoreModal(info.allSegs.map((segment) => segment.event));
        return false;
      },
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
  document.querySelector("#attendance-more-close").addEventListener("click", closeMoreModal);
  moreModal.addEventListener("click", (event) => { if (event.target === moreModal) closeMoreModal(); });
  document.addEventListener("keydown", (event) => { if (event.key === "Escape") closeMoreModal(); });
  loadUsers().then(() => { buildCalendar(); loadActivity(); refreshStatus(); }).catch((error) => setState("error", error.message));
})();
