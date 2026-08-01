<aside class="app-sidebar" id="sidebar">
    <div class="main-sidebar-header">
        <a class="header-logo" href="index.php">
            <img alt="logo" class="desktop-logo" src="./assets/images/brand-logos/desktop-logo.png" />
            <img alt="logo" class="toggle-dark" src="./assets/images/brand-logos/toggle-dark.png" />
            <img alt="logo" class="desktop-dark" src="./assets/images/brand-logos/desktop-dark.png" />
            <img alt="logo" class="toggle-logo" src="./assets/images/brand-logos/toggle-logo.png" />
            <img alt="logo" class="toggle-white" src="./assets/images/brand-logos/toggle-white.png" />
            <img alt="logo" class="desktop-white" src="./assets/images/brand-logos/desktop-white.png" />
        </a>
    </div>
    <div class="main-sidebar" data-simplebar="init" id="sidebar-scroll">
        <div class="simplebar-wrapper" style="margin: -8px 0px -80px;">
            <div class="simplebar-height-auto-observer-wrapper">
                <div class="simplebar-height-auto-observer"></div>
            </div>
            <div class="simplebar-mask">
                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                    <div aria-label="scrollable content" class="simplebar-content-wrapper" role="region"
                        style="height: 100%; overflow: hidden scroll;" tabindex="0">
                        <div class="simplebar-content" style="padding: 8px 0px 80px;">
                            <?php include ROOT . '/layout/menu.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="simplebar-placeholder" style="width: auto; height: 1724px;"></div>
        </div>
        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
        </div>
        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
            <div class="simplebar-scrollbar"
                style="height: 25px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
        </div>
    </div>
</aside>
