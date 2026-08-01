<header class="app-header sticky" id="header">
    <div class="main-header-container container-fluid">
        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo">
                    <a class="header-logo" href="index.php">
                        <img alt="logo" class="desktop-logo" src="./assets/images/brand-logos/desktop-logo.png" />
                        <img alt="logo" class="toggle-dark" src="./assets/images/brand-logos/toggle-dark.png" />
                        <img alt="logo" class="desktop-dark" src="./assets/images/brand-logos/desktop-dark.png" />
                        <img alt="logo" class="toggle-logo" src="./assets/images/brand-logos/toggle-logo.png" />
                        <img alt="logo" class="toggle-white" src="./assets/images/brand-logos/toggle-white.png" />
                        <img alt="logo" class="desktop-white" src="./assets/images/brand-logos/desktop-white.png" />
                    </a>
                </div>
            </div>
            <div class="header-element mx-lg-0">
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                    data-bs-toggle="sidebar" href="javascript:void(0);">
                    <span></span>
                </a>
            </div>
            <div class="header-element header-search md:!block !hidden my-auto auto-complete-search">
                <div aria-expanded="false" aria-haspopup="true" aria-owns="autoComplete_list_1"
                    class="autoComplete_wrapper" role="combobox">
                    <input aria-autocomplete="both" aria-controls="autoComplete_list_1" autocapitalize="off"
                        autocomplete="off" class="header-search-bar form-control" id="header-search"
                        placeholder="Search anything here ..." type="text" />
                    <ul hidden="" id="autoComplete_list_1" role="listbox"></ul>
                </div>
                <a aria-label="anchor" class="header-search-icon border-0" href="javascript:void(0);">
                    <i class="ri-search-line"></i>
                </a>
            </div>
        </div>
        <?php include ROOT . '/layout/navbar.php'; ?>
    </div>
</header>
