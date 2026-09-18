<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
<!doctype html>
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
  <link href="./assets/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet">
  <link href="./assets/css/asistencia.css?v=3" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css">
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
          <ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="usuarios.php">Personal</a></li><li class="breadcrumb-item active">Asistencia</li></ol>
          <h1 class="page-title font-medium text-lg mb-0">Control de asistencia</h1>
        </div>
      </div>
      <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 xl:col-span-9">
          <div class="box">
            <div class="box-header flex items-center justify-between flex-wrap gap-3">
              <div>
                <h5 class="box-title mb-1">Calendario de asistencia</h5>
                <p class="text-textmuted dark:text-textmuted/50 text-xs mb-0">Registra y consulta las entradas y salidas del personal.</p>
              </div>
              <div class="attendance-legend"><span><i class="entry-dot"></i> Entrada</span><span><i class="exit-dot"></i> Salida</span></div>
            </div>
            <div class="box-body">
              <div class="attendance-filters mb-5">
                <div class="attendance-filter">
                  <label class="ti-form-label" for="attendance-user">Usuario</label>
                  <select id="attendance-user" class="ti-form-select rounded-sm !py-2 !px-3">
                    <option value="">Todos los usuarios</option>
                  </select>
                </div>
                <div class="attendance-actions">
                  <button type="button" id="mark-entry" class="ti-btn ti-btn-success" disabled><i class="ti ti-login me-1"></i>Entrada</button>
                  <button type="button" id="mark-exit" class="ti-btn ti-btn-danger" disabled><i class="ti ti-logout me-1"></i>Salida</button>
                </div>
              </div>
              <div id="attendance-empty" class="hidden text-center text-textmuted dark:text-textmuted/50 py-5">No hay registros de asistencia para este periodo.</div>
              <div id="attendance-error" class="hidden text-center text-danger py-5" role="alert"></div>
              <div id="attendance-calendar" aria-label="Calendario de asistencia"></div>
            </div>
          </div>
        </div>
        <div class="col-span-12 xl:col-span-3">
          <div class="box">
            <div class="box-header flex items-center justify-between"><h5 class="box-title">All events</h5><span class="badge bg-primary/10 text-primary">Entrada / Salida</span></div>
            <div class="box-body"><div class="attendance-event-card entry-card"><i class="ti ti-login"></i><span>Registrar entrada</span></div><div class="attendance-event-card exit-card"><i class="ti ti-logout"></i><span>Registrar salida</span></div><p class="text-textmuted dark:text-textmuted/50 text-xs mt-4 mb-0">Selecciona un usuario para habilitar las acciones.</p></div>
          </div>
          <div class="box">
            <div class="box-header"><h5 class="box-title">Activity</h5></div>
            <div class="box-body"><ul id="attendance-activity" class="attendance-activity"></ul></div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php include ROOT . '/layout/footer.php'; ?>
</div>
<script src="./assets/libs/@popperjs/core/umd/popper.min.js"></script>
<script src="./assets/libs/preline/preline.js"></script>
<script src="./assets/js/defaultmenu.min.js"></script>
<script src="./assets/libs/node-waves/waves.min.js"></script>
<script src="./assets/js/sticky.js"></script>
<script src="./assets/libs/simplebar/simplebar.min.js"></script>
<script src="./assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js"></script>
<script src="./assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
<script src="./assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="./assets/js/simplebar.js"></script>
<script src="./assets/js/custom-switcher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script src="./assets/js/custom.js"></script>
<script src="./assets/js/asistencia.js?v=5"></script>
</body>
</html>
