<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/controller/check_session.php';
?>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
      <title>Xintra Elephant</title>
      <meta name="Description" content="Tailwind Responsive Admin Web Dashboard HTML5 Template">
      <meta name="Author" content="Spruko Technologies Private Limited">
      <meta name="keywords" content="tailwind template,tailwind dashboard,tailwind,tailwind admin template,dashboard,tailwind css templates,html dashboard template,tailwind dashboard template,dashboard tailwind,admin,html css templates,html dashboard,html css javascript templates,dashboard tailwind template,tailwind css dashboard">
      <script src="./assets/js/main.js"></script> 
      <link href="./assets/css/styles.css" rel="stylesheet">
      <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
      <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
      <link rel="stylesheet" href="./assets/libs/flatpickr/flatpickr.min.css">
      <link rel="stylesheet" href="./assets/libs/@simonwep/pickr/themes/nano.min.css">
      <link rel="stylesheet" href="./assets/libs/choices.js/public/assets/styles/choices.min.css">
      <link rel="stylesheet" href="./assets/libs/flatpickr/flatpickr.min.css">
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
                           <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                        </ol>
                     </nav>
                     <h1 class="page-title font-medium text-lg mb-0">Nuevo Usuario</h1>
                  </div>
                  <div class="btn-list"> <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"  onclick="window.location.href='usuarios.php'"> <i class="ri-reply-line"></i> </button> </div>
               </div>
            
               <div class="col-span-12">
                  <div class="box">
                     <div class="box-header">
                        <h5 class="box-title">Data Validation</h5>
                     </div>
                     <div class="box-body">
                        <form class="ti-custom-validation-user" novalidate>
                           <div class="grid lg:grid-cols-2 gap-6">

                              <div class="space-y-2">
                                 <label class="ti-form-label">Documento</label>
                                 <input id="documento" name="documento" type="text" inputmode="numeric" class="ti-form-input rounded-sm"
                                       placeholder="12345678" data-rules="required|numeric|min:8|max:11">
                                 <span class="text-red-500 text-xs hidden" data-error-for="documento"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Nombres</label>
                                 <input id="firstName" name="nombres" type="text" class="ti-form-input rounded-sm"
                                       placeholder="Firstname" data-rules="required|min:2|max:50">
                                 <span class="text-red-500 text-xs hidden" data-error-for="firstName"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Apellidos</label>
                                 <input id="lastName" name="apellidos" type="text" class="ti-form-input rounded-sm"
                                       placeholder="Lastname" data-rules="required|min:2|max:50">
                                 <span class="text-red-500 text-xs hidden" data-error-for="lastName"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Email</label>
                                 <input id="email" name="email" type="email" class="ti-form-input rounded-sm"
                                       placeholder="your@site.com" data-rules="required|email">
                                 <span class="text-red-500 text-xs hidden" data-error-for="email"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Teléfono</label>
                                 <input id="phone" name="telefono" type="text" inputmode="numeric" class="ti-form-input rounded-sm"
                                       placeholder="987654321" data-rules="required|numeric|min:6|max:12">
                                 <span class="text-red-500 text-xs hidden" data-error-for="phone"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Sexo</label>
                                 <ul class="flex flex-col sm:flex-row">
                                 <li class="ti-list-group w-full gap-x-2.5 flex py-2 px-4">
                                    <div class="relative flex items-start w-full">
                                       <div class="flex items-center h-5">
                                       <input id="sexo-f" name="sexo" type="radio" value="2" class="ti-form-radio" data-rules="required">
                                       </div>
                                       <label for="sexo-f" class="ms-3 block w-full text-sm">Femenino</label>
                                    </div>
                                 </li>
                                 <li class="ti-list-group w-full gap-x-2.5 flex py-2 px-4">
                                    <div class="relative flex items-start w-full">
                                       <div class="flex items-center h-5">
                                       <input id="sexo-m" name="sexo" type="radio" value="1" class="ti-form-radio" data-rules="required">
                                       </div>
                                       <label for="sexo-m" class="ms-3 block w-full text-sm">Masculino</label>
                                    </div>
                                 </li>
                                 <li class="ti-list-group w-full gap-x-2.5 flex py-2 px-4">
                                    <div class="relative flex items-start w-full">
                                       <div class="flex items-center h-5">
                                       <input id="sexo-o" name="sexo" type="radio" value="0" class="ti-form-radio" data-rules="required">
                                       </div>
                                       <label for="sexo-o" class="ms-3 block w-full text-sm">Otro</label>
                                    </div>
                                 </li>
                                 </ul>
                                 <span class="text-red-500 text-xs hidden" data-error-for="sexo"></span>
                              </div>
                           </div>

                           <div class="my-5">
                              <input id="terms" name="terms" type="checkbox" class="ti-form-checkbox mt-0.5" data-rules="required">
                              <label for="terms" class="text-sm inline">Acepto los términos y condiciones</label>
                              <span class="text-red-500 text-xs hidden" data-error-for="terms"></span>
                           </div>

                           <button type="submit" class="ti-btn ti-btn-primary ti-custom-validate-btn">Enviar</button>
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
      <script src="./assets/libs/tabulator-tables/js/tabulator.min.js"></script>
      <script src="./assets/js/form-validation.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
      <script src="./assets/js/custom.js"></script>
   </body>
</html>