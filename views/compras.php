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
  <title>Compras | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/compras.css?v=1" rel="stylesheet">
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
            <ol class="breadcrumb mb-1"><li class="breadcrumb-item">Compras</li><li class="breadcrumb-item active" aria-current="page">Registro de compras</li></ol>
            <h1 class="page-title font-medium text-lg mb-0">Registro de compras</h1>
            <p class="text-sm text-textmuted mt-1">Registra comprobantes recibidos y sus productos.</p>
          </div>
          <button id="btnNuevaCompra" type="button" class="ti-btn ti-btn-primary !border-0"><i class="ri-add-line me-1" aria-hidden="true"></i>Registrar compra</button>
        </div>

        <section class="box" aria-labelledby="compras-heading">
          <div class="box-header flex items-center justify-between">
            <h2 id="compras-heading" class="box-title">Historial de compras</h2>
            <button id="btnRecargarCompras" type="button" class="ti-btn ti-btn-light ti-btn-sm"><i id="iconRecargarCompras" class="ri-refresh-line me-1" aria-hidden="true"></i><span id="textoRecargarCompras">Actualizar</span></button>
          </div>
          <div class="box-body">
            <p class="rounded-lg border border-primary/15 bg-primary/5 px-3 py-2 text-sm text-textmuted mb-3">Las compras registradas quedan pendientes y no modifican el inventario todavía.</p>
            <div id="compras-error" class="hidden text-danger mb-3" role="alert"></div>
            <div class="overflow-x-auto table-bordered">
              <div id="tabla-compras" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid"></div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <div id="modal-compra" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-compra-titulo">
      <div class="compra-modal-dialog hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all m-3 sm:mx-auto">
        <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
          <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-white/10">
            <div>
              <h2 id="modal-compra-titulo" class="font-semibold text-defaulttextcolor dark:text-white">Registrar compra</h2>
              <p class="text-xs text-textmuted mt-1">El documento queda pendiente de recepción en inventario.</p>
            </div>
            <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-compra" aria-label="Cerrar"><i class="ri-close-line" aria-hidden="true"></i></button>
          </div>
          <form id="form-compra" class="p-4 space-y-4">
            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
              <div class="space-y-2 md:col-span-2">
                <label for="compra-proveedor" class="ti-form-label">Proveedor <span aria-hidden="true">*</span></label>
                <select id="compra-proveedor" name="proveedor_id" class="ti-form-select rounded-sm" required>
                  <option value="">Selecciona proveedor</option>
                </select>
              </div>
              <div class="space-y-2">
                <label for="compra-tipo" class="ti-form-label">Comprobante</label>
                <select id="compra-tipo" name="tipo_comprobante" class="ti-form-select rounded-sm" required>
                  <option value="FACTURA">Factura</option>
                  <option value="BOLETA">Boleta</option>
                  <option value="TICKET">Ticket</option>
                  <option value="OTRO">Otro</option>
                </select>
              </div>
              <div class="space-y-2">
                <label for="compra-fecha" class="ti-form-label">Fecha</label>
                <input id="compra-fecha" name="fecha" type="date" class="ti-form-input rounded-sm" required>
              </div>
              <div class="space-y-2">
                <label for="compra-serie" class="ti-form-label">Serie</label>
                <input id="compra-serie" name="serie" type="text" maxlength="20" class="ti-form-input rounded-sm" required placeholder="F001">
              </div>
              <div class="space-y-2">
                <label for="compra-numero" class="ti-form-label">Número</label>
                <input id="compra-numero" name="numero" type="text" maxlength="20" class="ti-form-input rounded-sm" required placeholder="00001234">
              </div>
            </div>

            <section class="rounded-xl border border-defaultborder dark:border-white/10 overflow-hidden" aria-labelledby="compra-detalle-heading">
              <div class="flex flex-wrap items-center justify-between gap-2 p-3 border-b border-defaultborder dark:border-white/10">
                <div>
                  <h3 id="compra-detalle-heading" class="font-semibold">Detalle de productos</h3>
                  <p class="text-xs text-textmuted">Ingresa el costo unitario antes del IGV.</p>
                </div>
                <button id="btnAgregarLineaCompra" type="button" class="ti-btn ti-btn-light ti-btn-sm"><i class="ri-add-line me-1" aria-hidden="true"></i>Agregar producto</button>
              </div>
              <div class="overflow-x-auto">
                <table class="compra-lines-table w-full min-w-[900px] text-sm">
                  <thead><tr><th>Producto</th><th>Cantidad</th><th>Costo unitario</th><th>IGV</th><th>Subtotal</th><th>Total</th><th><span class="sr-only">Quitar</span></th></tr></thead>
                  <tbody id="compra-lineas"></tbody>
                </table>
              </div>
              <div class="grid sm:grid-cols-3 gap-3 p-3 border-t border-defaultborder dark:border-white/10 bg-light/30">
                <div><span class="text-xs text-textmuted">Subtotal</span><strong id="compra-subtotal" class="block">S/ 0.00</strong></div>
                <div><span class="text-xs text-textmuted">IGV</span><strong id="compra-igv" class="block">S/ 0.00</strong></div>
                <div><span class="text-xs text-textmuted">Total</span><strong id="compra-total" class="block text-lg text-primary">S/ 0.00</strong></div>
              </div>
            </section>
            <div class="space-y-2">
              <label for="compra-observaciones" class="ti-form-label">Observaciones</label>
              <textarea id="compra-observaciones" name="observaciones" maxlength="500" rows="2" class="ti-form-input rounded-sm" placeholder="Notas internas opcionales"></textarea>
            </div>
            <p id="compra-form-error" class="hidden text-danger text-sm" role="alert"></p>
            <div class="flex justify-end gap-2 border-t border-defaultborder dark:border-white/10 pt-4">
              <button type="button" class="ti-btn ti-btn-light" data-hs-overlay="#modal-compra">Cancelar</button>
              <button id="btnGuardarCompra" type="submit" class="ti-btn ti-btn-primary">Guardar compra pendiente</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <button id="abrir-modal-compra" class="hidden" type="button" data-hs-overlay="#modal-compra" aria-hidden="true" tabindex="-1"></button>

    <div id="modal-detalle-compra" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="detalle-compra-titulo">
      <div class="compra-modal-dialog compra-detail-dialog hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all m-3 sm:mx-auto">
        <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
          <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-white/10">
            <div><h2 id="detalle-compra-titulo" class="font-semibold">Detalle de compra</h2><p id="detalle-compra-resumen" class="text-xs text-textmuted mt-1"></p></div>
            <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-detalle-compra" aria-label="Cerrar"><i class="ri-close-line" aria-hidden="true"></i></button>
          </div>
          <div class="p-4 space-y-4">
            <div id="detalle-compra-proveedor" class="rounded-lg bg-light p-3 text-sm"></div>
            <div class="overflow-x-auto table-bordered"><table class="compra-lines-table w-full min-w-[700px] text-sm"><thead><tr><th>Producto</th><th>Cantidad</th><th>Costo unitario</th><th>Subtotal</th><th>IGV</th><th>Total</th></tr></thead><tbody id="detalle-compra-lineas"></tbody></table></div>
            <p id="detalle-compra-observaciones" class="text-sm text-textmuted"></p>
            <div class="grid grid-cols-3 gap-3 text-right"><div><span class="text-xs text-textmuted">Subtotal</span><strong id="detalle-compra-subtotal" class="block"></strong></div><div><span class="text-xs text-textmuted">IGV</span><strong id="detalle-compra-igv" class="block"></strong></div><div><span class="text-xs text-textmuted">Total</span><strong id="detalle-compra-total" class="block text-primary"></strong></div></div>
          </div>
        </div>
      </div>
    </div>
    <button id="abrir-modal-detalle-compra" class="hidden" type="button" data-hs-overlay="#modal-detalle-compra" aria-hidden="true" tabindex="-1"></button>

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
  <script src="./assets/js/compras.js?v=1"></script>
</body>
</html>
