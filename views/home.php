<?php
  require_once __DIR__ . '/../config/bootstrap.php';
  require_once ROOT . '/controller/check_session.php';
?>

<html bg-img="bgimg5" class="light" data-header-styles="light" data-menu-styles="dark" data-nav-layout="vertical"
    data-vertical-style="overlay" data-width="fullwidth" dir="ltr" lang="en" loader="disable">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='75'>🐘</text></svg>" />
    <title>Xintra Elephant</title>
    <meta content="Tailwind Responsive Admin Web Dashboard HTML5 Template" name="Description" />
    <meta content="amvsoft.tech Technologies Private Limited" name="Author" />
    <meta
        content="tailwind template,tailwind dashboard,tailwind,tailwind admin template,dashboard,tailwind css templates,html dashboard template,tailwind dashboard template,dashboard tailwind,admin,html css templates,html dashboard,html css javascript templates,dashboard tailwind template,tailwind css dashboard"
        name="keywords" />
    <script src="./assets/js/main.js"></script>
    <link href="./assets/css/styles.css" rel="stylesheet" />
    <link href="./assets/libs/node-waves/waves.min.css" rel="stylesheet" />
    <link href="./assets/libs/simplebar/simplebar.min.css" rel="stylesheet" />
    <link href="./assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" />
    <link href="./assets/libs/@simonwep/pickr/themes/nano.min.css" rel="stylesheet" />
    <link href="./assets/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" />
    <link href="./assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css" />
    <meta content="no" http-equiv="imagetoolbar" />
</head>

<body>
    <?php include ROOT . '/layout/init.php'; ?>

    <div class="page">
        <?php include ROOT . '/layout/header.php'; ?>
        <?php include ROOT . '/layout/sidebar.php'; ?>

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
                                <input type="text" class="form-control breadcrumb-input flatpickr-input" id="daterange"
                                    placeholder="Search By Date Range" readonly="readonly">
                            </div>
                        </div>
                        <div class="ti-btn-list">
                            <button
                                class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 !m-0 !me-[0.35rem] waves-effect waves-light">
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
                                                <span class="text-textmuted dark:text-textmuted/50 block mb-1">Total
                                                    Productos</span>
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
                                                <span class="block text-textmuted dark:text-textmuted/50 mb-1">Total
                                                    Usuarios</span>
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
                                                <span class="text-textmuted dark:text-textmuted/50 block mb-1">Total
                                                    Tickets</span>
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
                                                <span class="text-textmuted dark:text-textmuted/50 block mb-1">Total
                                                    Servicios</span>
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
                                        <div class="box-title"> Total Sales </div>
                                        <div class="ti-dropdown hs-dropdown">
                                            <a href="javascript:void(0);"
                                                class="ti-btn ti-btn-light ti-btn-sm text-textmuted dark:text-textmuted/50 ti-dropdown-toggle hs-dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false"> Sort By <i
                                                    class="ri-arrow-down-s-line align-middle fs-13 d-inline-block"></i>
                                            </a>
                                            <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu"
                                                data-popper-placement="bottom-end">
                                                <li>
                                                    <a class="ti-dropdown-item" href="javascript:void(0);">This Week</a>
                                                </li>
                                                <li>
                                                    <a class="ti-dropdown-item" href="javascript:void(0);">Last Week</a>
                                                </li>
                                                <li>
                                                    <a class="ti-dropdown-item" href="javascript:void(0);">This
                                                        Month</a>
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
                                            <a aria-label="anchor" href="javascript:void(0);"
                                                class="ti-btn ti-btn-light ti-btn-sm ti-btn-icon text-textmuted dark:text-textmuted/50 hs-dropdown-toggle ti-dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
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
                                                    <span class="text-[11px] mb-1 block font-medium">Productividad
                                                        Usuarios</span>
                                                    <div class="flex items-center justify-between">
                                                        <h4 class="mb-0 flex items-center" id="total_item">0
                                                            <span
                                                                class="text-success text-xs ms-2 inline-flex items-center"
                                                                id="porcentaje_item">
                                                                <i class="ti ti-trending-up align-middle me-1"></i>0%
                                                            </span>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <a href="javascript:void(0);"
                                                    class="text-success text-xs decoration-solid">Earnings ?</a>
                                            </div>
                                        </div>
                                        <div id="orders" class="my-2" style="min-height: 188.8px;">

                                        </div>
                                    </div>

                                    <div class="box-footer border-t border-dashed">
                                        <div class="grid">
                                            <button
                                                class="ti-btn ti-btn-outline-primary ti-btn-wave btn-wave font-medium waves-effect waves-light table-icon">Complete
                                                Statistics <i
                                                    class="ti ti-arrow-narrow-right ms-2 text-[16px] inline-block"></i>
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
                                            <div
                                                class="xxl:col-span-7 xl:col-span-5 lg:col-span-5 md:col-span-5 sm:col-span-5 col-span-12">
                                                <h4 class="mb-4 font-medium text-white">Actualizar Profesional</h4>
                                                <p class="mb-6 text-white">Maximiza la información sobre ventas.
                                                    Optimiza el rendimiento.</p>
                                                <a href="javascript:void(0);"
                                                    class="font-medium text-white decoration-solid underline">Obtener
                                                    más información <i class="ti ti-arrow-narrow-right"></i>
                                                </a>
                                            </div>
                                            <div
                                                class="xxl:col-span-4 xl:col-span-7 lg:col-span-7 md:col-span-7 sm:col-span-7 sm:block hidden text-end my-auto col-span-12">
                                                <img src="./assets/images/media/media-86.png" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="xl:col-span12 col-span-12">
                                <div class="box overflow-hidden">
                                    <div class="box-header justify-between pb-1">
                                        <div class="box-title"> Top Selling </div>
                                        <div class="ti-dropdown hs-dropdown">
                                            <a href="javascript:void(0);"
                                                class="ti-btn ti-btn-light text-textmuted dark:text-textmuted/50 ti-dropdown-toggle hs-dropdown-toggle ti-btn-sm gap-0"
                                                data-bs-toggle="dropdown" aria-expanded="false"> Sort By <i
                                                    class="ri-arrow-down-s-line align-middle ms-1 inline-block"></i>
                                            </a>
                                            <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu"
                                                data-popper-placement="bottom-end">
                                                <li>
                                                    <a class="ti-dropdown-item" href="javascript:void(0);">This Week</a>
                                                </li>
                                                <li>
                                                    <a class="ti-dropdown-item" href="javascript:void(0);">Last Week</a>
                                                </li>
                                                <li>
                                                    <a class="ti-dropdown-item" href="javascript:void(0);">This
                                                        Month</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="box-body p-0">
                                        <div class="p-4 pb-0">
                                            <div class="progress-stacked progress-sm mb-3 flex gap-1">
                                                <div class="progress-bar w-[25%]" role="progressbar" aria-valuenow="25"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                                <div class="progress-bar bg-primarytint1color w-[15%] !rounded-none"
                                                    role="progressbar" aria-valuenow="15" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                                <div class="progress-bar bg-primarytint2color !rounded-none w-[15%]"
                                                    role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                                <div class="progress-bar bg-primarytint3color !rounded-none w-[20%]"
                                                    role="progressbar" aria-valuenow="35" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                                <div class="progress-bar !rounded-none !rounded-tr-md !rounded-br-md bg-secondary w-[25%]"
                                                    role="progressbar" aria-valuenow="35" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                            <div class="flex items-center justify-between mb-2">
                                                <div>Overall Sales</div>
                                                <div class="h6 mb-0">
                                                    <span class="text-success me-2 text-[11px]"
                                                        id="porcentaje_actual">0% <i class="ti ti-arrow-narrow-up"></i>
                                                    </span><span id="total_actual">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive top-categories">
                                            <table class="table text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th class="border-top-0">Usuario</th>
                                                        <th class="border-top-0"></th>
                                                        <th class="border-top-0">Ultimo</th>
                                                        <th class="border-top-0">Anterior</th>
                                                        <th class="border-top-0 !text-end">%</th>
                                                    </tr>
                                                </thead>
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

        <?php include ROOT . '/layout/footer.php'; ?>
    </div>

    <?php include ROOT . '/layout/scroll.php'; ?>

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
    <script src="./assets/js/xintra-tooltip.js?v=1.3"></script>
    <script src="./assets/js/sales-dashboard.js?v=2.3"></script>
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <script src="./assets/js/custom.js?v=2"></script>
    <script src="./assets/js/custom-switcher.min.js"></script>
</body>

</html>
