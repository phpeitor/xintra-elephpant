<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
$permisosVista = new Permisos();
$puedeGestionarSucursalCargo = $permisosVista->esAdminOCargoUno((int)($_SESSION['session_id'] ?? 0));
?>

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
      <link href="./assets/css/usuarios.css?v=2" rel="stylesheet">
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
                     <nav aria-label="nav">
                        <ol class="breadcrumb mb-1">
                           <li class="breadcrumb-item"><a href="javascript:void(0);">Usuarios</a></li>
                           <li class="breadcrumb-item active" aria-current="page">Data Usuarios</li>
                        </ol>
                     </nav>
                     <h1 class="page-title font-medium text-lg mb-0">Data Usuarios</h1>
                  </div>
                  <div class="btn-list"> <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> 
                     <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"
                                                         onclick="window.location.href='add_usuario.php'"> <i class="ri-share-forward-line me-1"></i> Registrar </button> 
                  </div>
               </div>
            
               <div class="grid grid-cols-12 gap-6">
                  <div class="col-span-12">
                     <div class="box">
                        <div class="box-header">
                           <h5 class="box-title">Download DataTable</h5>
                        </div>
                        <div class="box-body space-y-3">
                            <div class="download-data flex items-center gap-2 flex-wrap">
                               <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button>
                               <button type="button" class="ti-btn ti-btn-primary" id="download-pdf">Download PDF</button>
                               <select id="user-status-filter" class="ti-form-select rounded-sm !py-2 !px-3 w-auto" aria-label="Filtrar usuarios por estado">
                                  <option value="ACTIVOS" selected>Usuarios activos</option>
                                  <option value="INACTIVOS">Usuarios inactivos</option>
                                  <option value="TODOS">Todos los usuarios</option>
                               </select>
                            </div>
                            <div class="overflow-x-auto table-bordered">
                               <div id="download-table" data-current-user-id="<?= (int)($_SESSION['session_id'] ?? 0) ?>" data-puede-gestionar-sucursal-cargo="<?= $puedeGestionarSucursalCargo ? '1' : '0' ?>" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid" tabulator-layout="fitColumns">
                               </div>

                            </div>
                        </div>
                     </div>
                  </div>
                </div>
            </div>
         </div>

         <button id="abrir-modal-permisos-usuario" class="hidden" type="button" data-hs-overlay="#modal-permisos-usuario" aria-hidden="true" tabindex="-1"></button>
         <div id="modal-permisos-usuario" class="hs-overlay hidden fixed inset-0 z-[110] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-permisos-usuario-titulo">
            <div class="assignment-modal-dialog hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-300 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
               <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
                  <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-defaultborder/10">
                     <div><h2 id="modal-permisos-usuario-titulo" class="text-lg font-semibold">Permisos del usuario</h2><p id="permisos-usuario-nombre" class="text-sm text-textmuted mt-1"></p></div>
                     <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-permisos-usuario" aria-label="Cerrar"><i class="ri-close-line" aria-hidden="true"></i></button>
                  </div>
                  <form id="form-permisos-usuario" class="p-4 space-y-4">
                     <input type="hidden" id="permisos-usuario-id" name="id">
                     <div id="permisos-usuario-lista" class="grid grid-cols-1 sm:grid-cols-2 gap-3" aria-live="polite"></div>
                     <p id="permisos-usuario-error" class="hidden text-danger text-sm" role="alert"></p>
                     <div class="flex justify-end gap-2 border-t border-defaultborder dark:border-white/10 pt-4">
                        <button type="button" class="ti-btn ti-btn-light" data-hs-overlay="#modal-permisos-usuario">Cancelar</button>
                        <button id="btnGuardarPermisosUsuario" type="submit" class="ti-btn ti-btn-primary">Guardar </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>

         <button id="abrir-modal-asignacion-usuario" class="hidden" type="button" data-hs-overlay="#modal-asignacion-usuario" aria-hidden="true" tabindex="-1"></button>
         <div id="modal-asignacion-usuario" class="hs-overlay hidden fixed inset-0 z-[110] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-asignacion-usuario-titulo">
             <div class="assignment-modal-dialog hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-300 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
               <div class="pointer-events-auto flex flex-col bg-white border border-defaultborder shadow-xl rounded-xl dark:bg-bodybg dark:border-white/10">
                  <div class="flex justify-between items-center py-3 px-4 border-b border-defaultborder dark:border-defaultborder/10">
                     <div>
                        <h2 id="modal-asignacion-usuario-titulo" class="text-lg font-semibold">Sucursal y cargo</h2>
                        <p id="asignacion-usuario-nombre" class="text-sm text-textmuted mt-1"></p>
                     </div>
                     <button type="button" class="ti-btn ti-btn-light !mb-0" data-hs-overlay="#modal-asignacion-usuario" aria-label="Cerrar">
                        <i class="ri-close-line" aria-hidden="true"></i>
                     </button>
                  </div>
                  <form id="form-asignacion-usuario" class="p-4 space-y-4">
                     <input type="hidden" id="asignacion-usuario-id" name="id">
                     <div class="space-y-2">
                        <label for="asignacion-sucursal" class="ti-form-label">Sucursal</label>
                        <select id="asignacion-sucursal" name="id_sucursal" class="ti-form-select rounded-sm" required></select>
                     </div>
                     <div class="space-y-2">
                        <label for="asignacion-cargo" class="ti-form-label">Cargo</label>
                        <select id="asignacion-cargo" name="cargo" class="ti-form-select rounded-sm" required></select>
                     </div>
                     <p class="text-xs text-textmuted">La transferencia se bloquea si el usuario tiene tickets o asistencias históricas para preservar la sucursal de esos registros. Si cambia de sucursal, deberá iniciar sesión nuevamente.</p>
                     <p id="asignacion-usuario-error" class="hidden text-danger text-sm" role="alert"></p>
                     <div class="flex justify-end gap-2 border-t border-defaultborder dark:border-white/10 pt-4">
                        <button type="button" class="ti-btn ti-btn-light" data-hs-overlay="#modal-asignacion-usuario">Cancelar</button>
                        <button id="btnGuardarAsignacionUsuario" type="submit" class="ti-btn ti-btn-primary">Guardar cambios</button>
                     </div>
                  </form>
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
      <script src="./assets/libs/tabulator-tables/js/tabulator.min.js"></script>
      <script src="./assets/libs/xlsx/xlsx.full.min.js"></script>
      <script src="./assets/libs/jspdf/jspdf.umd.min.js"></script>
       <script src="./assets/libs/jspdf-autotable/jspdf.plugin.autotable.min.js"></script>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
       <script src="./assets/js/xintra-tooltip.js?v=1"></script>
       <script src="./assets/js/datatables_user.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
      <script src="./assets/js/custom.js"></script>
   </body>
</html>
