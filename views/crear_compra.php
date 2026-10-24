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
  <title>Nueva compra | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/compras.css?v=9" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/libs/flatpickr/flatpickr.min.css">
  <link rel="stylesheet" href="./assets/libs/choices.js/public/assets/styles/choices.min.css">
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
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 mb-4">
          <div>
            <ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="compras.php">Compras</a></li><li class="breadcrumb-item active" aria-current="page">Nueva compra</li></ol>
            <h1 class="page-title font-medium text-lg mb-0">Registrar compra</h1>
            <p class="text-sm text-textmuted mt-1">Registra el comprobante recibido y los productos comprados.</p>
          </div>
          <a href="compras.php" class="ti-btn ti-btn-light"><i class="ri-arrow-left-line me-1" aria-hidden="true"></i>Volver al historial</a>
        </div>

        <form id="form-compra" class="grid grid-cols-12 gap-6" novalidate>
          <div class="col-span-12 xl:col-span-8 space-y-6">
            <section class="box" aria-labelledby="compra-info-heading">
              <div class="box-header"><h2 id="compra-info-heading" class="box-title">Información del comprobante</h2></div>
              <div class="box-body">
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                  <div class="space-y-2 sm:col-span-2 xl:col-span-1">
                    <label for="compra-proveedor" class="ti-form-label">Proveedor <span class="text-danger">*</span></label>
                    <select id="compra-proveedor" name="proveedor_id" class="ti-form-select rounded-sm" required><option value="">Selecciona proveedor</option></select>
                  </div>
                  <div class="space-y-2">
                    <label for="compra-tipo" class="ti-form-label">Tipo de comprobante</label>
                    <select id="compra-tipo" name="tipo_comprobante" class="ti-form-select rounded-sm" required>
                      <option value="FACTURA">Factura</option><option value="BOLETA">Boleta</option><option value="TICKET">Ticket</option><option value="OTRO">Otro</option>
                    </select>
                  </div>
                  <div class="space-y-2">
                    <label for="date" class="ti-form-label">Fecha de emisión</label>
                    <input id="date" name="fecha" type="text" class="ti-form-input rounded-sm" readonly required autocomplete="off" placeholder="Selecciona la fecha">
                  </div>
                  <div class="space-y-2">
                    <label for="compra-serie" class="ti-form-label">Serie del comprobante</label>
                    <input id="compra-serie" name="serie" type="text" maxlength="20" class="ti-form-input rounded-sm" required placeholder="F001">
                  </div>
                  <div class="space-y-2">
                    <label for="compra-numero" class="ti-form-label">Número del comprobante</label>
                    <input id="compra-numero" name="numero" type="text" maxlength="20" class="ti-form-input rounded-sm" required placeholder="00001234">
                  </div>
                </div>
                <div class="mt-4 flex items-start gap-2 rounded-lg border border-info/20 bg-info/5 p-3 text-xs text-textmuted">
                  <i class="ri-information-line text-info mt-0.5" aria-hidden="true"></i>
                  <p>La serie y el número corresponden al comprobante que emitió el proveedor. Este registro no usa la numeración de tus comprobantes de venta.</p>
                </div>
              </div>
            </section>

            <section class="box" aria-labelledby="compra-productos-heading">
              <div class="box-header flex items-center justify-between flex-wrap gap-2">
                <div><h2 id="compra-productos-heading" class="box-title">Productos de la compra</h2><p class="text-xs text-textmuted mt-1">Ingresa el costo unitario antes del IGV.</p></div>
              </div>
              <div class="box-body p-0">
                <div class="compra-product-picker-row grid sm:grid-cols-[minmax(0,1fr)_auto] gap-3 p-4 border-b border-defaultborder dark:border-white/10">
                  <select id="compra-product-picker" class="ti-form-select rounded-sm" aria-label="Buscar producto para agregar"><option value="">Buscar producto para agregar...</option></select>
                  <button id="btnAgregarLineaCompra" type="button" class="ti-btn ti-btn-primary !border-0"><i class="ri-add-line me-1" aria-hidden="true"></i>Agregar producto</button>
                </div>
                <div class="overflow-x-auto">
                  <table class="compra-lines-table w-full min-w-[850px] text-sm">
                    <thead><tr><th>Producto</th><th>Cantidad</th><th>Costo unitario</th><th>IGV</th><th>Subtotal</th><th>Total</th><th><span class="sr-only">Quitar</span></th></tr></thead>
                    <tbody id="compra-lineas"></tbody>
                  </table>
                  <div id="compra-lineas-vacias" class="hidden py-10 text-center">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary"><i class="ri-shopping-basket-2-line text-xl" aria-hidden="true"></i></span>
                    <p class="font-medium">Aún no agregas productos</p><p class="text-xs text-textmuted mt-1">Busca un producto en el selector y agrégalo a la compra.</p>
                  </div>
                </div>
              </div>
            </section>

            <section class="box" aria-labelledby="compra-observaciones-heading">
              <div class="box-header"><h2 id="compra-observaciones-heading" class="box-title">Notas internas</h2></div>
              <div class="box-body"><textarea id="compra-observaciones" name="observaciones" maxlength="500" rows="3" class="ti-form-input rounded-sm" placeholder="Observaciones opcionales para esta compra"></textarea></div>
            </section>
          </div>

          <aside class="col-span-12 xl:col-span-4">
            <div class="box compra-summary-card xl:sticky xl:top-20">
              <div class="box-header"><h2 class="box-title">Resumen de compra</h2></div>
              <div class="box-body space-y-4">
                <div class="flex items-center justify-between"><span class="text-textmuted">Productos</span><strong id="compra-cantidad-productos">0</strong></div>
                <div class="flex items-center justify-between"><span class="text-textmuted">Subtotal</span><strong id="compra-subtotal">S/ 0.00</strong></div>
                <div class="flex items-center justify-between"><span class="text-textmuted">IGV</span><strong id="compra-igv">S/ 0.00</strong></div>
                <div class="border-t border-dashed border-defaultborder pt-4 flex items-center justify-between"><span class="font-semibold">Total</span><strong id="compra-total" class="text-xl text-primary">S/ 0.00</strong></div>
                <div class="rounded-lg border border-warning/20 bg-warning/5 p-3 text-xs text-textmuted"><i class="ri-time-line text-warning me-1" aria-hidden="true"></i>La compra quedará pendiente. El inventario no cambia con este registro.</div>
                <p id="compra-form-error" class="hidden rounded-lg border border-danger/20 bg-danger/5 p-3 text-sm text-danger" role="alert"></p>
                <div class="grid grid-cols-2 gap-2 pt-2">
                  <a href="compras.php" class="ti-btn ti-btn-light justify-center">Cancelar</a>
                  <button id="btnGuardarCompra" type="submit" class="ti-btn ti-btn-primary justify-center"><i class="ri-save-line me-1" aria-hidden="true"></i>Registrar</button>
                </div>
              </div>
            </div>
          </aside>
        </form>
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
  <script src="./assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="./assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="./assets/js/xintra-tooltip.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <script src="./assets/js/crear_compra.js?v=2"></script>
</body>
</html>
