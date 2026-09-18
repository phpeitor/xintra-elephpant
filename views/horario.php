<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
<!doctype html>
<html lang="es" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Horario del usuario</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link href="./assets/css/horario.css" rel="stylesheet">
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
        <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder" id="back-to-users">Volver</button>
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
<script src="./assets/js/defaultmenu.min.js"></script>
<script src="./assets/libs/node-waves/waves.min.js"></script>
<script src="./assets/js/sticky.js"></script>
<script src="./assets/libs/simplebar/simplebar.min.js"></script>
<script src="./assets/js/simplebar.js"></script>
<script src="./assets/js/custom-switcher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script src="./assets/js/custom.js"></script>
<script src="./assets/js/horario.js?v=2"></script>
</body>
</html>
