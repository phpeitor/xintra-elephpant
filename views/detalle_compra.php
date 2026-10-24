<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
require_once ROOT . '/model/compra.php';

$idCompra = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$compra = $idCompra && $idCompra > 0 ? (new Compra())->obtener((int)$idCompra) : null;
$esc = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$dinero = static fn($value): string => 'S/ ' . number_format((float)$value, 2, '.', ',');
if (!$compra) {
    http_response_code(404);
}
?>
<!doctype html>
<html lang="es" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title><?= $compra ? 'Compra ' . $esc($compra['serie'] . '-' . $compra['numero']) : 'Compra no encontrada' ?> | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/compras.css?v=7" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
</head>
<body>
  <?php include ROOT . '/layout/init.php'; ?>
  <div class="page">
    <?php include ROOT . '/layout/header.php'; ?>
    <?php include ROOT . '/layout/sidebar.php'; ?>
    <main class="main-content app-content">
      <div class="container-fluid">
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-3 mb-4">
          <div>
            <ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="compras.php">Compras</a></li><li class="breadcrumb-item active" aria-current="page">Detalle</li></ol>
            <h1 class="page-title font-medium text-lg mb-0">Detalle de compra</h1>
            <p class="text-sm text-textmuted mt-1">Revisa el comprobante recibido y sus productos.</p>
          </div>
          <div class="flex gap-2">
            <a href="compras.php" class="ti-btn ti-btn-light"><i class="ri-arrow-left-line me-1" aria-hidden="true"></i>Historial</a>
            <?php if ($compra): ?><button id="btnImprimirCompra" type="button" class="ti-btn ti-btn-primary !border-0"><i class="ri-printer-line me-1" aria-hidden="true"></i>Imprimir</button><?php endif; ?>
          </div>
        </div>

        <?php if (!$compra): ?>
          <section class="box"><div class="box-body py-12 text-center"><span class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-warning/10 text-warning"><i class="ri-file-warning-line text-2xl" aria-hidden="true"></i></span><h2 class="font-semibold">Compra no encontrada</h2><p class="text-sm text-textmuted mt-1">No existe en la sucursal activa o el identificador no es válido.</p></div></section>
        <?php else: ?>
          <section class="box compra-details-card">
            <div class="box-header flex items-start justify-between gap-4 flex-wrap">
              <div class="flex items-start gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary"><i class="ri-file-list-3-line text-2xl" aria-hidden="true"></i></span>
                <div>
                  <p class="text-xs uppercase tracking-wide text-textmuted">Comprobante del proveedor</p>
                  <h2 class="text-lg font-semibold mt-1"><?= $esc($compra['tipo_comprobante']) ?> <span class="text-primary"><?= $esc($compra['serie']) ?>-<?= $esc($compra['numero']) ?></span></h2>
                  <p class="text-sm text-textmuted mt-1">Emitido el <?= $esc($compra['fecha']) ?></p>
                </div>
              </div>
              <span class="badge <?= $compra['estado'] === 'ANULADA' ? 'bg-danger' : 'bg-warning text-dark' ?>"><i class="<?= $compra['estado'] === 'ANULADA' ? 'ri-close-circle-line' : 'ri-time-line' ?> me-1" aria-hidden="true"></i><?= $compra['estado'] === 'ANULADA' ? 'Anulada' : 'Pendiente de ingreso' ?></span>
            </div>
            <div class="box-body">
              <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="rounded-xl border border-defaultborder dark:border-white/10 p-4">
                  <p class="text-xs font-semibold uppercase tracking-wide text-textmuted mb-2">Proveedor</p>
                  <h3 class="font-semibold"><?= $esc($compra['proveedor']) ?></h3>
                  <p class="text-sm text-textmuted mt-1"><?= $esc($compra['tipo_documento']) ?>: <?= $esc($compra['documento']) ?></p>
                </div>
                <div class="rounded-xl border border-defaultborder dark:border-white/10 p-4">
                  <p class="text-xs font-semibold uppercase tracking-wide text-textmuted mb-2">Estado de recepción</p>
                  <p class="font-medium"><?= $compra['estado'] === 'ANULADA' ? 'Esta compra está anulada.' : 'Pendiente de ingreso al inventario.' ?></p>
                  <p class="text-xs text-textmuted mt-1">El registro por sí solo no modifica las existencias.</p>
                </div>
              </div>

              <div class="overflow-x-auto table-bordered rounded-lg">
                <table class="compra-lines-table w-full min-w-[760px] text-sm">
                  <thead><tr><th>Producto</th><th>Cantidad</th><th>Costo unitario</th><th>IGV</th><th>Subtotal</th><th>Total</th></tr></thead>
                  <tbody>
                    <?php foreach ($compra['items'] as $item): ?>
                      <tr>
                        <td class="font-medium"><?= $esc($item['descripcion']) ?></td>
                        <td><?= $esc($item['cantidad']) ?></td>
                        <td><?= $dinero($item['costo_unitario']) ?></td>
                        <td><?= number_format((float)$item['porcentaje_igv'], 0) ?>% · <?= $dinero($item['igv']) ?></td>
                        <td><?= $dinero($item['subtotal']) ?></td>
                        <td class="font-semibold"><?= $dinero($item['total']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="grid lg:grid-cols-[minmax(0,1fr)_20rem] gap-6 mt-6">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-wide text-textmuted mb-2">Observaciones</p>
                  <p class="text-sm whitespace-pre-line"><?= !empty($compra['observaciones']) ? $esc($compra['observaciones']) : 'Sin observaciones.' ?></p>
                </div>
                <div class="rounded-xl bg-light/60 p-4 space-y-3">
                  <div class="flex justify-between text-sm"><span class="text-textmuted">Subtotal</span><span class="font-medium"><?= $dinero($compra['subtotal']) ?></span></div>
                  <div class="flex justify-between text-sm"><span class="text-textmuted">IGV</span><span class="font-medium"><?= $dinero($compra['igv']) ?></span></div>
                  <div class="border-t border-defaultborder pt-3 flex justify-between"><span class="font-semibold">Total</span><strong class="text-xl text-primary"><?= $dinero($compra['total']) ?></strong></div>
                </div>
              </div>
            </div>
          </section>
        <?php endif; ?>
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
  <script src="./assets/js/detalle_compra.js?v=1"></script>
</body>
</html>
