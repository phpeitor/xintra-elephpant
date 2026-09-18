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
          <ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="usuarios.php">Personal</a></li><li class="breadcrumb-item active">Asistencia</li></ol>
          <h1 class="page-title font-medium text-lg mb-0"></h1>
        </div>
        <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder" id="back-to-users">Volver</button>
      </div>
      <div class="box">
        <div class="box-header"><h5 class="box-title" id="employee-name">Cargando usuario...</h5></div>
        
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
</body>
</html>
