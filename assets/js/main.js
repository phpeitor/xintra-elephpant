(function () {
    'use strict';
    if (localStorage.getItem("xyntradarktheme")) {
        document.querySelector("html").setAttribute("class", "dark")
        document.querySelector("html").setAttribute("data-menu-styles", "dark")
        document.querySelector("html").setAttribute("data-header-styles", "transparent")
    }
    if (localStorage.xyntrartl) {
        let html = document.querySelector('html');
        html.setAttribute("dir", "rtl");
    }
    if (localStorage.xyntralayout) {
        let html = document.querySelector('html');
        html.setAttribute("data-nav-layout", "horizontal");
        document.querySelector("html").setAttribute("data-menu-styles", "light")
    }
    if (localStorage.getItem("xyntralayout") == "horizontal") {
        document.querySelector("html").setAttribute("data-nav-layout", "horizontal")
    }
    if (localStorage.loaderEnable == 'true') {
        document.querySelector("html").setAttribute("loader", "enable");
    } else {
        if (!document.querySelector("html").getAttribute("loader")) {
            document.querySelector("html").setAttribute("loader", "disable");
        }
    }

    function localStorageBackup() {

        // if there is a value stored, update color picker and background color
        // Used to retrive the data from local storage
        if (localStorage.primaryRGB) {
            document
                .querySelector("html")
                .style.setProperty("--primary", localStorage.primaryRGB1);
            document
                .querySelector("html")
                .style.setProperty("--primary-rgb", localStorage.primaryRGB);
        }
        if (localStorage.bodyBgRGB) {
            document
              .querySelector("html")
              .style.setProperty("--body-bg", localStorage.bodyBgRGB);
            document
              .querySelector("html")
              .style.setProperty("--dark-bg", localStorage.darkBgRGB);
              document
                .querySelector("html")
                .style.setProperty("--light", localStorage.darkBgRGB);
            let html = document.querySelector("html");
            html.classList.add("dark");
            html.classList.remove("light");
            html.setAttribute("data-menu-styles", "dark");
            html.setAttribute("data-header-styles", "transparent");
          }
        if (localStorage.xyntradarktheme) {
            let html = document.querySelector('html');
            html.setAttribute('class', 'dark');
        }
        if (localStorage.xyntralayout) {
            let html = document.querySelector('html');
            let layoutValue = localStorage.getItem('xyntralayout');
            html.setAttribute('data-nav-layout', 'horizontal');
            setTimeout(() => {
                clearNavDropdown();
            }, 5000);
            html.setAttribute('data-nav-style', 'menu-click');
            setTimeout(() => {
                checkHoriMenu();
            }, 5000);
        }
        if (localStorage.xyntraverticalstyles) {
            let html = document.querySelector('html');
            let verticalStyles = localStorage.getItem('xyntraverticalstyles');

            if (verticalStyles == 'default') {
                html.setAttribute('data-vertical-style', 'default');
                localStorage.removeItem("xyntranavstyles")
            }
            if (verticalStyles == 'closed') {
                html.setAttribute('data-vertical-style', 'closed');
                localStorage.removeItem("xyntranavstyles")
            }
            if (verticalStyles == 'icontext') {
                html.setAttribute('data-vertical-style', 'icontext');
                localStorage.removeItem("xyntranavstyles")
            }
            if (verticalStyles == 'overlay') {
                html.setAttribute('data-vertical-style', 'overlay');
                localStorage.removeItem("xyntranavstyles")
            }
            if (verticalStyles == 'detached') {
                html.setAttribute('data-vertical-style', 'detached');
                localStorage.removeItem("xyntranavstyles")
            }
            if (verticalStyles == 'doublemenu') {
                html.setAttribute('data-vertical-style', 'doublemenu');
                localStorage.removeItem("xyntranavstyles")
                setTimeout(() => {

                    const menuSlideItem = document.querySelectorAll(
                        ".main-menu > li > .side-menu__item"
                    );

                    // Create the tooltip element
                    const tooltip = document.createElement("div");
                    tooltip.className = "custome-tooltip";
                    // tooltip.textContent = "This is a tooltip";

                    // Set the CSS properties of the tooltip element
                    tooltip.style.setProperty("position", "fixed");
                    tooltip.style.setProperty("display", "none");
                    tooltip.style.setProperty("padding", "0.5rem");
                    tooltip.style.setProperty("font-weight", "500");
                    tooltip.style.setProperty("font-size", "0.75rem");
                    tooltip.style.setProperty("background-color", "rgb(15, 23 ,42)");
                    tooltip.style.setProperty("color", "rgb(255, 255 ,255)");
                    tooltip.style.setProperty("margin-inline-start", "45px");
                    tooltip.style.setProperty("border-radius", "0.25rem");
                    tooltip.style.setProperty("z-index", "99");

                    menuSlideItem.forEach((e) => {
                        // Add an event listener to the menu slide item to show the tooltip
                        e.addEventListener("mouseenter", () => {
                            tooltip.style.setProperty("display", "block");
                            tooltip.textContent =
                                e.querySelector(".side-menu__label").textContent;
                            if (document.querySelector("html").getAttribute("data-vertical-style") == "doublemenu") {
                                e.appendChild(tooltip);
                            }
                        });

                        // Add an event listener to hide the tooltip
                        e.addEventListener("mouseleave", () => {
                            tooltip.style.setProperty("display", "none");
                            tooltip.textContent = e.querySelector(".side-menu__label").textContent;

                        });
                    });
                }, 1000);
            }
        }
        if (localStorage.xyntranavstyles) {
            let html = document.querySelector('html');
            let navStyles = localStorage.getItem('xyntranavstyles');
            if (navStyles == 'menu-click') {
                html.setAttribute('data-nav-style', 'menu-click');
                localStorage.removeItem("xyntraverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
            if (navStyles == 'menu-hover') {
                html.setAttribute('data-nav-style', 'menu-hover');
                localStorage.removeItem("xyntraverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
            if (navStyles == 'icon-click') {
                html.setAttribute('data-nav-style', 'icon-click');
                localStorage.removeItem("xyntraverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
            if (navStyles == 'icon-hover') {
                html.setAttribute('data-nav-style', 'icon-hover');
                localStorage.removeItem("xyntraverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
        }
        if (localStorage.xyntraclassic) {
            let html = document.querySelector('html');
            html.setAttribute('data-page-style', 'classic');
        }
        if (localStorage.xyntramodern) {
            let html = document.querySelector('html');
            html.setAttribute('data-page-style', 'modern');
        }
        if (localStorage.xyntraboxed) {
            let html = document.querySelector('html');
            html.setAttribute('data-width', 'boxed');
        }
        if (localStorage.xyntrafullwidth) {
            let html = document.querySelector('html');
            html.setAttribute('data-width', 'fullwidth');
        }
        if (localStorage.xyntraheaderfixed) {
            let html = document.querySelector('html');
            html.setAttribute('data-header-position', 'fixed');
        }
        if (localStorage.xyntraheaderscrollable) {
            let html = document.querySelector('html');
            html.setAttribute('data-header-position', 'scrollable');
        }
        if (localStorage.xyntramenufixed) {
            let html = document.querySelector('html');
            html.setAttribute('data-menu-position', 'fixed');
        }
        if (localStorage.xyntramenuscrollable) {
            let html = document.querySelector('html');
            html.setAttribute('data-menu-position', 'scrollable');
        }
        if (localStorage.xyntraMenu) {
            let html = document.querySelector('html');
            let menuValue = localStorage.getItem('xyntraMenu');
            switch (menuValue) {
                case 'light':
                    html.setAttribute('data-menu-styles', 'light');
                    break;
                case 'dark':
                    html.setAttribute('data-menu-styles', 'dark');
                    break;
                case 'color':
                    html.setAttribute('data-menu-styles', 'color');
                    break;
                case 'gradient':
                    html.setAttribute('data-menu-styles', 'gradient');
                    break;
                case 'transparent':
                    html.setAttribute('data-menu-styles', 'transparent');
                    break;
                default:
                    break;
            }
        }
        if (localStorage.xyntraHeader) {
            let html = document.querySelector('html');
            let headerValue = localStorage.getItem('xyntraHeader');
            switch (headerValue) {
                case 'light':
                    html.setAttribute('data-header-styles', 'light');
                    break;
                case 'dark':
                    html.setAttribute('data-header-styles', 'dark');
                    break;
                case 'color':
                    html.setAttribute('data-header-styles', 'color');
                    break;
                case 'gradient':
                    html.setAttribute('data-header-styles', 'gradient');
                    break;
                case 'transparent':
                    html.setAttribute('data-header-styles', 'transparent');
                    break;

                default:
                    break;
            }
        }
        if (localStorage.bgimg) {
            let html = document.querySelector('html');
            let value = localStorage.getItem('bgimg');
            html.setAttribute('bg-img', value);
        }
    }
    localStorageBackup()

})();