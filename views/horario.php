<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title>Xintra Elephant</title>
  <meta name="Description" content="Tailwind Responsive Admin Web Dashboard HTML5 Template">
  <meta name="Author" content="amvsoft.tech Technologies Private Limited">
  <meta name="keywords" content="tailwind template,tailwind dashboard,tailwind,tailwind admin template,dashboard,tailwind css templates,html dashboard template,tailwind dashboard template,dashboard tailwind,admin,html css templates,html dashboard,html css javascript templates,dashboard tailwind template,tailwind css dashboard">
  <script src="./assets/js/main.js"></script> 
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/libs/flatpickr/flatpickr.min.css">
  <link rel="stylesheet" href="./assets/libs/@simonwep/pickr/themes/nano.min.css">
  <link rel="stylesheet" href="./assets/libs/choices.js/public/assets/styles/choices.min.css">
  <link rel="stylesheet" href="./assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css">
  <link rel="stylesheet" href="./assets/libs/tabulator-tables/css/tabulator.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css"/>
  <meta http-equiv="imagetoolbar" content="no">
</head>
<body>
<?php include ROOT . '/layout/init.php'; ?>
<div class="page">
  <?php include ROOT . '/layout/header.php'; ?>
  <?php include ROOT . '/layout/sidebar.php'; ?>
  <main class="main-content app-content">
    <div class="container-fluid">
      <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
        <div>
          <ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li><li class="breadcrumb-item active">Horario</li></ol>
          <h1 class="page-title font-medium text-lg mb-0">Horario semanal</h1>
        </div>
        <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder" onclick="window.location.href='usuarios.php'">Volver</button>
      </div>
      <div class="box">
        <div class="box-header"><h5 class="box-title" id="employee-name">Cargando usuario...</h5></div>
        <div class="box-body">
          <p class="text-textmuted dark:text-textmuted/50 mb-4">Configura el horario recurrente para control de asistencia.</p>
          <form id="schedule-form">
            <div class="overflow-x-auto">
              <table class="table whitespace-nowrap">
                <thead><tr><th>Día</th><th>Trabaja</th><th>Entrada</th><th>Salida</th></tr></thead>
                <tbody id="schedule-rows"></tbody>
              </table>
            </div>
            <div class="flex justify-end mt-4"><button class="ti-btn ti-btn-primary" type="submit">Guardar horario</button></div>
          </form>
        </div>
      </div>
    </div>
  </main>
  <?php include ROOT . '/layout/footer.php'; ?>
</div>

<script src="./assets/js/switch.js"></script>
<script src="./assets/libs/@popperjs/core/umd/popper.min.js"></script>
<script src="./assets/libs/preline/preline.js"></script>
<script src="./assets/js/defaultmenu.min.js"> </script>
<script src="./assets/libs/node-waves/waves.min.js"></script>
<script src="./assets/js/sticky.js"></script>
<script src="./assets/libs/simplebar/simplebar.min.js"></script>
<script src="./assets/js/simplebar.js"></script>
<script src="./assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js"></script>
<script src="./assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
<script src="./assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="./assets/js/custom-switcher.min.js"></script>
<script src="./assets/libs/tabulator-tables/js/tabulator.min.js"></script>
<script src="./assets/js/form-validation.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script src="./assets/js/custom.js"></script>

<script>
(() => {
  const hash = new URLSearchParams(location.search).get('hash');
  const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
  const rows = document.querySelector('#schedule-rows');
  const values = new Map();

  days.forEach((day, index) => {
    values.set(index + 1, { dia_semana: index + 1, activo: index < 6 ? 1 : 0, hora_inicio: index < 5 ? '09:00' : index === 5 ? '09:00' : '', hora_fin: index < 5 ? '20:00' : index === 5 ? '13:00' : '' });
  });

  days.forEach((day, index) => {
    const value = values.get(index + 1);
    rows.insertAdjacentHTML('beforeend', `<tr>
      <td class="font-medium">${day}</td>
      <td><input type="checkbox" class="schedule-active ti-form-checkbox" data-day="${index + 1}" ${value.activo ? 'checked' : ''}></td>
      <td><input type="time" class="form-control schedule-start" data-day="${index + 1}" value="${value.hora_inicio}"></td>
      <td><input type="time" class="form-control schedule-end" data-day="${index + 1}" value="${value.hora_fin}"></td>
    </tr>`);
  });

  const setDayState = (day) => {
    const active = document.querySelector(`.schedule-active[data-day="${day}"]`).checked;
    document.querySelector(`.schedule-start[data-day="${day}"]`).disabled = !active;
    document.querySelector(`.schedule-end[data-day="${day}"]`).disabled = !active;
  };
  document.querySelectorAll('.schedule-active').forEach((input) => {
    input.addEventListener('change', () => setDayState(input.dataset.day));
    setDayState(input.dataset.day);
  });

  if (!hash) return alertify.error('Falta el usuario.');
  fetch(`controller/horario.php?hash=${encodeURIComponent(hash)}`)
    .then((response) => response.json())
    .then((json) => {
      if (!json.ok) throw new Error(json.message || 'Usuario no encontrado.');
      document.querySelector('#employee-name').textContent = `Horario de ${json.nombre}`;
      (json.data || []).forEach((item) => {
        if (!item.dia_semana) return;
        const active = document.querySelector(`.schedule-active[data-day="${item.dia_semana}"]`);
        const start = document.querySelector(`.schedule-start[data-day="${item.dia_semana}"]`);
        const end = document.querySelector(`.schedule-end[data-day="${item.dia_semana}"]`);
        active.checked = Number(item.activo) === 1;
        start.value = item.hora_inicio || '';
        end.value = item.hora_fin || '';
        setDayState(item.dia_semana);
      });
    })
    .catch((error) => alertify.error(error.message));

  document.querySelector('#schedule-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const horarios = days.map((_, index) => {
      const day = index + 1;
      return {
        dia_semana: day,
        activo: document.querySelector(`.schedule-active[data-day="${day}"]`).checked ? 1 : 0,
        hora_inicio: document.querySelector(`.schedule-start[data-day="${day}"]`).value,
        hora_fin: document.querySelector(`.schedule-end[data-day="${day}"]`).value,
      };
    });
    const body = new URLSearchParams({ hash, horarios: JSON.stringify(horarios) });
    fetch('controller/horario.php', { method: 'POST', body })
      .then((response) => response.json())
      .then((json) => json.ok ? alertify.success(json.message) : alertify.error(json.message))
      .catch(() => alertify.error('No se pudo guardar el horario.'));
  });
})();
</script>
</body>
</html>
