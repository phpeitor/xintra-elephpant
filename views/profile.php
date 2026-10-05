<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
require_once ROOT . '/model/usuario.php';

$usuario = new Usuario();
$perfil = $usuario->obtenerPerfil((int)($_SESSION['session_id'] ?? 0));
if (!$perfil) {
    http_response_code(404);
}

$escapar = static fn($valor): string => htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
$nombreCompleto = trim((string)($perfil['nombres'] ?? '') . ' ' . (string)($perfil['apellidos'] ?? ''));
$sexo = match ((int)($perfil['sexo'] ?? -1)) {
    1 => 'Masculino',
    2 => 'Femenino',
    0 => 'Otro',
    default => 'No registrado',
};
$estado = (int)($perfil['estado'] ?? 0) === 1 ? 'Activo' : 'Inactivo';
$ubicacion = trim(implode(' · ', array_filter([
    trim((string)($perfil['distrito'] ?? '')),
    trim((string)($perfil['departamento'] ?? '')),
])));
$fechaRegistro = !empty($perfil['fecha_registro'])
    ? (new DateTimeImmutable((string)$perfil['fecha_registro']))->format('d/m/Y')
    : 'No registrada';
?>
<!doctype html>
<html lang="es" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title>Mi perfil | Xintra Elephpant</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/css/profile.css" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link href="./assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet">
  <link href="./assets/libs/@simonwep/pickr/themes/nano.min.css" rel="stylesheet">
  <link href="./assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css" rel="stylesheet">
</head>
<body>
  <?php include ROOT . '/layout/init.php'; ?>
  <div class="page">
    <?php include ROOT . '/layout/header.php'; ?>
    <?php include ROOT . '/layout/sidebar.php'; ?>

    <main class="main-content app-content">
      <div class="container-fluid">
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2 mb-5">
          <div>
            <nav aria-label="Navegación">
              <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mi perfil</li>
              </ol>
            </nav>
            <h1 class="page-title font-medium text-lg mb-0">Mi perfil</h1>
          </div>
        </div>

        <?php if (!$perfil): ?>
          <div class="box"><div class="box-body text-center text-danger" role="alert">No se pudo cargar el perfil de tu usuario en la sucursal actual.</div></div>
        <?php else: ?>
          <section class="box profile-summary" aria-labelledby="profile-name">
            <div class="profile-cover"></div>
            <div class="box-body profile-summary-body">
              <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <img src="./assets/images/faces/15.jpg" alt="Foto de perfil" class="avatar avatar-xxl profile-avatar">
                <div class="flex-auto min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <h2 id="profile-name" class="text-xl font-semibold mb-0"><?= $escapar($nombreCompleto ?: $perfil['usuario']) ?></h2>
                    <span class="badge <?= (int)$perfil['estado'] === 1 ? 'bg-success' : 'bg-danger' ?>"><?= $escapar($estado) ?></span>
                  </div>
                  <p class="text-textmuted mb-0 mt-1">@<?= $escapar($perfil['usuario']) ?></p>
                </div>
                <div class="profile-summary-meta text-sm text-textmuted">
                  <span class="badge bg-primary/10 text-primary"><i class="ri-building-line me-1" aria-hidden="true"></i><?= $escapar($perfil['sucursal'] ?: 'Sucursal no asignada') ?></span>
                  <span class="badge bg-light text-defaulttextcolor"><i class="ri-briefcase-3-line me-1" aria-hidden="true"></i><?= $escapar($perfil['cargo'] ?: 'Cargo no registrado') ?></span>
                </div>
              </div>
            </div>
          </section>

          <div class="grid grid-cols-12 gap-6">
            <section class="col-span-12 xl:col-span-4" aria-labelledby="profile-contact-title">
              <div class="box h-full">
                <div class="box-header"><h3 id="profile-contact-title" class="box-title">Información de contacto</h3></div>
                <div class="box-body divide-y divide-defaultborder dark:divide-white/10">
                  <div class="profile-detail-row">
                    <span class="profile-detail-icon"><i class="ri-mail-line" aria-hidden="true"></i></span>
                    <div class="min-w-0"><p class="profile-detail-label">Correo electrónico</p><p class="profile-detail-value break-all"><?= $escapar($perfil['email'] ?: 'No registrado') ?></p></div>
                  </div>
                  <div class="profile-detail-row">
                    <span class="profile-detail-icon"><i class="ri-phone-line" aria-hidden="true"></i></span>
                    <div><p class="profile-detail-label">Teléfono</p><p class="profile-detail-value"><?= $escapar($perfil['telefono'] ?: 'No registrado') ?></p></div>
                  </div>
                  <div class="profile-detail-row">
                    <span class="profile-detail-icon"><i class="ri-smartphone-line" aria-hidden="true"></i></span>
                    <div><p class="profile-detail-label">Celular</p><p class="profile-detail-value"><?= $escapar($perfil['celular'] ?: 'No registrado') ?></p></div>
                  </div>
                  <div class="profile-detail-row">
                    <span class="profile-detail-icon"><i class="ri-map-pin-line" aria-hidden="true"></i></span>
                    <div class="min-w-0"><p class="profile-detail-label">Dirección</p><p class="profile-detail-value"><?= $escapar($perfil['direccion'] ?: 'No registrada') ?></p><p class="profile-detail-caption"><?= $escapar($ubicacion ?: 'Ubicación no registrada') ?></p></div>
                  </div>
                </div>
              </div>
            </section>

            <section class="col-span-12 xl:col-span-8" aria-labelledby="profile-details-title">
              <div class="box h-full">
                <div class="box-header"><h3 id="profile-details-title" class="box-title">Datos de la cuenta</h3></div>
                <div class="box-body">
                  <dl class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="profile-info-card"><dt>Nombres</dt><dd><?= $escapar($perfil['nombres'] ?: 'No registrados') ?></dd></div>
                    <div class="profile-info-card"><dt>Apellidos</dt><dd><?= $escapar($perfil['apellidos'] ?: 'No registrados') ?></dd></div>
                    <div class="profile-info-card"><dt>Documento</dt><dd><?= $escapar($perfil['documento'] ?: 'No registrado') ?></dd></div>
                    <div class="profile-info-card"><dt>Sexo</dt><dd><?= $escapar($sexo) ?></dd></div>
                    <div class="profile-info-card"><dt>Fecha de nacimiento</dt><dd><?= $escapar($perfil['fecha_nacimiento'] ?: 'No registrada') ?></dd></div>
                    <div class="profile-info-card"><dt>Cargo</dt><dd><?= $escapar($perfil['cargo'] ?: 'No registrado') ?></dd></div>
                    <div class="profile-info-card"><dt>Sucursal</dt><dd><?= $escapar($perfil['sucursal'] ?: 'Sucursal no asignada') ?></dd></div>
                    <div class="profile-info-card"><dt>Miembro desde</dt><dd><?= $escapar($fechaRegistro) ?></dd></div>
                    <div class="profile-info-card"><dt>ID de usuario</dt><dd>#<?= (int)$perfil['id'] ?></dd></div>
                    <div class="profile-info-card"><dt>Usuario</dt><dd><?= $escapar($perfil['usuario']) ?></dd></div>
                  </dl>
                </div>
              </div>
            </section>
          </div>
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
  <script src="./assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js"></script>
  <script src="./assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
  <script src="./assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="./assets/js/custom-switcher.min.js"></script>
  <script src="./assets/js/custom.js"></script>
</body>
</html>
