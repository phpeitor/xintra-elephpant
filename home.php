<?php
  require_once __DIR__ . "/controller/check_session.php";
?>

<html bg-img="bgimg5" class="light" data-header-styles="light" data-menu-styles="dark" data-nav-layout="vertical" data-vertical-style="overlay" data-width="fullwidth" dir="ltr" lang="en" loader="disable">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
  <title>Xintra Elephant</title>
  <meta content="Tailwind Responsive Admin Web Dashboard HTML5 Template" name="Description"/>
  <meta content="Spruko Technologies Private Limited" name="Author"/>
  <meta content="tailwind template,tailwind dashboard,tailwind,tailwind admin template,dashboard,tailwind css templates,html dashboard template,tailwind dashboard template,dashboard tailwind,admin,html css templates,html dashboard,html css javascript templates,dashboard tailwind template,tailwind css dashboard" name="keywords"/>
  <script src="./assets/js/main.js"></script>
  <link href="./assets/css/styles.css" rel="stylesheet"/>
  <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet"/>
  <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet"/>
  <link href="./assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet"/>
  <link href="./assets/libs/@simonwep/pickr/themes/nano.min.css" rel="stylesheet"/>
  <link href="./assets/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet"/>
  <link href="./assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css"/>
  <meta content="no" http-equiv="imagetoolbar"/>
 </head>
 <body>
  
  <div class="hs-overlay hidden ti-offcanvas ti-offcanvas-right" id="hs-overlay-switcher" tabindex="-1">
   <div class="ti-offcanvas-header z-10 relative">
    <h5 class="ti-offcanvas-title">
     Switcher
    </h5>
    <button class="ti-btn flex-shrink-0 p-0 !mb-0 transition-none text-defaulttextcolor dark:text-defaulttextcolor/80 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10" data-hs-overlay="#hs-overlay-switcher" type="button">
     <span class="sr-only">
      Close modal
     </span>
     <i class="ri-close-circle-line leading-none text-lg">
     </i>
    </button>
   </div>
   <div class="ti-offcanvas-body !p-0 !border-b dark:border-white/10 z-10 relative !h-auto">
    <div aria-label="Tabs" class="flex rtl:space-x-reverse" role="tablist">
     <button aria-controls="switcher-1" class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10 active" data-hs-tab="#switcher-1" id="switcher-item-1" role="tab" type="button">
      Theme Style
     </button>
     <button aria-controls="switcher-2" class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:hover:text-gray-300" data-hs-tab="#switcher-2" id="switcher-item-2" role="tab" type="button">
      Theme Colors
     </button>
    </div>
   </div>
   <div class="ti-offcanvas-body !p-0 !pb-[20rem]" id="switcher-body">
    <div aria-labelledby="switcher-item-1" class="" id="switcher-1" role="tabpanel">
     <div class="">
      <p class="switcher-style-head">
       Theme Color Mode:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex items-center">
        <input checked="" class="ti-form-radio" id="switcher-light-theme" name="theme-style" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-light-theme">
         Light
        </label>
       </div>
       <div class="flex items-center">
        <input class="ti-form-radio" id="switcher-dark-theme" name="theme-style" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-dark-theme">
         Dark
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Directions:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex items-center">
        <input checked="" class="ti-form-radio" id="switcher-ltr" name="direction" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-ltr">
         LTR
        </label>
       </div>
       <div class="flex items-center">
        <input class="ti-form-radio" id="switcher-rtl" name="direction" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-rtl">
         RTL
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Navigation Styles:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex items-center">
        <input checked="" class="ti-form-radio" id="switcher-vertical" name="navigation-style" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-vertical">
         Vertical
        </label>
       </div>
       <div class="flex items-center">
        <input class="ti-form-radio" id="switcher-horizontal" name="navigation-style" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-horizontal">
         Horizontal
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Navigation Menu Style:
      </p>
      <div class="grid grid-cols-2 gap-2 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-menu-click" name="navigation-data-menu-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-menu-click">
         Menu Click
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-menu-hover" name="navigation-data-menu-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-menu-hover">
         Menu Hover
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-icon-click" name="navigation-data-menu-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-icon-click">
         Icon Click
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-icon-hover" name="navigation-data-menu-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-icon-hover">
         Icon Hover
        </label>
       </div>
      </div>
     </div>
     <div class="sidemenu-layout-styles">
      <p class="switcher-style-head">
       Sidemenu Layout Syles:
      </p>
      <div class="grid grid-cols-2 gap-2 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-default-menu" name="sidemenu-layout-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-default-menu">
         Default Menu
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-closed-menu" name="sidemenu-layout-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-closed-menu">
         Closed Menu
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-icontext-menu" name="sidemenu-layout-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-icontext-menu">
         Icon Text
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-icon-overlay" name="sidemenu-layout-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-icon-overlay">
         Icon Overlay
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-detached" name="sidemenu-layout-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-detached">
         Detached
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-double-menu" name="sidemenu-layout-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-double-menu">
         Double Menu
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Page Styles:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-regular" name="data-page-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-regular">
         Regular
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-classic" name="data-page-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-classic">
         Classic
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-modern" name="data-page-styles" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-modern">
         Modern
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Layout Width Styles:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-full-width" name="layout-width" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-full-width">
         FullWidth
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-boxed" name="layout-width" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-boxed">
         Boxed
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Menu Positions:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-menu-fixed" name="data-menu-positions" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-menu-fixed">
         Fixed
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-menu-scroll" name="data-menu-positions" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-menu-scroll">
         Scrollable
        </label>
       </div>
      </div>
     </div>
     <div>
      <p class="switcher-style-head">
       Header Positions:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-header-fixed" name="data-header-positions" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-header-fixed">
         Fixed
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-header-scroll" name="data-header-positions" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-header-scroll">
         Scrollable
        </label>
       </div>
      </div>
     </div>
     <div class="">
      <p class="switcher-style-head">
       Loader:
      </p>
      <div class="grid grid-cols-3 switcher-style">
       <div class="flex">
        <input checked="" class="ti-form-radio" id="switcher-loader-enable" name="page-loader" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-loader-enable">
         Enable
        </label>
       </div>
       <div class="flex">
        <input class="ti-form-radio" id="switcher-loader-disable" name="page-loader" type="radio"/>
        <label class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal" for="switcher-loader-disable">
         Disable
        </label>
       </div>
      </div>
     </div>
    </div>
    <div aria-labelledby="switcher-item-2" class="hidden" id="switcher-2" role="tabpanel">
     <div class="theme-colors">
      <p class="switcher-style-head">
       Menu Colors:
      </p>
      <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-white" id="switcher-menu-light" name="menu-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Light Menu
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input checked="" class="hs-tooltip-toggle ti-form-radio color-input color-dark" id="switcher-menu-dark" name="menu-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Dark Menu
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-primary" id="switcher-menu-primary" name="menu-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Color Menu
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient" id="switcher-menu-gradient" name="menu-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Gradient Menu
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent" id="switcher-menu-transparent" name="menu-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs !font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Transparent Menu
        </span>
       </div>
      </div>
      <div class="px-4 text-textmuted dark:text-textmuted/50 text-[.6875rem]">
       <b class="me-2 font-normal">
        Note:
       </b>
       If you want to change color Menu dynamically change from below Theme Primary color picker.
      </div>
     </div>
     <div class="theme-colors">
      <p class="switcher-style-head">
       Header Colors:
      </p>
      <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input checked="" class="hs-tooltip-toggle ti-form-radio color-input color-white !border" id="switcher-header-light" name="header-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Light Header
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-dark" id="switcher-header-dark" name="header-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Dark Header
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-primary" id="switcher-header-primary" name="header-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Color Header
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient" id="switcher-header-gradient" name="header-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Gradient Header
        </span>
       </div>
       <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
        <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent" id="switcher-header-transparent" name="header-colors" type="radio"/>
        <span class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black" data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden="" role="tooltip" style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
         Transparent Header
        </span>
       </div>
      </div>
      <div class="px-4 text-textmuted dark:text-textmuted/50 text-[.6875rem]">
       <b class="me-2 !font-normal">
        Note:
       </b>
       If you want to change color Header dynamically change from below Theme Primary color picker.
      </div>
     </div>
     <div class="theme-colors">
      <p class="switcher-style-head">
       Theme Primary:
      </p>
      <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-primary-1" id="switcher-primary" name="theme-primary" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-primary-2" id="switcher-primary1" name="theme-primary" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-primary-3" id="switcher-primary2" name="theme-primary" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-primary-4" id="switcher-primary3" name="theme-primary" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-primary-5" id="switcher-primary4" name="theme-primary" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select ps-0 mt-1 color-primary-light">
        <div class="theme-container-primary">
         <button class="">
          nano
         </button>
        </div>
        <div class="pickr-container-primary">
         <div class="pickr">
          <button aria-label="toggle color picker dialog" class="pcr-button" role="button" style="--pcr-color: rgba(92, 103, 247, 1);" type="button">
          </button>
         </div>
        </div>
       </div>
      </div>
     </div>
     <div class="theme-colors">
      <p class="switcher-style-head">
       Theme Background:
      </p>
      <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-bg-1" id="switcher-background" name="theme-background" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-bg-2" id="switcher-background1" name="theme-background" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-bg-3" id="switcher-background2" name="theme-background" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-bg-4" id="switcher-background3" name="theme-background" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio color-input color-bg-5" id="switcher-background4" name="theme-background" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select ps-0 mt-1 color-bg-transparent">
        <div class="theme-container-background hidden">
         <button>
          nano
         </button>
        </div>
        <div class="pickr-container-background">
         <div class="pickr">
          <button aria-label="toggle color picker dialog" class="pcr-button" role="button" style="--pcr-color: rgba(92, 103, 247, 1);" type="button">
          </button>
         </div>
        </div>
       </div>
      </div>
     </div>
     <div class="menu-image theme-colors">
      <p class="switcher-style-head">
       Menu With Background Image:
      </p>
      <div class="flex switcher-style space-x-3 rtl:space-x-reverse flex-wrap gap-3">
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio bgimage-input bg-img1" id="switcher-bg-img" name="theme-images" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio bgimage-input bg-img2" id="switcher-bg-img1" name="theme-images" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio bgimage-input bg-img3" id="switcher-bg-img2" name="theme-images" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio bgimage-input bg-img4" id="switcher-bg-img3" name="theme-images" type="radio"/>
       </div>
       <div class="ti-form-radio switch-select">
        <input class="ti-form-radio bgimage-input bg-img5" id="switcher-bg-img4" name="theme-images" type="radio"/>
       </div>
      </div>
     </div>
    </div>
   </div>
   <div class="ti-offcanvas-footer sm:flex justify-between">
    <a class="ti-btn ti-btn-danger m-1" href="javascript:void(0);" id="reset-all">
     Reset
    </a>
   </div>
  </div>
  <div class="loader-disable" id="loader">
   <img alt="" src="./assets/images/media/loader.svg"/>
  </div>
  <div class="page">
   <header class="app-header sticky" id="header">
    <div class="main-header-container container-fluid">
     <div class="header-content-left">
      <div class="header-element">
       <div class="horizontal-logo">
        <a class="header-logo" href="index.php">
         <img alt="logo" class="desktop-logo" src="./assets/images/brand-logos/desktop-logo.png"/>
         <img alt="logo" class="toggle-dark" src="./assets/images/brand-logos/toggle-dark.png"/>
         <img alt="logo" class="desktop-dark" src="./assets/images/brand-logos/desktop-dark.png"/>
         <img alt="logo" class="toggle-logo" src="./assets/images/brand-logos/toggle-logo.png"/>
         <img alt="logo" class="toggle-white" src="./assets/images/brand-logos/toggle-white.png"/>
         <img alt="logo" class="desktop-white" src="./assets/images/brand-logos/desktop-white.png"/>
        </a>
       </div>
      </div>
      <div class="header-element mx-lg-0">
       <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);">
        <span>
        </span>
       </a>
      </div>
      <div class="header-element header-search md:!block !hidden my-auto auto-complete-search">
       <div aria-expanded="false" aria-haspopup="true" aria-owns="autoComplete_list_1" class="autoComplete_wrapper" role="combobox">
        <input aria-autocomplete="both" aria-controls="autoComplete_list_1" autocapitalize="off" autocomplete="off" class="header-search-bar form-control" id="header-search" placeholder="Search anything here ..." type="text"/>
        <ul hidden="" id="autoComplete_list_1" role="listbox">
        </ul>
       </div>
       <a aria-label="anchor" class="header-search-icon border-0" href="javascript:void(0);">
        <i class="ri-search-line">
        </i>
       </a>
      </div>
     </div>
     <?php include __DIR__ . '/navbar.php'; ?>
    </div>
   </header>
   <aside class="app-sidebar" id="sidebar">
    <div class="main-sidebar-header">
     <a class="header-logo" href="index.php">
      <img alt="logo" class="desktop-logo" src="./assets/images/brand-logos/desktop-logo.png"/>
      <img alt="logo" class="toggle-dark" src="./assets/images/brand-logos/toggle-dark.png"/>
      <img alt="logo" class="desktop-dark" src="./assets/images/brand-logos/desktop-dark.png"/>
      <img alt="logo" class="toggle-logo" src="./assets/images/brand-logos/toggle-logo.png"/>
      <img alt="logo" class="toggle-white" src="./assets/images/brand-logos/toggle-white.png"/>
      <img alt="logo" class="desktop-white" src="./assets/images/brand-logos/desktop-white.png"/>
     </a>
    </div>
    <div class="main-sidebar" data-simplebar="init" id="sidebar-scroll">
     <div class="simplebar-wrapper" style="margin: -8px 0px -80px;">
      <div class="simplebar-height-auto-observer-wrapper">
       <div class="simplebar-height-auto-observer">
       </div>
      </div>
      <div class="simplebar-mask">
       <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
        <div aria-label="scrollable content" class="simplebar-content-wrapper" role="region" style="height: 100%; overflow: hidden scroll;" tabindex="0">
         <div class="simplebar-content" style="padding: 8px 0px 80px;">
            <?php include __DIR__ . '/menu.php'; ?>
         </div>
        </div>
       </div>
      </div>
      <div class="simplebar-placeholder" style="width: auto; height: 1724px;">
      </div>
     </div>
     <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
      <div class="simplebar-scrollbar" style="width: 0px; display: none;">
      </div>
     </div>
     <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
      <div class="simplebar-scrollbar" style="height: 25px; transform: translate3d(0px, 0px, 0px); display: block;">
      </div>
     </div>
    </div>
   </aside>
   
   <div class="main-content app-content">
    <div class="container-fluid">

      <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
        <div>
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
              <a href="javascript:void(0);"> Dashboards </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Sales</li>
          </ol>
          <h1 class="page-title font-medium text-lg mb-0">Sales Dashboard</h1>
        </div>
        <div class="flex gap-2 flex-wrap">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-text bg-white dark:bg-bodybg border">
                <i class="ri-calendar-line"></i>
              </div>
              <input type="text" class="form-control breadcrumb-input flatpickr-input" id="daterange" placeholder="Search By Date Range" readonly="readonly">
            </div>
          </div>
          <div class="ti-btn-list">
            <button class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 !m-0 !me-[0.35rem] waves-effect waves-light">
              <i class="ri-filter-3-line align-middle leading-none"></i> Filter </button>
            <button class="ti-btn ti-btn-primary btn-wave !border-0 me-0 !m-0 waves-effect waves-light">
              <i class="ri-share-forward-line"></i> Share </button>
          </div>
        </div>
      </div>
      <!-- ROW-1 -->
      <div class="grid grid-cols-12 gap-x-6">
        <div class="xl:col-span-8 col-span-12">
          <div class="grid grid-cols-12 gap-x-6">
            <div class="xxl:col-span-3 xl:col-span-6 col-span-12">
              <div class="box overflow-hidden main-content-card">
                <div class="box-body">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <span class="text-textmuted dark:text-textmuted/50 block mb-1">Total Productos</span>
                      <h4 class="font-medium mb-0" id="total_producto">0</h4>
                    </div>
                    <div class="leading-none">
                      <span class="avatar avatar-md avatar-rounded bg-primary">
                        <i class="ti ti-shopping-cart text-[1.25rem]"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="xxl:col-span-3 xl:col-span-6 col-span-12">
              <div class="box overflow-hidden main-content-card">
                <div class="box-body">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <span class="block text-textmuted dark:text-textmuted/50 mb-1">Total Usuarios</span>
                      <h4 class="font-medium mb-0" id="total_usuario">0</h4>
                    </div>
                    <div class="leading-none">
                      <span class="avatar avatar-md avatar-rounded bg-primarytint1color">
                        <i class="ti ti-users text-[1.25rem]"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="xxl:col-span-3 xl:col-span-6 col-span-12">
              <div class="box overflow-hidden main-content-card">
                <div class="box-body">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <span class="text-textmuted dark:text-textmuted/50 block mb-1">Total Tickets</span>
                      <h4 class="font-medium mb-0" id="total_ticket">0</h4>
                    </div>
                    <div class="leading-none">
                      <span class="avatar avatar-md avatar-rounded bg-primarytint2color">
                        <i class="ti ti-currency-dollar text-[1.25rem]"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="xxl:col-span-3 xl:col-span-6 col-span-12">
              <div class="box overflow-hidden main-content-card">
                <div class="box-body">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <span class="text-textmuted dark:text-textmuted/50 block mb-1">Total Servicios</span>
                      <h4 class="font-medium mb-0" id="total_servicio">0</h4>
                    </div>
                    <div class="leading-none">
                      <span class="avatar avatar-md avatar-rounded bg-primarytint3color">
                        <i class="ti ti-chart-bar text-[1.25rem]"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="xxl:col-span-8 xl:col-span-6 col-span-12">
              <div class="box">
                <div class="box-header justify-between">
                  <div class="box-title"> Total Sales  </div>
                  <div class="ti-dropdown hs-dropdown">
                    <a href="javascript:void(0);" class="ti-btn ti-btn-light ti-btn-sm text-textmuted dark:text-textmuted/50 ti-dropdown-toggle hs-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> Sort By <i class="ri-arrow-down-s-line align-middle fs-13 d-inline-block"></i>
                    </a>
                    <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu" data-popper-placement="bottom-end">
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);">This Week</a>
                      </li>
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);">Last Week</a>
                      </li>
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);">This Month</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="box-body">
                  <div id="sales-overview" class="" style="min-height: 333px;">
                    
                  </div>
                </div>
              </div>
            </div>
            
            <div class="xxl:col-span-4 xl:col-span-6 col-span-12">
              <div class="box overflow-hidden">
                <div class="box-header pb-0 justify-between">
                  <div class="box-title"> Total Statistics </div>
                  <div class="ti-dropdown hs-dropdown">
                    <a aria-label="anchor" href="javascript:void(0);" class="ti-btn ti-btn-light ti-btn-sm ti-btn-icon text-textmuted dark:text-textmuted/50 hs-dropdown-toggle ti-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fe fe-more-vertical"></i>
                    </a>
                    <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu">
                      <li class="ti-dropdown-item">
                        <a href="javascript:void(0);">Today</a>
                      </li>
                      <li class="ti-dropdown-item">
                        <a href="javascript:void(0);">This Week</a>
                      </li>
                      <li class="ti-dropdown-item">
                        <a href="javascript:void(0);">Last Week</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="box-body py-4 px-3">
                  <div class="flex gap-4 mb-3">
                    <div class="avatar avatar-md bg-primary/10 !w-[3rem]">
                      <i class="ti ti-trending-up text-[1.25rem] text-primary"></i>
                    </div>
                    <div class="flex-auto flex items-start justify-between w-full flex-wrap">
                      <div>
                        <span class="text-[11px] mb-1 block font-medium">Productividad Usuarios</span>
                        <div class="flex items-center justify-between">
                          <h4 class="mb-0 flex items-center" id="total_item">0 
                            <span class="text-success text-xs ms-2 inline-flex items-center" id="porcentaje_item">
                              <i class="ti ti-trending-up align-middle me-1"></i>0% </span>
                          </h4>
                        </div>
                      </div>
                      <a href="javascript:void(0);" class="text-success text-xs decoration-solid">Earnings ?</a>
                    </div>
                  </div>
                  <div id="orders" class="my-2" style="min-height: 188.8px;">
                    
                  </div>
                </div>

                <div class="box-footer border-t border-dashed">
                  <div class="grid">
                    <button class="ti-btn ti-btn-outline-primary ti-btn-wave btn-wave font-medium waves-effect waves-light table-icon">Complete Statistics <i class="ti ti-arrow-narrow-right ms-2 text-[16px] inline-block"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="xl:col-span-4 col-span-12">
          <div class="grid grid-cols-12 gap-x-6">
            <div class="xl:col-span12 col-span-12">
              <div class="box main-dashboard-banner overflow-hidden">
                <div class="box-body p-6">
                  <div class="grid grid-cols-12 justify-between">
                    <div class="xxl:col-span-7 xl:col-span-5 lg:col-span-5 md:col-span-5 sm:col-span-5 col-span-12">
                      <h4 class="mb-4 font-medium text-white">Actualiza a Profesional</h4>
                      <p class="mb-6 text-white">Maximiza la información sobre ventas. Optimiza el rendimiento.</p>
                      <a href="javascript:void(0);" class="font-medium text-white decoration-solid underline">Obtener más información <i class="ti ti-arrow-narrow-right"></i>
                      </a>
                    </div>
                    <div class="xxl:col-span-4 xl:col-span-7 lg:col-span-7 md:col-span-7 sm:col-span-7 sm:block hidden text-end my-auto col-span-12">
                      <img src="./assets/images/media/media-86.png" alt="" class="img-fluid">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="xl:col-span12 col-span-12">
              <div class="box overflow-hidden">
                <div class="box-header justify-between pb-1">
                  <div class="box-title"> Top Selling Usuarios </div>
                  <div class="ti-dropdown hs-dropdown">
                    <a href="javascript:void(0);" class="ti-btn ti-btn-light text-textmuted dark:text-textmuted/50 ti-dropdown-toggle hs-dropdown-toggle ti-btn-sm gap-0" data-bs-toggle="dropdown" aria-expanded="false"> Sort By <i class="ri-arrow-down-s-line align-middle ms-1 inline-block"></i>
                    </a>
                    <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu" data-popper-placement="bottom-end">
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);">This Week</a>
                      </li>
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);">Last Week</a>
                      </li>
                      <li>
                        <a class="ti-dropdown-item" href="javascript:void(0);">This Month</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="box-body p-0">
                  <div class="p-4 pb-0">
                    <div class="progress-stacked progress-sm mb-3 flex gap-1">
                      <div class="progress-bar w-[25%]" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      <div class="progress-bar bg-primarytint1color w-[15%] !rounded-none" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                      <div class="progress-bar bg-primarytint2color !rounded-none w-[15%]" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      <div class="progress-bar bg-primarytint3color !rounded-none w-[20%]" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                      <div class="progress-bar !rounded-none !rounded-tr-md !rounded-br-md bg-secondary w-[25%]" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                      <div>Overall Sales</div>
                      <div class="h6 mb-0">
                        <span class="text-success me-2 text-[11px]" id="porcentaje_actual" >0% <i class="ti ti-arrow-narrow-up"></i>
                        </span><span id="total_actual">0</span>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive top-categories">
                    <table class="table text-nowrap">
                      <tbody>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
















     
    </div>
   </div>

   <footer class="mt-auto py-4 bg-white dark:bg-bodybg text-center border-t border-defaultborder dark:border-defaultborder/10">
    <div class="container">
     <span class="text-textmuted dark:text-textmuted/50">
      Copyright &copy;
      <span id="year">
       2025
      </span>
      <a class="text-dark font-medium" href="javascript:void(0);">
       Xintra
      </a>
      . Designed with
      <span class="text-danger">
       ❤
      </span>
      by
      <a href="https://www.instagram.com/amvsoft.tech/" target="_blank">
       <span class="font-medium text-primary">
        Spruko
       </span>
      </a>
      All rights reserved
     </span>
    </div>
   </footer>
   <div aria-labelledby="header-responsive-search" class="hs-overlay ti-modal hidden" id="header-responsive-search" tabindex="-1">
    <div class="ti-modal-box">
     <div class="ti-modal-dialog">
      <div class="ti-modal-content">
       <div class="ti-modal-body">
        <div class="input-group">
         <input aria-describedby="button-addon2" aria-label="Search Anything ..." class="form-control border-end-0 !border-s" placeholder="Search Anything ..." type="text"/>
         <button aria-label="button" class="ti-btn ti-btn-primary !m-0" id="button-addon2" type="button">
          <i class="bi bi-search">
          </i>
         </button>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
  <div class="scrollToTop">
   <span class="arrow">
    <i class="ti ti-arrow-narrow-up text-xl">
    </i>
   </span>
  </div>
  <div id="responsive-overlay">
  </div>

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
  <script src="./assets/libs/apexcharts/apexcharts.min.js"></script>
  <script src="./assets/js/sales-dashboard.js?v=1.9"></script>
  <svg height="0" id="SvgjsSvg1006" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;" version="1.1" width="2" xmlns="http://www.w3.org/2000/svg" xmlns:svgjs="http://svgjs.dev" xmlns:xlink="http://www.w3.org/1999/xlink">
   <defs id="SvgjsDefs1007">
   </defs>
   <polyline id="SvgjsPolyline1008" points="0,0">
   </polyline>
   <path d="M0 0 " id="SvgjsPath1009">
   </path>
  </svg>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
  <script src="./assets/js/custom.js?v=1"></script>
  <div class="flatpickr-calendar rangeMode animate" tabindex="-1">
    <div class="flatpickr-months">
      <span class="flatpickr-prev-month">
        <svg version="1.1" viewbox="0 0 17 17" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
          <g>
          </g>
          <path d="M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z">
          </path>
        </svg>
      </span>
      
      <span class="flatpickr-next-month">
        <svg version="1.1" viewbox="0 0 17 17" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
          <g>
          </g>
          <path d="M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z">
          </path>
        </svg>
      </span>
    </div>
  </div>

  <div aria-label="color picker dialog" class="pcr-app" data-theme="nano" role="window" style="left: 0px; top: 8px;">
   <div class="pcr-selection">
    <div class="pcr-color-preview">
     <button aria-label="use previous color" class="pcr-last-color" style="--pcr-color: rgba(92, 103, 247, 1);" type="button">
     </button>
     <div class="pcr-current-color" style="--pcr-color: rgba(92, 103, 247, 1);">
     </div>
    </div>
    <div class="pcr-color-palette">
     <div class="pcr-picker" style="left: calc(62.753% - 9px); top: calc(3.13725% - 9px); background: rgb(92, 103, 247);">
     </div>
     <div aria-label="color selection area" class="pcr-palette" role="listbox" style="background: linear-gradient(to top, rgb(0, 0, 0), transparent), linear-gradient(to left, rgb(0, 18, 255), rgb(255, 255, 255));" tabindex="0">
     </div>
    </div>
    <div class="pcr-color-chooser">
     <div class="pcr-picker" style="left: calc(65.4839% - 9px); background-color: rgb(0, 18, 255);">
     </div>
     <div aria-label="hue selection slider" class="pcr-hue pcr-slider" role="slider" tabindex="0">
     </div>
    </div>
    <div class="pcr-color-opacity" hidden="" style="display:none">
     <div class="pcr-picker">
     </div>
     <div aria-label="selection slider" class="pcr-opacity pcr-slider" role="slider" tabindex="0">
     </div>
    </div>
   </div>
   <div class="pcr-swatches">
   </div>
   <div class="pcr-interaction">
    <input aria-label="color input field" class="pcr-result" spellcheck="false" type="text"/>
    <input class="pcr-type" data-type="HEXA" hidden="" style="display:none" type="button" value="HEXA"/>
    <input class="pcr-type active" data-type="RGBA" type="button" value="RGBA"/>
    <input class="pcr-type" data-type="HSLA" hidden="" style="display:none" type="button" value="HSLA"/>
    <input class="pcr-type" data-type="HSVA" hidden="" style="display:none" type="button" value="HSVA"/>
    <input class="pcr-type" data-type="CMYK" hidden="" style="display:none" type="button" value="CMYK"/>
    <input aria-label="save and close" class="pcr-save" hidden="" style="display:none" type="button" value="Save"/>
    <input aria-label="cancel and close" class="pcr-cancel" hidden="" style="display:none" type="button" value="Cancel"/>
    <input aria-label="clear and close" class="pcr-clear" hidden="" style="display:none" type="button" value="Clear"/>
   </div>
  </div>
  <div aria-label="color picker dialog" class="pcr-app" data-theme="nano" role="window" style="left: 0px; top: 8px;">
   <div class="pcr-selection">
    <div class="pcr-color-preview">
     <button aria-label="use previous color" class="pcr-last-color" style="--pcr-color: rgba(92, 103, 247, 1);" type="button">
     </button>
     <div class="pcr-current-color" style="--pcr-color: rgba(92, 103, 247, 1);">
     </div>
    </div>
    <div class="pcr-color-palette">
     <div class="pcr-picker" style="left: calc(62.753% - 9px); top: calc(3.13725% - 9px); background: rgb(92, 103, 247);">
     </div>
     <div aria-label="color selection area" class="pcr-palette" role="listbox" style="background: linear-gradient(to top, rgb(0, 0, 0), transparent), linear-gradient(to left, rgb(0, 18, 255), rgb(255, 255, 255));" tabindex="0">
     </div>
    </div>
    <div class="pcr-color-chooser">
     <div class="pcr-picker" style="left: calc(65.4839% - 9px); background-color: rgb(0, 18, 255);">
     </div>
     <div aria-label="hue selection slider" class="pcr-hue pcr-slider" role="slider" tabindex="0">
     </div>
    </div>
    <div class="pcr-color-opacity" hidden="" style="display:none">
     <div class="pcr-picker">
     </div>
     <div aria-label="selection slider" class="pcr-opacity pcr-slider" role="slider" tabindex="0">
     </div>
    </div>
   </div>
   <div class="pcr-swatches">
   </div>
   <div class="pcr-interaction">
    <input aria-label="color input field" class="pcr-result" spellcheck="false" type="text"/>
    <input class="pcr-type" data-type="HEXA" hidden="" style="display:none" type="button" value="HEXA"/>
    <input class="pcr-type active" data-type="RGBA" type="button" value="RGBA"/>
    <input class="pcr-type" data-type="HSLA" hidden="" style="display:none" type="button" value="HSLA"/>
    <input class="pcr-type" data-type="HSVA" hidden="" style="display:none" type="button" value="HSVA"/>
    <input class="pcr-type" data-type="CMYK" hidden="" style="display:none" type="button" value="CMYK"/>
    <input aria-label="save and close" class="pcr-save" hidden="" style="display:none" type="button" value="Save"/>
    <input aria-label="cancel and close" class="pcr-cancel" hidden="" style="display:none" type="button" value="Cancel"/>
    <input aria-label="clear and close" class="pcr-clear" hidden="" style="display:none" type="button" value="Clear"/>
   </div>
  </div>

  <script src="./assets/js/custom-switcher.min.js">
  </script>
 </body>
</html>
