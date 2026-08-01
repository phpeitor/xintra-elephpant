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
      <link href="./assets/css/xintra-ux.css" rel="stylesheet">
      <meta http-equiv="imagetoolbar" content="no">
   </head>
   <body>
   <?php include ROOT . '/layout/init.php'; ?>

   <div class="page">
      <?php include ROOT . '/layout/header.php'; ?>
      <?php include ROOT . '/layout/sidebar.php'; ?>
         <div class="main-content app-content">
            <div class="container-fluid">
               <div class="xxl:col-span-4 xl:col-span-6 col-span-12">
                  <div class="box">
                     <div class="box-header justify-between">
                        <div class="box-title"> Tickets Usuario <code>(Comparación por usuario)</code></div>
                        <a href="javascript:void(0);" class="ti-btn ti-btn-light btn-wave text-textmuted dark:text-textmuted/50 waves-effect ti-btn-sm waves-light">View All</a>
                     </div>
                     <div class="box-body sm:block items-center">
                         <div class="grid grid-cols-12 gap-6 items-center mb-4">
                            <div class="xl:col-span-4 md:col-span-4 col-span-12">
                               <div id="referrals-chart" class="p-4 flex-shrink-0 px-0" style="min-height: 248.7px;"></div>
                            </div>
                            <div class="xl:col-span-4 md:col-span-4 col-span-12">
                               <div id="tickets-chart" class="p-4 flex-shrink-0 px-0" style="min-height: 248.7px;"></div>
                            </div>
                            <div class="xl:col-span-4 md:col-span-4 col-span-12">
                               <div id="items-chart" class="p-4 flex-shrink-0 px-0" style="min-height: 248.7px;"></div>
                            </div>
                         </div>

                        <div class="table-responsive overflow-x-auto overflow-y-visible table-bordered-default">
                           <table class="ti-custom-table text-nowrap min-w-full">
                              <thead>
                                 <tr>
                                     <th class="border-b border-defaultborder dark:border-defaultborder/10" rowspan="2">Usuario</th>
                                     <th class="border-b border-defaultborder dark:border-defaultborder/10" rowspan="2"></th>
                                     <th class="border-b border-defaultborder dark:border-defaultborder/10 text-center" colspan="2">Total</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10 text-center" colspan="2">Tickets</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10 text-center" colspan="2">Items</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10" rowspan="2">Variación</th>
                                 </tr>
                                 <tr>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10">Ultimo</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10">Anterior</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10">Ultimo</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10">Anterior</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10">Ultimo</th>
                                    <th class="border-b border-defaultborder dark:border-defaultborder/10">Anterior</th>
                                 </tr>
                              </thead>
                              <tbody>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="xxl:col-span-4 xl:col-span-6 col-span-12">
                  <div class="box">
                     <div class="box-header justify-between">
                        <h5 class="box-title">Top Items <code>(Últimos 15 días)</code></h5>
                        <a href="javascript:void(0);" class="ti-btn ti-btn-light btn-wave text-textmuted dark:text-textmuted/50 ti-btn-sm waves-effect waves-light">View All</a>
                     </div>
                     <div class="box-body bar-graf">
                        
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
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
      <script src="./assets/js/custom.js?v=2"></script>
      <script src="./assets/libs/apexcharts/apexcharts.min.js"></script>
      <script src="./assets/js/xintra-tooltip.js?v=1.3"></script>
      <script src="./assets/js/analytics-reporte.js?v=1.6"></script>
   </body>
</html>
