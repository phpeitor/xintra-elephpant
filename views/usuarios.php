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

      <div id="hs-overlay-switcher" class="hs-overlay hidden ti-offcanvas ti-offcanvas-right" tabindex="-1">
         <div class="ti-offcanvas-header z-10 relative">
            <h5 class="ti-offcanvas-title"> Switcher </h5>
            <button type="button" class="ti-btn flex-shrink-0 p-0 !mb-0  transition-none text-defaulttextcolor dark:text-defaulttextcolor/80 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white  dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10" data-hs-overlay="#hs-overlay-switcher"> <span class="sr-only">Close modal</span> <i class="ri-close-circle-line leading-none text-lg"></i> </button> 
         </div>
         <div class="ti-offcanvas-body !p-0 !border-b dark:border-white/10 z-10 relative !h-auto">
            <div class="flex rtl:space-x-reverse" aria-label="Tabs" role="tablist"> <button type="button" class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center  text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10  active" id="switcher-item-1" data-hs-tab="#switcher-1" aria-controls="switcher-1" role="tab"> Theme Style </button> <button type="button" class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px  bg-white font-normal text-center  text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10  dark:hover:text-gray-300" id="switcher-item-2" data-hs-tab="#switcher-2" aria-controls="switcher-2" role="tab"> Theme Colors </button> </div>
         </div>
         <div class="ti-offcanvas-body !p-0 !pb-[20rem]" id="switcher-body">
            <div id="switcher-1" role="tabpanel" aria-labelledby="switcher-item-1" class="">
               <div class="">
                  <p class="switcher-style-head">Theme Color Mode:</p>
                  <div class="grid grid-cols-3 switcher-style">
                     <div class="flex items-center"> <input type="radio" name="theme-style" class="ti-form-radio" id="switcher-light-theme" checked=""> <label for="switcher-light-theme" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal">Light</label> </div>
                     <div class="flex items-center"> <input type="radio" name="theme-style" class="ti-form-radio" id="switcher-dark-theme"> <label for="switcher-dark-theme" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal">Dark</label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Directions:</p>
                  <div class="grid grid-cols-3  switcher-style">
                     <div class="flex items-center"> <input type="radio" name="direction" class="ti-form-radio" id="switcher-ltr" checked=""> <label for="switcher-ltr" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">LTR</label> </div>
                     <div class="flex items-center"> <input type="radio" name="direction" class="ti-form-radio" id="switcher-rtl"> <label for="switcher-rtl" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">RTL</label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Navigation Styles:</p>
                  <div class="grid grid-cols-3  switcher-style">
                     <div class="flex items-center"> <input type="radio" name="navigation-style" class="ti-form-radio" id="switcher-vertical" checked=""> <label for="switcher-vertical" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Vertical</label> </div>
                     <div class="flex items-center"> <input type="radio" name="navigation-style" class="ti-form-radio" id="switcher-horizontal"> <label for="switcher-horizontal" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Horizontal</label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Navigation Menu Style:</p>
                  <div class="grid grid-cols-2 gap-2 switcher-style">
                     <div class="flex"> <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-menu-click" checked=""> <label for="switcher-menu-click" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Menu Click</label> </div>
                     <div class="flex"> <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-menu-hover"> <label for="switcher-menu-hover" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Menu Hover</label> </div>
                     <div class="flex"> <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-icon-click"> <label for="switcher-icon-click" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Icon Click</label> </div>
                     <div class="flex"> <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-icon-hover"> <label for="switcher-icon-hover" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Icon Hover</label> </div>
                  </div>
               </div>
               <div class=" sidemenu-layout-styles">
                  <p class="switcher-style-head">Sidemenu Layout Syles:</p>
                  <div class="grid grid-cols-2 gap-2 switcher-style">
                     <div class="flex"> <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-default-menu" checked=""> <label for="switcher-default-menu" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal ">Default Menu</label> </div>
                     <div class="flex"> <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-closed-menu"> <label for="switcher-closed-menu" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal "> Closed Menu</label> </div>
                     <div class="flex"> <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-icontext-menu"> <label for="switcher-icontext-menu" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal ">Icon Text</label> </div>
                     <div class="flex"> <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-icon-overlay"> <label for="switcher-icon-overlay" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal ">Icon Overlay</label> </div>
                     <div class="flex"> <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-detached"> <label for="switcher-detached" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal ">Detached</label> </div>
                     <div class="flex"> <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-double-menu"> <label for="switcher-double-menu" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Double Menu</label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Page Styles:</p>
                  <div class="grid grid-cols-3  switcher-style">
                     <div class="flex"> <input type="radio" name="data-page-styles" class="ti-form-radio" id="switcher-regular" checked=""> <label for="switcher-regular" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Regular</label> </div>
                     <div class="flex"> <input type="radio" name="data-page-styles" class="ti-form-radio" id="switcher-classic"> <label for="switcher-classic" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Classic</label> </div>
                     <div class="flex"> <input type="radio" name="data-page-styles" class="ti-form-radio" id="switcher-modern"> <label for="switcher-modern" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal"> Modern</label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Layout Width Styles:</p>
                  <div class="grid grid-cols-3 switcher-style">
                     <div class="flex"> <input type="radio" name="layout-width" class="ti-form-radio" id="switcher-full-width" checked=""> <label for="switcher-full-width" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">FullWidth</label> </div>
                     <div class="flex"> <input type="radio" name="layout-width" class="ti-form-radio" id="switcher-boxed"> <label for="switcher-boxed" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Boxed</label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Menu Positions:</p>
                  <div class="grid grid-cols-3  switcher-style">
                     <div class="flex"> <input type="radio" name="data-menu-positions" class="ti-form-radio" id="switcher-menu-fixed" checked=""> <label for="switcher-menu-fixed" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Fixed</label> </div>
                     <div class="flex"> <input type="radio" name="data-menu-positions" class="ti-form-radio" id="switcher-menu-scroll"> <label for="switcher-menu-scroll" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Scrollable </label> </div>
                  </div>
               </div>
               <div>
                  <p class="switcher-style-head">Header Positions:</p>
                  <div class="grid grid-cols-3 switcher-style">
                     <div class="flex"> <input type="radio" name="data-header-positions" class="ti-form-radio" id="switcher-header-fixed" checked=""> <label for="switcher-header-fixed" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal"> Fixed</label> </div>
                     <div class="flex"> <input type="radio" name="data-header-positions" class="ti-form-radio" id="switcher-header-scroll"> <label for="switcher-header-scroll" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Scrollable </label> </div>
                  </div>
               </div>
               <div class="">
                  <p class="switcher-style-head">Loader:</p>
                  <div class="grid grid-cols-3 switcher-style">
                     <div class="flex"> <input type="radio" name="page-loader" class="ti-form-radio" id="switcher-loader-enable" checked=""> <label for="switcher-loader-enable" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal"> Enable</label> </div>
                     <div class="flex"> <input type="radio" name="page-loader" class="ti-form-radio" id="switcher-loader-disable"> <label for="switcher-loader-disable" class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2  font-normal">Disable </label> </div>
                  </div>
               </div>
            </div>
            <div id="switcher-2" class="hidden" role="tabpanel" aria-labelledby="switcher-item-2">
               <div class="theme-colors">
                  <p class="switcher-style-head">Menu Colors:</p>
                  <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-white" type="radio" name="menu-colors" id="switcher-menu-light"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Light Menu </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-dark" type="radio" name="menu-colors" id="switcher-menu-dark" checked=""> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Dark Menu </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-primary" type="radio" name="menu-colors" id="switcher-menu-primary"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Color Menu </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient" type="radio" name="menu-colors" id="switcher-menu-gradient"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Gradient Menu </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent" type="radio" name="menu-colors" id="switcher-menu-transparent"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs !font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Transparent Menu </span> </div>
                  </div>
                  <div class="px-4 text-textmuted dark:text-textmuted/50 text-[.6875rem]"><b class="me-2 font-normal">Note:</b>If you want to change color Menu dynamically change from below Theme Primary color picker.</div>
               </div>
               <div class="theme-colors">
                  <p class="switcher-style-head">Header Colors:</p>
                  <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-white !border" type="radio" name="header-colors" id="switcher-header-light" checked=""> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Light Header </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-dark" type="radio" name="header-colors" id="switcher-header-dark"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Dark Header </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-primary" type="radio" name="header-colors" id="switcher-header-primary"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Color Header </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient" type="radio" name="header-colors" id="switcher-header-gradient"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Gradient Header </span> </div>
                     <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select "> <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent" type="radio" name="header-colors" id="switcher-header-transparent"> <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" role="tooltip" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);"> Transparent Header </span> </div>
                  </div>
                  <div class="px-4 text-textmuted dark:text-textmuted/50 text-[.6875rem]"><b class="me-2 !font-normal">Note:</b>If you want to change color Header dynamically change from below Theme Primary color picker.</div>
               </div>
               <div class="theme-colors">
                  <p class="switcher-style-head">Theme Primary:</p>
                  <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-primary-1" type="radio" name="theme-primary" id="switcher-primary"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-primary-2" type="radio" name="theme-primary" id="switcher-primary1"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-primary-3" type="radio" name="theme-primary" id="switcher-primary2"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-primary-4" type="radio" name="theme-primary" id="switcher-primary3"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-primary-5" type="radio" name="theme-primary" id="switcher-primary4"> </div>
                     <div class="ti-form-radio switch-select ps-0 mt-1 color-primary-light">
                        <div class="theme-container-primary"><button class="">nano</button></div>
                        <div class="pickr-container-primary">
                           <div class="pickr">
                              <button type="button" class="pcr-button" role="button" aria-label="toggle color picker dialog" style="--pcr-color: rgba(92, 103, 247, 1);"></button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="theme-colors">
                  <p class="switcher-style-head">Theme Background:</p>
                  <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-bg-1" type="radio" name="theme-background" id="switcher-background"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-bg-2" type="radio" name="theme-background" id="switcher-background1"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-bg-3" type="radio" name="theme-background" id="switcher-background2"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-bg-4" type="radio" name="theme-background" id="switcher-background3"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio color-input color-bg-5" type="radio" name="theme-background" id="switcher-background4"> </div>
                     <div class="ti-form-radio switch-select ps-0 mt-1 color-bg-transparent">
                        <div class="theme-container-background hidden"><button>nano</button></div>
                        <div class="pickr-container-background">
                           <div class="pickr">
                              <button type="button" class="pcr-button" role="button" aria-label="toggle color picker dialog" style="--pcr-color: rgba(92, 103, 247, 1);"></button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="menu-image theme-colors">
                  <p class="switcher-style-head">Menu With Background Image:</p>
                  <div class="flex switcher-style space-x-3 rtl:space-x-reverse flex-wrap gap-3">
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio bgimage-input bg-img1" type="radio" name="theme-images" id="switcher-bg-img"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio bgimage-input bg-img2" type="radio" name="theme-images" id="switcher-bg-img1"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio bgimage-input bg-img3" type="radio" name="theme-images" id="switcher-bg-img2"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio bgimage-input bg-img4" type="radio" name="theme-images" id="switcher-bg-img3"> </div>
                     <div class="ti-form-radio switch-select"> <input class="ti-form-radio bgimage-input bg-img5" type="radio" name="theme-images" id="switcher-bg-img4"> </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="ti-offcanvas-footer sm:flex justify-between"> <a href="javascript:void(0);" id="reset-all" class="ti-btn ti-btn-danger m-1">Reset</a> </div>
      </div>
      <!-- ========== END Switcher  ========== --> <!-- Loader --> 
      <div id="loader" class="loader-disable"> <img src="./assets/images/media/loader.svg" alt=""> </div>
      <!-- Loader --> 
      <div class="page">
         <!-- app-header --> 
         <header class="app-header sticky sticky-pin" id="header">
            <!-- Start::main-header-container --> 
            <div class="main-header-container container-fluid">
               <!-- Start::header-content-left --> 
               <div class="header-content-left">
                  <!-- Start::header-element --> 
                  <div class="header-element">
                     <div class="horizontal-logo"> <a href="index.php" class="header-logo"> <img src="./assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo"> <img src="./assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark"> <img src="./assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark"> <img src="./assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo"> <img src="./assets/images/brand-logos/toggle-white.png" alt="logo" class="toggle-white"> <img src="./assets/images/brand-logos/desktop-white.png" alt="logo" class="desktop-white"> </a> </div>
                  </div>
                  <!-- End::header-element --> <!-- Start::header-element --> 
                  <div class="header-element mx-lg-0"> <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a> </div>
                  <!-- End::header-element --> <!-- Start::header-element --> 
                  <div class="header-element header-search md:!block !hidden my-auto auto-complete-search">
                     <!-- Start::header-link --> 
                     <div class="autoComplete_wrapper" role="combobox" aria-owns="autoComplete_list_1" aria-haspopup="true" aria-expanded="false">
                        <input type="text" class="header-search-bar form-control" id="header-search" placeholder="Search anything here ..." autocomplete="off" autocapitalize="off" aria-controls="autoComplete_list_1" aria-autocomplete="both">
                        <ul id="autoComplete_list_1" role="listbox" hidden=""></ul>
                     </div>
                     <a aria-label="anchor" href="javascript:void(0);" class="header-search-icon border-0"> <i class="ri-search-line"></i> </a> <!-- End::header-link --> 
                  </div>
                  <!-- End::header-element --> 
               </div>
               <?php include ROOT . '/layout/navbar.php'; ?>
            </div>
            <!-- End::main-header-container --> 
         </header>
         <!-- /app-header --> <!-- Start::app-sidebar --> 
         <aside class="app-sidebar sticky-pin" id="sidebar">
            <!-- Start::main-sidebar-header --> 
            <div class="main-sidebar-header"> <a href="index.php" class="header-logo"> <img src="./assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo"> <img src="./assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark"> <img src="./assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark"> <img src="./assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo"> <img src="./assets/images/brand-logos/toggle-white.png" alt="logo" class="toggle-white"> <img src="./assets/images/brand-logos/desktop-white.png" alt="logo" class="desktop-white"> </a> </div>
            <!-- End::main-sidebar-header --> <!-- Start::main-sidebar --> 
            <div class="main-sidebar" id="sidebar-scroll" data-simplebar="init">
               <div class="simplebar-wrapper" style="margin: -8px 0px -80px;">
                  <div class="simplebar-height-auto-observer-wrapper">
                     <div class="simplebar-height-auto-observer"></div>
                  </div>
                  <div class="simplebar-mask">
                     <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                        <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                           <div class="simplebar-content" style="padding: 8px 0px 80px;">
                              <?php include ROOT . '/layout/menu.php'; ?>
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
         <!-- START MAINCONTENT --> 
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
                           <div class="download-data"> <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button> <button type="button" class="ti-btn ti-btn-primary" id="download-pdf">Download PDF</button> </div>
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
      <script src="./assets/js/datatables_user.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
      <script src="./assets/js/custom.js"></script>
   </body>
</html>