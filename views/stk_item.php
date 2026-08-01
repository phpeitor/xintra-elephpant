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
      <meta name="Author" content="amvsoft.tech Technologies Private Limited">
      <meta name="keywords" content="tailwind template,tailwind dashboard,tailwind,tailwind admin template,dashboard,tailwind css templates,html dashboard template,tailwind dashboard template,dashboard tailwind,admin,html css templates,html dashboard,html css javascript templates,dashboard tailwind template,tailwind css dashboard">
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
            <div class="container-fluid ti-stock-item">
               <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
                  <div>
                     <nav aria-label="nav">
                        <ol class="breadcrumb mb-1">
                           <li class="breadcrumb-item"><a href="javascript:void(0);">Items</a></li>
                           <li class="breadcrumb-item active" aria-current="page">Stock</li>
                        </ol>
                     </nav>
                     <h1 class="page-title font-medium text-lg mb-0">Stock Item </h1>
                  </div>
                  <div class="btn-list"> 
                     <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"  onclick="window.location.href='items.php'"> <i class="ri-reply-line"></i> </button> 
                  </div>
               </div>

               <div class="grid grid-cols-12 gap-x-6">
                  <div class="xl:col-span-12 col-span-12">
                     <div class="box">
                        <div class="box-body">
                        <div class="flex items-center flex-wrap gap-2 justify-between">
                           <div class="flex items-center">
                              <span class="font-medium text-[1rem] me-2" id="producto">Producto</span>
                              <span class="badge bg-primary align-middle" id="categoria">Categoria</span>
                           </div>
                           <div class="flex flex-wrap gap-2">
                              <button aria-label="button" type="button" class="ti-btn ti-btn-primary ti-btn-sm" data-hs-overlay="#create-stock">
                              <i class="ri-add-line me-1 font-medium align-middle"></i>Stock </button>
                           </div>
                        </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="grid grid-cols-12 gap-x-6">
                  <div class="xxl:col-span-2 md:col-span-4 col-span-12">
                     <div class="box border border-primary/50">
                        <div class="box-body p-4">
                        <div class="flex items-top flex-wrap justify-between">
                           <div>
                              <h6 class="font-medium lead-discovered">
                              <i class="ri-circle-fill p-1 leading-none text-[0.4375rem] rounded-md bg-primary/10 text-primary me-2 align-middle"></i>Almacén
                              </h6>
                           </div>
                           <div class="ms-auto text-center">
                              <span class=" badge bg-primary" id="almacen">0</span>
                           </div>
                        </div>
                        </div>
                     </div>
                  </div>
                  <div class="xxl:col-span-2 md:col-span-4 col-span-12">
                     <div class="box border border-primarytint1color/50">
                        <div class="box-body p-4">
                           <div class="flex items-top flex-wrap justify-between">
                              <div>
                                 <h6 class="font-medium lead-qualified">
                                 <i class="ri-circle-fill p-1 leading-none text-[0.4375rem] rounded-md bg-primarytint1color/10 text-primarytint1color me-2 align-middle"></i>Ventas
                                 </h6>
                              </div>
                              <div>
                                 <span class=" badge bg-primarytint1color text-white" id="ventas">0</span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="xxl:col-span-2 md:col-span-4 col-span-12">   
                     <div id="stock-box" class="box border border-primarytint2color/50">
                        <div class="box-body p-4">
                           <div class="flex items-top flex-wrap justify-between">
                              <div>
                                 <h6 class="font-medium">
                                    <i id="stock-icon" class="ri-circle-fill p-1 leading-none text-[0.4375rem] rounded-md bg-primarytint2color/10 text-primarytint2color me-2 align-middle"></i>
                                    Stock
                                 </h6>
                              </div>
                              <div>
                                 <span id="stock-badge" class="badge bg-primarytint2color text-white">0</span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="grid grid-cols-12 gap-x-6">
                  <div class="xxl:col-span-4 col-span-12">
                     <div class="box">
                        <div class="box-header">
                           <div class="box-title"> Sales </div>
                        </div>
                        <div class="box-body">
                           <div id="salerevenue1" class="" style="min-height: 315px;">
                           </div>
                        </div>
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
      <script src="./assets/js/form-validation.js?v=1"></script>
      <script src="./assets/js/custom.js"></script>
      <script src="./assets/libs/apexcharts/apexcharts.min.js"></script>
      <script src="./assets/js/widgets.js?v=3.1"></script>
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
   </body>
</html>