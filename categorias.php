<?php
require_once __DIR__ . "/controller/check_session.php";
?>
<!doctype html>
<html lang="es" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark" data-width="fullwidth" loader="disable" bg-img="bgimg5" data-vertical-style="overlay">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title>Xintra Elephant - Categorías</title>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet">
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/libs/tabulator-tables/css/tabulator.min.css">
  <link rel="stylesheet" href="./assets/libs/flatpickr/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css"/>
</head>
<body>
  <div id="loader" class="loader-disable"> <img src="./assets/images/media/loader.svg" alt=""> </div>
  <div class="page">
    <header class="app-header sticky sticky-pin" id="header">
      <div class="main-header-container container-fluid">
        <div class="header-content-left">
          <div class="header-element">
            <div class="horizontal-logo"><a href="index.php" class="header-logo"><img src="./assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo"></a></div>
          </div>
          <div class="header-element mx-lg-0">
            <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
          </div>
        </div>
        <?php include __DIR__ . '/navbar.php'; ?>
      </div>
    </header>

    <aside class="app-sidebar sticky-pin" id="sidebar">
      <div class="main-sidebar-header">
        <a href="index.php" class="header-logo">
          <img src="./assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
          <img src="./assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark">
          <img src="./assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
          <img src="./assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
          <img src="./assets/images/brand-logos/toggle-white.png" alt="logo" class="toggle-white">
          <img src="./assets/images/brand-logos/desktop-white.png" alt="logo" class="desktop-white">
        </a>
      </div>
      <div class="main-sidebar" id="sidebar-scroll" data-simplebar="init">
        <div class="simplebar-wrapper" style="margin: -8px 0px -80px;">
          <div class="simplebar-height-auto-observer-wrapper">
            <div class="simplebar-height-auto-observer"></div>
          </div>
          <div class="simplebar-mask">
            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
              <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                <div class="simplebar-content" style="padding: 8px 0px 80px;">
                  <?php include __DIR__ . '/menu.php'; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="simplebar-placeholder" style="width: auto; height: 1210px;"></div>
        </div>
        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
          <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
        </div>
        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
          <div class="simplebar-scrollbar" style="height: 42px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
        </div>
      </div>
    </aside>

    <div class="main-content app-content">
      <div class="container-fluid">
        <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
          <div>
            <nav aria-label="nav"><ol class="breadcrumb mb-1"><li class="breadcrumb-item">Catálogo</li><li class="breadcrumb-item active" aria-current="page">Categorías</li></ol></nav>
            <h1 class="page-title font-medium text-lg mb-0">Data Categorías</h1>
          </div>
          <div class="btn-list">
            <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light" onclick="window.location.href='add_categoria.php'">
              <i class="ri-share-forward-line me-1"></i> Registrar
            </button>
          </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header"><h5 class="box-title">Download DataTable</h5></div>
              <div class="box-body space-y-3">
                <div class="download-data">
                  <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="download-pdf">Download PDF</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="download-table" class="ti-custom-table ti-striped-table ti-custom-table-hover tabulator" role="grid" tabulator-layout="fitColumns"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <footer class="mt-auto py-4 bg-white dark:bg-bodybg text-center border-t border-defaultborder dark:border-defaultborder/10">
        <div class="container"> <span class="text-textmuted dark:text-textmuted/50"> Copyright © <span id="year">2026</span> <a href="javascript:void(0);" class="text-dark font-medium">Xintra</a>. Designed with <span class="text-danger">❤</span> by <a href="https://www.instagram.com/amvsoft.tech/" target="_blank"> <span class="font-medium text-primary">AMV</span> </a> All rights reserved </span> </div>
      </footer>
      <div class="hs-overlay ti-modal hidden" id="header-responsive-search" tabindex="-1" aria-labelledby="header-responsive-search">
        <div class="ti-modal-box">
          <div class="ti-modal-dialog">
            <div class="ti-modal-content">
              <div class="ti-modal-body">
                <div class="input-group"> <input type="text" class="form-control border-end-0 !border-s" placeholder="Search Anything ..." aria-label="Search Anything ..." aria-describedby="button-addon2"> <button aria-label="button" class="ti-btn ti-btn-primary !m-0" type="button" id="button-addon2"><i class="bi bi-search"></i></button> </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>

    <div class="scrollToTop" style="display: flex;"> <span class="arrow"><i class="ti ti-arrow-narrow-up text-xl"></i></span> </div>
    <div id="responsive-overlay"></div>
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
  <script src="./assets/libs/tabulator-tables/js/tabulator.min.js"></script>
  <script src="./assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="./assets/libs/xlsx/xlsx.full.min.js"></script>
    <script src="./assets/libs/jspdf/jspdf.umd.min.js"></script>
    <script src="./assets/libs/jspdf-autotable/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <script src="./assets/js/datatables_categoria.js"></script>
  <script src="./assets/js/custom.js"></script>
  <div class="scrollToTop" style="display: flex;"> <span class="arrow"><i class="ti ti-arrow-narrow-up text-xl"></i></span> </div>
  <div id="responsive-overlay"></div>
</body>
</html>
