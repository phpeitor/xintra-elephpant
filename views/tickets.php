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
            <div class="container-fluid">

               <div class="xxl:col-span-4 col-span-12 div_upgrade !hidden">
                  <div class="box main-dashboard-banner main-dashboard-banner2 overflow-hidden">
                     <div class="box-body p-6">
                        <div class="grid grid-cols-12 sm:gap-x-6 justify-between">
                        <div class="xxl:col-span-8 xl:col-span-4 lg:col-span-5 md:col-span-5 sm:col-span-5 col-span-12">
                           <h4 class="mb-3 font-medium text-white">¡Actualiza tu plan para obtener más!</h4>
                           <p class="mb-3 text-white text-[11px]">Obtener acceso Premium y desbloquear funciones exclusivas</p>
                           <a href="javascript:void(0);" class="font-medium text-white underline">Actualizar <i class="ti ti-arrow-narrow-right"></i>
                           </a>
                        </div>
                        <div class="xxl:col-span-4 xl:col-span-7 lg:col-span-7 md:col-span-7 sm:col-span-7 col-span-12 sm:block hidden text-end my-auto">
                           <img src="./assets/images/media/media-91.png" alt="" class="img-fluid">
                        </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="xxl:col-span-8 col-span-12">
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                     <!-- Bloque 1 -->
                     <div class="box overflow-hidden">
                        <div class="box-body pb-0 pe-0">
                        <div class="mb-4">
                           <div class="flex justify-between flex-wrap">
                              <span class="avatar avatar-rounded bg-primary svg-white">
                              <i class="bx bx-group text-[22px]"></i>
                              </span>
                              <span class="font-medium text-[13px] text-textmuted dark:text-textmuted/50">Total Clientes</span>
                           </div>
                        </div>
                        <div class="flex items-end justify-between">
                           <div class="pb-3">
                              <span class="text-[20px] font-medium flex items-center" id="total_21">0</span>
                              <div class="text-textmuted dark:text-textmuted/50 text-[13px]" id="inc_21">Increased By</div>
                              <span class="text-success" id="pct_21">0% <i class="ti ti-arrow-narrow-up text-[16px]"></i></span>
                           </div>
                           <div id="chart-21" style="min-height:85px;"></div>
                        </div>
                        </div>
                     </div>

                     <!-- Bloque 2 -->
                     <div class="box overflow-hidden">
                        <div class="box-body pb-0 pe-0">
                        <div class="mb-4">
                           <div class="flex justify-between">
                              <span class="avatar avatar-rounded bg-primarytint1color svg-white">
                              <i class="bx bx-trending-up text-[22px]"></i>
                              </span>
                              <span class="font-medium text-[13px] text-textmuted dark:text-textmuted/50">Total Mensual <code id="mes_22">?</code></span>
                           </div>
                        </div>
                        <div class="flex items-end justify-between">
                           <div class="pb-3">
                              <span class="text-[20px] font-medium flex items-center" id="total_22">0</span>
                              <div class="text-textmuted dark:text-textmuted/50 text-[13px]" id="inc_22">Increased By</div>
                              <span class="text-success" id="pct_22">0% <i class="ti ti-arrow-narrow-up text-[16px]"></i></span>
                           </div>
                           <div id="chart-22" style="min-height:85px;"></div>
                        </div>
                        </div>
                     </div>

                     <!-- Bloque 3 -->
                     <div class="box overflow-hidden">
                        <div class="box-body pb-0 pe-0">
                        <div class="mb-4">
                           <div class="flex justify-between">
                              <span class="avatar avatar-rounded bg-primarytint2color svg-white">
                              <i class="bx bx-dollar text-[22px]"></i>
                              </span>
                              <span class="font-medium text-[13px] text-textmuted dark:text-textmuted/50">Total Diario <code id="mes_23">?</code></span>
                           </div>
                        </div>
                        <div class="flex items-end justify-between">
                           <div class="pb-3">
                              <span class="text-[20px] font-medium flex items-center" id="total_23">0</span>
                              <div class="text-textmuted dark:text-textmuted/50 text-[13px]" id="inc_23">Decreased By</div>
                              <span class="text-danger" id="pct_23">0% <i class="ti ti-arrow-narrow-down text-[16px]"></i></span>
                           </div>
                           <div id="chart-23" style="min-height:85px;"></div>
                        </div>
                        </div>
                     </div>

                  </div>
               </div>
               <!-- Start::page-header -->
               <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
                  <div>
                     <nav aria-label="nav">
                        <ol class="breadcrumb mb-1">
                           <li class="breadcrumb-item"><a href="javascript:void(0);">Tickets</a></li>
                           <li class="breadcrumb-item active" aria-current="page">Data Tickets</li>
                        </ol>
                     </nav>
                     <h1 class="page-title font-medium text-lg mb-0">Data Tickets</h1>
                  </div>
                  <div class="flex gap-2 flex-wrap">
                     <div class="form-group">
                        <div class="input-group">
                           <div class="input-group-text bg-white dark:bg-bodybg border">
                              <i class="ri-calendar-line"></i>
                           </div>
                           <input class="form-control breadcrumb-input flatpickr-input" id="daterange" placeholder="Search By Date Range" readonly="readonly" type="text"/>
                           <button type="button" class="ti-btn ti-btn-icon ti-btn-outline-secondary !rounded-full btn-wave  waves-effect waves-light">   
                              <i class="ri-search-line me-1"></i> 
                           </button>   
                        </div>
                     </div>

                     <div class="btn-list"> 
                        <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> 
                        <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light btn-registrar" onclick="window.location.href='add_ticket.php'"> <i class="ri-share-forward-line me-1"></i> Registrar 
                        </button> 
                     </div>
                  </div>
               </div>
            
               <div class="grid grid-cols-12 gap-6">
                  <div class="col-span-12">
                     <div class="box">

                        <div class="box-body space-y-3">

                           <div class="download-data"> 
                              <button type="button" class="ti-btn ti-btn-primary" id="download-csv">Download CSV</button> 
                              <button type="button" class="ti-btn ti-btn-primary" id="download-json">Download JSON</button> <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button>
                           </div>

                           <div class="overflow-hidden table-bordered">
                              <div id="download-table" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid" tabulator-layout="fitColumns">
                              </div>
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
      <script src="./assets/libs/xlsx/xlsx.full.min.js"></script>
      <script src="./assets/libs/jspdf/jspdf.umd.min.js"></script>
      <script src="./assets/libs/jspdf-autotable/jspdf.plugin.autotable.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
      <script src="./assets/js/datatables_ticket.js?v=1"></script>
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
      <script src="./assets/js/custom.js"></script>
      <script src="./assets/libs/apexcharts/apexcharts.min.js"></script>
      <script src="./assets/js/analytics-dashboard.js?v=1.0"></script>
   </body>
</html>