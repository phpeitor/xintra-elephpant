<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
<!doctype html>
<html lang="es" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title>Xintra Elephant</title>
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

    <div class="main-content app-content">
      <div class="container-fluid">
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
          <div>
            <nav aria-label="nav"><ol class="breadcrumb mb-1"><li class="breadcrumb-item">Catálogo</li><li class="breadcrumb-item active" aria-current="page">Registrar</li></ol></nav>
            <h1 class="page-title font-medium text-lg mb-0">Nueva Categoría</h1>
          </div>
          <div class="btn-list"><button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light" onclick="window.location.href='categorias.php'"><i class="ri-reply-line"></i></button></div>
        </div>

        <div class="col-span-12">
          <div class="box">
            <div class="box-header">
              <h5 class="box-title">Data Validation</h5>
              <div id="estadoToggle" class="toggle toggle-sm on mb-0"><span></span></div>
              <input type="hidden" name="estado" id="estadoInput" value="1">
            </div>
            <div class="box-body">
              <form class="ti-custom-validation-category" novalidate>
                <div class="grid lg:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="ti-form-label">Tipo</label>
                    <select id="grupo" name="tpo" class="ti-form-select rounded-sm" data-rules="required">
                      <option value="">Seleccione</option>
                      <option value="PRODUCTO">PRODUCTO</option>
                      <option value="SERVICIO">SERVICIO</option>
                    </select>
                    <span class="text-red-500 text-xs hidden" data-error-for="grupo"></span>
                  </div>
                  <div class="space-y-2">
                    <label class="ti-form-label">Nombre</label>
                    <input id="nombre" name="nombre" type="text" class="ti-form-input rounded-sm" placeholder="Ej. Cabello" data-rules="required|min:2|max:50">
                    <span class="text-red-500 text-xs hidden" data-error-for="nombre"></span>
                  </div>
                </div>
                <div class="my-5">
                  <button type="submit" class="ti-btn ti-btn-primary ti-custom-validate-btn">Enviar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  <?php include ROOT . '/layout/footer.php'; ?>
  </div>

  <?php include ROOT . '/layout/scroll.php'; ?>

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
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <script src="./assets/js/form-validation.js"></script>
  <script src="./assets/js/custom.js"></script>
</body>
</html>
