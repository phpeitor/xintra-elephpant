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
  <title>Sucursales | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/sucursal.css?v=3" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/libs/tabulator-tables/css/tabulator.min.css">
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
            <ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="usuarios.php">Administración</a></li><li class="breadcrumb-item active">Sucursales</li></ol>
            <h1 class="page-title font-medium text-lg mb-0">Sucursales</h1>
          </div>
          <button id="btnNuevaSucursal" type="button" class="ti-btn ti-btn-primary !border-0">
            <i class="ri-add-line me-1" aria-hidden="true"></i>Nueva sucursal
          </button>
        </div>

        <section class="box" aria-labelledby="sucursales-heading">
          <div class="box-header flex items-center justify-between">
            <h2 id="sucursales-heading" class="box-title">Listado de sucursales</h2>
            <button id="btnRecargarSucursales" type="button" class="ti-btn ti-btn-light ti-btn-sm" aria-label="Recargar listado de sucursales">
              <i id="iconRecargarSucursales" class="ri-refresh-line me-1" aria-hidden="true"></i>
              <span id="textoRecargarSucursales">Recargar listado</span>
            </button>
          </div>
          <div class="box-body">
            <div id="sucursales-error" class="hidden text-danger mb-3" role="alert"></div>
            <div class="overflow-x-auto table-bordered">
              <div id="tabla-sucursales" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid"></div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <div id="modal-sucursal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-sucursal-titulo">
      <div class="sucursal-modal-dialog sucursal-modal-fall hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-xl sm:w-full m-3 sm:mx-auto">
        <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
          <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-white/10">
            <h6 id="modal-sucursal-titulo" class="font-semibold text-defaulttextcolor dark:text-white">Nueva sucursal</h6>
            <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-sucursal" aria-label="Cerrar">
              <i class="ri-close-line" aria-hidden="true"></i>
            </button>
          </div>
          <form id="form-sucursal" class="p-4 space-y-4">
            <input type="hidden" id="sucursal-id" name="id">
            <div class="grid sm:grid-cols-2 gap-4">
              <div class="space-y-2 sm:col-span-2">
                <label for="sucursal-nombre" class="ti-form-label">Nombre <span aria-hidden="true">*</span></label>
                <input id="sucursal-nombre" name="nombre" class="ti-form-input rounded-sm" type="text" maxlength="100" required autocomplete="organization">
              </div>
              <div class="space-y-2">
                <label for="sucursal-distrito" class="ti-form-label">Distrito</label>
                <input id="sucursal-distrito" name="distrito" class="ti-form-input rounded-sm" type="text" maxlength="200">
              </div>
              <div class="space-y-2">
                <label for="sucursal-departamento" class="ti-form-label">Departamento</label>
                <input id="sucursal-departamento" name="departamento" class="ti-form-input rounded-sm" type="text" maxlength="200">
              </div>
              <div class="space-y-2 sm:col-span-2">
                <label for="sucursal-direccion" class="ti-form-label">Dirección</label>
                <input id="sucursal-direccion" name="direccion" class="ti-form-input rounded-sm" type="text" maxlength="200" autocomplete="street-address">
              </div>
              <div class="space-y-2">
                <label for="sucursal-telefono" class="ti-form-label">Teléfono</label>
                <input id="sucursal-telefono" name="telefono" class="ti-form-input rounded-sm" type="tel" maxlength="20" autocomplete="tel">
              </div>
              <div class="space-y-2">
                <label for="sucursal-estado" class="ti-form-label">Estado</label>
                <select id="sucursal-estado" name="estado" class="ti-form-select rounded-sm">
                  <option value="1">Activa</option>
                  <option value="0">Inactiva</option>
                </select>
              </div>
            </div>
            <p id="sucursal-form-error" class="hidden text-danger text-sm" role="alert"></p>
            <div class="flex justify-end gap-2 border-t border-defaultborder dark:border-white/10 pt-4">
              <button type="button" class="ti-btn ti-btn-light" data-hs-overlay="#modal-sucursal">Cancelar</button>
              <button id="btnGuardarSucursal" type="submit" class="ti-btn ti-btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <button id="abrir-modal-sucursal" class="hidden" type="button" data-hs-overlay="#modal-sucursal" aria-hidden="true" tabindex="-1"></button>

    <div id="modal-cuota-sucursal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-cuota-titulo">
      <div class="sucursal-modal-dialog sucursal-modal-fall hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-xl sm:w-full m-3 sm:mx-auto">
        <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
          <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-white/10">
            <div>
              <h6 id="modal-cuota-titulo" class="font-semibold text-defaulttextcolor dark:text-white">Aumentar cuota de tickets</h6>
              <p id="cuota-sucursal-nombre" class="text-sm text-textmuted mt-1"></p>
            </div>
            <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-cuota-sucursal" aria-label="Cerrar">
              <i class="ri-close-line" aria-hidden="true"></i>
            </button>
          </div>
          <form id="form-cuota-sucursal" class="p-4 space-y-4">
            <input type="hidden" id="cuota-sucursal-id" name="id_sucursal">
            <div class="grid grid-cols-2 gap-3">
              <div class="rounded-lg border border-defaultborder p-3">
                <p class="text-xs text-textmuted">Tickets usados</p>
                <p id="cuota-tickets-usados" class="text-lg font-semibold">0</p>
              </div>
              <div class="rounded-lg border border-defaultborder p-3">
                <p class="text-xs text-textmuted">Cuota actual</p>
                <p id="cuota-actual" class="text-lg font-semibold">0</p>
              </div>
            </div>
            <div class="space-y-2">
              <label for="cuota-incremento" class="ti-form-label">Tickets adicionales <span aria-hidden="true">*</span></label>
              <input id="cuota-incremento" name="incremento" class="ti-form-input rounded-sm" type="number" min="1" step="1" required inputmode="numeric" placeholder="Ej. 500">
              <p class="text-xs text-textmuted">La cantidad se sumará a la cuota vigente.</p>
            </div>
            <div class="space-y-2">
              <label for="cuota-nueva" class="ti-form-label">Nueva cuota total</label>
              <input id="cuota-nueva" class="ti-form-input rounded-sm bg-light" type="text" readonly value="0">
            </div>
            <div class="space-y-2">
              <label for="cuota-motivo" class="ti-form-label">Motivo (opcional)</label>
              <input id="cuota-motivo" name="motivo" class="ti-form-input rounded-sm" type="text" maxlength="250" placeholder="Ej. ampliación de tickets">
            </div>
            <div class="border-t border-defaultborder pt-4">
              <div class="flex items-center justify-between mb-2">
                <h6 class="font-medium">Historial de aumentos</h6>
                <span id="cuota-historial-estado" class="text-xs text-textmuted" aria-live="polite"></span>
              </div>
              <ol id="cuota-historial-lista" class="space-y-2 max-h-48 overflow-y-auto" aria-label="Historial de cambios de cuota"></ol>
            </div>
            <p id="cuota-form-error" class="hidden text-danger text-sm" role="alert"></p>
            <div class="flex justify-end gap-2 border-t border-defaultborder pt-4">
              <button type="button" class="ti-btn ti-btn-light" data-hs-overlay="#modal-cuota-sucursal">Cancelar</button>
              <button id="btnAumentarCuota" type="submit" class="ti-btn ti-btn-primary">Aumentar cuota</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <button id="abrir-modal-cuota" class="hidden" type="button" data-hs-overlay="#modal-cuota-sucursal" aria-hidden="true" tabindex="-1"></button>

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
  <script src="./assets/js/xintra-tooltip.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <script src="./assets/js/sucursal.js?v=2"></script>
</body>
</html>
