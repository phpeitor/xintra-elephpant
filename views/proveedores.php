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
  <title>Proveedores | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/proveedores.css?v=1" rel="stylesheet">
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
            <ol class="breadcrumb mb-1"><li class="breadcrumb-item">Compras</li><li class="breadcrumb-item active" aria-current="page">Proveedores</li></ol>
            <h1 class="page-title font-medium text-lg mb-0">Proveedores</h1>
            <p class="text-sm text-textmuted mt-1">Administra los proveedores de esta sucursal.</p>
          </div>
          <button id="btnNuevoProveedor" type="button" class="ti-btn ti-btn-primary !border-0"><i class="ri-add-line me-1" aria-hidden="true"></i>Nuevo proveedor</button>
        </div>

        <section class="box" aria-labelledby="proveedores-heading">
          <div class="box-header flex items-center justify-between">
            <h2 id="proveedores-heading" class="box-title">Directorio de proveedores</h2>
            <button id="btnRecargarProveedores" type="button" class="ti-btn ti-btn-light ti-btn-sm"><i id="iconRecargarProveedores" class="ri-refresh-line me-1" aria-hidden="true"></i><span id="textoRecargarProveedores">Actualizar</span></button>
          </div>
          <div class="box-body">
            <div id="proveedores-error" class="hidden text-danger mb-3" role="alert"></div>
            <div class="overflow-x-auto table-bordered">
              <div id="tabla-proveedores" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid"></div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <div id="modal-proveedor" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-proveedor-titulo">
      <div class="proveedor-modal-dialog hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-xl sm:w-full m-3 sm:mx-auto">
        <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
          <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-white/10">
            <div>
              <h3 id="modal-proveedor-titulo" class="font-semibold text-defaulttextcolor dark:text-white">Nuevo proveedor</h3>
              <p class="text-xs text-textmuted mt-1">Los datos se guardan únicamente para la sucursal activa.</p>
            </div>
            <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-proveedor" aria-label="Cerrar"><i class="ri-close-line" aria-hidden="true"></i></button>
          </div>
          <form id="form-proveedor" class="p-4 space-y-4">
            <input type="hidden" id="proveedor-id" name="id">
            <div class="grid sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="proveedor-tipo-documento" class="ti-form-label">Tipo de documento</label>
                <select id="proveedor-tipo-documento" name="tipo_documento" class="ti-form-select rounded-sm">
                  <option value="RUC">RUC</option>
                  <option value="DNI">DNI</option>
                </select>
              </div>
              <div class="space-y-2">
                <label for="proveedor-documento" class="ti-form-label">Número de documento</label>
                <input id="proveedor-documento" name="documento" type="text" inputmode="numeric" maxlength="11" pattern="[0-9]{11}" required class="ti-form-input rounded-sm" placeholder="11 dígitos">
                <span id="proveedor-documento-status" class="text-xs text-textmuted" role="status" aria-live="polite"></span>
              </div>
              <div class="space-y-2 sm:col-span-2">
                <label for="proveedor-razon-social" class="ti-form-label">Nombre / Razón Social</label>
                <input id="proveedor-razon-social" name="razon_social" type="text" maxlength="150" minlength="2" required class="ti-form-input rounded-sm" autocomplete="organization" placeholder="Razón social o nombre del proveedor">
              </div>
              <div class="space-y-2">
                <label for="proveedor-email" class="ti-form-label">Correo electrónico</label>
                <input id="proveedor-email" name="email" type="email" maxlength="150" class="ti-form-input rounded-sm" autocomplete="email" placeholder="proveedor@empresa.pe">
              </div>
              <div class="space-y-2">
                <label for="proveedor-telefono" class="ti-form-label">Teléfono</label>
                <input id="proveedor-telefono" name="telefono" type="tel" maxlength="20" class="ti-form-input rounded-sm" autocomplete="tel" placeholder="Teléfono de contacto">
              </div>
              <div class="space-y-2 sm:col-span-2">
                <label for="proveedor-direccion" class="ti-form-label">Dirección</label>
                <input id="proveedor-direccion" name="direccion" type="text" maxlength="200" class="ti-form-input rounded-sm" autocomplete="street-address" placeholder="Dirección fiscal o comercial">
              </div>
              <div class="space-y-2">
                <label for="proveedor-estado-toggle" class="ti-form-label">Estado</label>
                <div id="proveedor-estado-toggle" class="toggle toggle-sm on mb-0" role="switch" aria-checked="true" tabindex="0"><span></span></div>
                <input id="proveedor-estado" type="hidden" name="estado" value="1">
              </div>
            </div>
            <p id="proveedor-form-error" class="hidden text-danger text-sm" role="alert"></p>
            <div class="flex justify-end gap-2 border-t border-defaultborder dark:border-white/10 pt-4">
              <button type="button" class="ti-btn ti-btn-light" data-hs-overlay="#modal-proveedor">Cancelar</button>
              <button id="btnGuardarProveedor" type="submit" class="ti-btn ti-btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <button id="abrir-modal-proveedor" class="hidden" type="button" data-hs-overlay="#modal-proveedor" aria-hidden="true" tabindex="-1"></button>

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
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <script src="./assets/js/proveedores.js?v=2"></script>
</body>
</html>
