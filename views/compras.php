<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
<!doctype html>
<html lang="es" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title>Compras | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/compras.css?v=7" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/libs/tabulator-tables/css/tabulator.min.css">
</head>
<body>
  <?php include ROOT . '/layout/init.php'; ?>
  <div class="page">
    <?php include ROOT . '/layout/header.php'; ?>
    <?php include ROOT . '/layout/sidebar.php'; ?>
    <main class="main-content app-content">
      <div class="container-fluid">
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-3 mb-5">
          <div>
            <ol class="breadcrumb mb-1"><li class="breadcrumb-item">Compras</li><li class="breadcrumb-item active" aria-current="page">Facturas de proveedores</li></ol>
            <h1 class="page-title font-medium text-lg mb-0">Compras</h1>
            <p class="text-sm text-textmuted mt-1">Controla comprobantes recibidos, proveedores y productos comprados.</p>
          </div>
          <a href="crear_compra.php" class="ti-btn ti-btn-primary !border-0"><i class="ri-add-line me-1" aria-hidden="true"></i>Registrar Compra</a>
        </div>

        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
          <article class="box compra-kpi-card"><div class="box-body flex items-center gap-3"><span class="compra-kpi-icon bg-primary/10 text-primary"><i class="ri-file-list-3-line" aria-hidden="true"></i></span><div><p class="text-xs text-textmuted">Documentos registrados</p><strong id="compra-kpi-total" class="text-xl">0</strong></div></div></article>
          <article class="box compra-kpi-card"><div class="box-body flex items-center gap-3"><span class="compra-kpi-icon bg-warning/10 text-warning"><i class="ri-time-line" aria-hidden="true"></i></span><div><p class="text-xs text-textmuted">Pendientes de recepción</p><strong id="compra-kpi-pendientes" class="text-xl">0</strong></div></div></article>
          <article class="box compra-kpi-card"><div class="box-body flex items-center gap-3"><span class="compra-kpi-icon bg-success/10 text-success"><i class="ri-money-dollar-circle-line" aria-hidden="true"></i></span><div><p class="text-xs text-textmuted">Total registrado</p><strong id="compra-kpi-monto" class="text-xl">S/ 0.00</strong></div></div></article>
          <article class="box compra-kpi-card"><div class="box-body flex items-center gap-3"><span class="compra-kpi-icon bg-info/10 text-info"><i class="ri-store-2-line" aria-hidden="true"></i></span><div><p class="text-xs text-textmuted">Proveedores activos</p><a href="proveedores.php" class="text-sm font-semibold text-info">Administrar proveedores <i class="ri-arrow-right-up-line" aria-hidden="true"></i></a></div></div></article>
        </div>

        <section class="box" aria-labelledby="compras-heading">
          <div class="box-header flex items-center justify-between flex-wrap gap-3">
            <div><h2 id="compras-heading" class="box-title">Historial de compras</h2><p class="text-xs text-textmuted mt-1">Consulta el documento recibido y su detalle.</p></div>
            <button id="btnRecargarCompras" type="button" class="ti-btn ti-btn-light ti-btn-sm"><i id="iconRecargarCompras" class="ri-refresh-line me-1" aria-hidden="true"></i><span id="textoRecargarCompras">Actualizar</span></button>
          </div>
          <div class="box-body">
            <div class="flex flex-col sm:flex-row gap-3 mb-4">
              <div class="relative flex-1">
                <i class="ri-search-line absolute start-3 top-1/2 -translate-y-1/2 text-textmuted" aria-hidden="true"></i>
                <input id="compras-search" type="search" class="ti-form-input rounded-sm ps-9" placeholder="Buscar por proveedor, comprobante o número..." aria-label="Buscar compras">
              </div>
              <select id="compras-estado-filter" class="ti-form-select rounded-sm sm:max-w-56" aria-label="Filtrar por estado">
                <option value="">Todos los estados</option>
                <option value="PENDIENTE">Pendiente de recepción</option>
                <option value="ANULADA">Anulada</option>
              </select>
            </div>
            <p id="compras-error" class="hidden rounded-lg border border-danger/20 bg-danger/5 p-3 text-sm text-danger mb-3" role="alert"></p>
            <div class="overflow-x-auto table-bordered rounded-lg">
              <div id="tabla-compras" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid"></div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs text-textmuted"><i class="ri-information-line text-info" aria-hidden="true"></i>Las compras registradas quedan pendientes y no modifican el inventario todavía.</div>
          </div>
        </section>
      </div>
    </main>
    <?php include ROOT . '/layout/footer.php'; ?>
  </div>
  <?php include ROOT . '/layout/scroll.php'; ?>
  <script src="./assets/js/switch.js"></script>
  <script src="./assets/libs/@popperjs/core/umd/popper.min.js"></script>
  <script src="./assets/libs/preline/preline.js"></script>
  <script src="./assets/js/defaultmenu.min.js"></script>
  <script src="./assets/libs/node-waves/waves.min.js"></script>
  <script src="./assets/js/sticky.js"></script>
  <script src="./assets/libs/simplebar/simplebar.min.js"></script>
  <script src="./assets/js/simplebar.js"></script>
  <script src="./assets/libs/tabulator-tables/js/tabulator.min.js"></script>
  <script src="./assets/js/compras.js?v=2"></script>
</body>
</html>
