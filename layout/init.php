<div class="hs-overlay hidden ti-offcanvas ti-offcanvas-right" id="hs-overlay-switcher" tabindex="-1">
    <div class="ti-offcanvas-header z-10 relative">
        <h5 class="ti-offcanvas-title">
            Switcher
        </h5>
        <button
            class="ti-btn flex-shrink-0 p-0 !mb-0 transition-none text-defaulttextcolor dark:text-defaulttextcolor/80 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
            data-hs-overlay="#hs-overlay-switcher" type="button">
            <span class="sr-only">
                Close modal
            </span>
            <i class="ri-close-circle-line leading-none text-lg">
            </i>
        </button>
    </div>

    <div class="ti-offcanvas-body !p-0 !border-b dark:border-white/10 z-10 relative !h-auto">
        <div aria-label="Tabs" class="flex rtl:space-x-reverse" role="tablist">
            <button aria-controls="switcher-1"
                class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10 active"
                data-hs-tab="#switcher-1" id="switcher-item-1" role="tab" type="button">
                Theme Style
            </button>
            <button aria-controls="switcher-2"
                class="hs-tab-active:bg-danger/20 w-full !py-2 !px-4 hs-tab-active:border-b-transparent text-[0.813rem] border-0 hs-tab-active:text-danger dark:hs-tab-active:bg-danger/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-danger -mb-px bg-white font-normal text-center text-defaulttextcolor dark:text-defaulttextcolor/80 rounded-none hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:hover:text-gray-300"
                data-hs-tab="#switcher-2" id="switcher-item-2" role="tab" type="button">
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
                        <input checked="" class="ti-form-radio" id="switcher-light-theme" name="theme-style"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-light-theme">
                            Light
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input class="ti-form-radio" id="switcher-dark-theme" name="theme-style" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-dark-theme">
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
                        <input checked="" class="ti-form-radio" id="switcher-ltr" name="direction" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-ltr">
                            LTR
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input class="ti-form-radio" id="switcher-rtl" name="direction" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-rtl">
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
                        <input checked="" class="ti-form-radio" id="switcher-vertical" name="navigation-style"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-vertical">
                            Vertical
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input class="ti-form-radio" id="switcher-horizontal" name="navigation-style" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-horizontal">
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
                        <input checked="" class="ti-form-radio" id="switcher-menu-click"
                            name="navigation-data-menu-styles" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-menu-click">
                            Menu Click
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-menu-hover" name="navigation-data-menu-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-menu-hover">
                            Menu Hover
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-icon-click" name="navigation-data-menu-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-icon-click">
                            Icon Click
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-icon-hover" name="navigation-data-menu-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-icon-hover">
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
                        <input checked="" class="ti-form-radio" id="switcher-default-menu" name="sidemenu-layout-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-default-menu">
                            Default Menu
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-closed-menu" name="sidemenu-layout-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-closed-menu">
                            Closed Menu
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-icontext-menu" name="sidemenu-layout-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-icontext-menu">
                            Icon Text
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-icon-overlay" name="sidemenu-layout-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-icon-overlay">
                            Icon Overlay
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-detached" name="sidemenu-layout-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-detached">
                            Detached
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-double-menu" name="sidemenu-layout-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-double-menu">
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
                        <input checked="" class="ti-form-radio" id="switcher-regular" name="data-page-styles"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-regular">
                            Regular
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-classic" name="data-page-styles" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-classic">
                            Classic
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-modern" name="data-page-styles" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-modern">
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
                        <input checked="" class="ti-form-radio" id="switcher-full-width" name="layout-width"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-full-width">
                            FullWidth
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-boxed" name="layout-width" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-boxed">
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
                        <input checked="" class="ti-form-radio" id="switcher-menu-fixed" name="data-menu-positions"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-menu-fixed">
                            Fixed
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-menu-scroll" name="data-menu-positions"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-menu-scroll">
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
                        <input checked="" class="ti-form-radio" id="switcher-header-fixed" name="data-header-positions"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-header-fixed">
                            Fixed
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-header-scroll" name="data-header-positions"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-header-scroll">
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
                        <input checked="" class="ti-form-radio" id="switcher-loader-enable" name="page-loader"
                            type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-loader-enable">
                            Enable
                        </label>
                    </div>
                    <div class="flex">
                        <input class="ti-form-radio" id="switcher-loader-disable" name="page-loader" type="radio" />
                        <label
                            class="text-[0.813rem] text-defaulttextcolor dark:text-defaulttextcolor/80 ms-2 font-normal"
                            for="switcher-loader-disable">
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
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-white" id="switcher-menu-light"
                            name="menu-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Light Menu
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input checked="" class="hs-tooltip-toggle ti-form-radio color-input color-dark"
                            id="switcher-menu-dark" name="menu-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Dark Menu
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-primary"
                            id="switcher-menu-primary" name="menu-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Color Menu
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient"
                            id="switcher-menu-gradient" name="menu-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Gradient Menu
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent"
                            id="switcher-menu-transparent" name="menu-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs !font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
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
                        <input checked="" class="hs-tooltip-toggle ti-form-radio color-input color-white !border"
                            id="switcher-header-light" name="header-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Light Header
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-dark" id="switcher-header-dark"
                            name="header-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Dark Header
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-primary"
                            id="switcher-header-primary" name="header-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Color Header
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient"
                            id="switcher-header-gradient" name="header-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
                            Gradient Header
                        </span>
                    </div>
                    <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select">
                        <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent"
                            id="switcher-header-transparent" name="header-colors" type="radio" />
                        <span
                            class="hs-tooltip-content ti-main-tooltip-content !py-1 !px-2 !bg-black text-xs font-medium !text-white shadow-sm dark:!bg-black"
                            data-popper-escaped="" data-popper-placement="bottom" data-popper-reference-hidden=""
                            role="tooltip"
                            style="position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 5px);">
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
                        <input class="ti-form-radio color-input color-primary-1" id="switcher-primary"
                            name="theme-primary" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-primary-2" id="switcher-primary1"
                            name="theme-primary" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-primary-3" id="switcher-primary2"
                            name="theme-primary" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-primary-4" id="switcher-primary3"
                            name="theme-primary" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-primary-5" id="switcher-primary4"
                            name="theme-primary" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select ps-0 mt-1 color-primary-light">
                        <div class="theme-container-primary">
                            <button class="">
                                nano
                            </button>
                        </div>
                        <div class="pickr-container-primary">
                            <div class="pickr">
                                <button aria-label="toggle color picker dialog" class="pcr-button" role="button"
                                    style="--pcr-color: rgba(92, 103, 247, 1);" type="button">
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
                        <input class="ti-form-radio color-input color-bg-1" id="switcher-background"
                            name="theme-background" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-bg-2" id="switcher-background1"
                            name="theme-background" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-bg-3" id="switcher-background2"
                            name="theme-background" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-bg-4" id="switcher-background3"
                            name="theme-background" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio color-input color-bg-5" id="switcher-background4"
                            name="theme-background" type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select ps-0 mt-1 color-bg-transparent">
                        <div class="theme-container-background hidden">
                            <button>
                                nano
                            </button>
                        </div>
                        <div class="pickr-container-background">
                            <div class="pickr">
                                <button aria-label="toggle color picker dialog" class="pcr-button" role="button"
                                    style="--pcr-color: rgba(92, 103, 247, 1);" type="button">
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
                        <input class="ti-form-radio bgimage-input bg-img1" id="switcher-bg-img" name="theme-images"
                            type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio bgimage-input bg-img2" id="switcher-bg-img1" name="theme-images"
                            type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio bgimage-input bg-img3" id="switcher-bg-img2" name="theme-images"
                            type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio bgimage-input bg-img4" id="switcher-bg-img3" name="theme-images"
                            type="radio" />
                    </div>
                    <div class="ti-form-radio switch-select">
                        <input class="ti-form-radio bgimage-input bg-img5" id="switcher-bg-img4" name="theme-images"
                            type="radio" />
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
    <img alt="" src="./assets/images/media/loader.svg" />
</div>