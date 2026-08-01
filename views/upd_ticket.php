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
      <link rel="stylesheet" href="./assets/libs/flatpickr/flatpickr.min.css">
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
               <div class="flex items-center justify-between page-header-breadcrumb flex-wrap gap-2">
                  <div>
                     <nav aria-label="nav">
                        <ol class="breadcrumb mb-1">
                           <li class="breadcrumb-item"><a href="javascript:void(0);">Tickets</a></li>
                           <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
                        </ol>
                     </nav>
                     <h1 class="page-title font-medium text-lg mb-0">Editar Ticket</h1>
                  </div>
                  <div class="btn-list"> <button type="button" class="ti-btn bg-white dark:bg-bodybg border border-defaultborder dark:border-defaultborder/10 btn-wave !my-0 waves-effect waves-light"> <i class="ri-filter-3-line align-middle me-1 leading-none"></i> Filter </button> <button type="button" class="ti-btn ti-btn-primary !border-0 btn-wave me-0 waves-effect waves-light"  onclick="window.location.href='tickets.php'"> <i class="ri-reply-line"></i> </button> </div>
               </div>
            
               <div class="col-span-12">
                  <div class="box">
                     <div class="box-header">
                        <h5 class="box-title">Data Validation</h5>
                     </div>
                     <div class="box-body">
                        <form class="ti-custom-validation-ticket" novalidate>

                           <div class="grid lg:grid-cols-3 gap-6">
                              <div class="space-y-2">
                                 <label class="ti-form-label">Fecha</label>
                                 <div class="input-group"> <div class="input-group-text !text-textmuted dark:text-textmuted/50 !border-defaultborder dark:!border-defaultborder/10"> <i class="ri-calendar-line"></i> </div> <input type="text" class="form-control flatpickr-input active" id="date" name="date" readonly="readonly" data-rules="required"> </div>
                                 <span class="text-red-500 text-xs hidden" data-error-for="fecha"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Cliente</label>
                                 <select id="cliente" name="cliente" class="ti-form-select rounded-sm !py-2 !px-3" data-rules="required">
                                    <option value="">Seleccione</option>
                                 </select>
                                 <span class="text-red-500 text-xs hidden" data-error-for="cliente"></span>
                              </div>

                               <div class="space-y-2">
                                 <label class="ti-form-label">Personal</label>
                                 <select id="personal" name="personal" class="ti-form-select rounded-sm !py-2 !px-3" data-rules="required">
                                    <option value="">Seleccione</option>
                                 </select>
                                 <span class="text-red-500 text-xs hidden" data-error-for="personal"></span>
                              </div>
                           </div>

                           <div class="grid lg:grid-cols-3 gap-6 mt-3">
                              <div class="space-y-2">
                                 <label class="ti-form-label">Grupo</label>
                                 <select id="grupo" name="grupo" class="ti-form-select rounded-sm !py-2 !px-3" >
                                    <option value="">Seleccione</option>
                                    <option value="PRODUCTO">Producto</option>
                                    <option value="SERVICIO">Servicio</option>
                                 </select>
                                 <span class="text-red-500 text-xs hidden" data-error-for="grupo"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Categoria</label>
                                 <select id="categoria" name="categoria" class="ti-form-select rounded-sm !py-2 !px-3">
                                    <option value="">Seleccione</option>
                                 </select>
                                 <span class="text-red-500 text-xs hidden" data-error-for="categoria"></span>
                              </div>

                              <div class="space-y-2">
                                 <label class="ti-form-label">Item</label>
                                 <div class="flex items-stretch gap-2">
                                    <div class="flex-1">
                                       <select id="item" name="item" class="ti-form-select w-full rounded-sm !py-2 !px-3">
                                       <option value="">Seleccione</option>
                                       </select>
                                    </div>

                                    <div class="hs-tooltip ti-main-tooltip ltr:[--placement:left] rtl:[--placement:right]"> 
                                       <a href="javascript:void(0);" id="btnAddToCart" class="ti-btn ti-btn-icon bg-primarytint2color text-white ti-btn-sm waves-effect waves-light">
                                          <i class="ri-shopping-cart-line"></i> 
                                          <span class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm hidden" role="tooltip"> Agregar carrito</span> 
                                       </a> 
                                    </div>
                                 </div>

                                 <span class="text-red-500 text-xs hidden" data-error-for="item"></span>
                              </div>
                           </div>
                        
                     </div>
                  </div>
               </div>

               <div class="grid grid-cols-12 gap-x-6">
                  <div class="xl:col-span-9 col-span-12">
                     <div class="box relative" id="cart-container-delete">
                        <div class="box-header">
                           <div class="box-title"> Tabla Items </div>
                        </div>
                        <div id="ticket-items-loader" class="absolute inset-0 z-30 hidden items-center justify-center bg-white dark:bg-bodybg rounded-[0.5rem]">
                           <div class="flex items-center gap-3 rounded-full bg-white dark:bg-bodybg px-4 py-3 shadow-sm border border-defaultborder dark:border-defaultborder/10">
                              <div class="ti-spinner" role="status"> <span class="sr-only">Cargando...</span> </div>
                              <span class="font-medium text-textmuted dark:text-textmuted/50">Cargando items del ticket...</span>
                           </div>
                        </div>
                        <div class="box-body">
                           <div class="table-responsive" id="ticket-items-table-wrap">
                              <table class="table table-bordered whitespace-nowrap min-w-full">
                                 <thead>
                                    <tr class="border border-solid dark:!border-defaultborder/10 !border-defaultborder">
                                       <th scope="col"> Nombre </th>
                                       <th scope="col"> Precio </th>
                                       <th scope="col"> Cantidad </th>
                                       <th scope="col"> Total </th>
                                       <th scope="col"> Opciones </th>
                                    </tr>
                                 </thead>
                                 <tbody id="cartBody">
                                    
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                     <div class="box !hidden" id="cart-empty-cart">

                        <div class="cargando flex items-center justify-between !hidden"> <strong>Cargando...</strong> 
                           <div class="ti-spinner" role="status"> <span class="sr-only">Cargando...</span> </div> 
                        </div>

                        <div class="box-header">
                           <div class="box-title">Carrito vacío</div>
                        </div>
                        <div class="box-body">
                           <div class="cart-empty text-center">
                              <span class="svg-muted">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="!inline-flex" width="24" height="24" viewBox="0 0 24 24">
                                 <path d="M18.6 16.5H8.9c-.9 0-1.6-.6-1.9-1.4L4.8 6.7c0-.1 0-.3.1-.4.1-.1.2-.1.4-.1h17.1c.1 0 .3.1.4.2.1.1.1.3.1.4L20.5 15c-.2.8-1 1.5-1.9 1.5zM5.9 7.1 8 14.8c.1.4.5.8 1 .8h9.7c.5 0 .9-.3 1-.8l2.1-7.7H5.9z"></path>
                                 <path d="M6 10.9 3.7 2.5H1.3v-.9H4c.2 0 .4.1.4.3l2.4 8.7-.8.3zM8.1 18.8 6 11l.9-.3L9 18.5z"></path>
                                 <path d="M20.8 20.4h-.9V20c0-.7-.6-1.3-1.3-1.3H8.9c-.7 0-1.3.6-1.3 1.3v.5h-.9V20c0-1.2 1-2.2 2.2-2.2h9.7c1.2 0 2.2 1 2.2 2.2v.4z"></path>
                                 <path d="M8.9 22.2c-1.2 0-2.2-1-2.2-2.2s1-2.2 2.2-2.2c1.2 0 2.2 1 2.2 2.2s-1 2.2-2.2 2.2zm0-3.5c-.7 0-1.3.6-1.3 1.3 0 .7.6 1.3 1.3 1.3.8 0 1.3-.6 1.3-1.3 0-.7-.5-1.3-1.3-1.3zM18.6 22.2c-1.2 0-2.2-1-2.2-2.2s1-2.2 2.2-2.2c1.2 0 2.2 1 2.2 2.2s-.9 2.2-2.2 2.2zm0-3.5c-.8 0-1.3.6-1.3 1.3 0 .7.6 1.3 1.3 1.3.7 0 1.3-.6 1.3-1.3 0-.7-.5-1.3-1.3-1.3z"></path>
                                 </svg>
                              </span>
                              <h3 class="font-bold mb-1">Carrito sin items</h3>
                              <h5 class="mb-3">Agrega items para sonreir 😃</h5>
                              <a href="#" class="ti-btn bg-primary text-white btn-wave m-3 waves-effect waves-light" data-abc="true">Continuar <i class="bi bi-arrow-right ms-1"></i>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="xl:col-span-3 col-span-12">
                     <div class="box">
                        <div class="box-header">
                           <div class="box-title"> Resumen del pedido</div>
                        </div>
                        <div class="box-body p-0">
                           <div class="p-4 border-b border-defaultborder dark:border-defaultborder/10 border-dashed">
                              <label for="promo-code" class="font-medium mb-0">¿Tienes un código promocional?</label>
                              <div class="text-[11px] text-textmuted dark:text-textmuted/50 mb-3">
                                 Aplica tu código promocional para obtener un descuento instantáneo
                              </div>
                              <div class="input-group mb-0">
                                 <input type="text" class="form-control form-control-sm !border-s" id="promo-code" name="promo-code" placeholder="Enter Promo Code" aria-label="Enter Promo Code" aria-describedby="coupons">
                                 <button class="ti-btn ti-btn-primary !m-0" type="button" id="coupons">Aplicar</button>
                              </div>
                              <input type="hidden" id="descuentoInput" name="descuento" value="0">
                              <div class="text-[11px] text-textmuted dark:text-textmuted/50 mt-2 mb-3">
                                 Seleccione tipo de pago
                              </div>
                              <div class="input-group mt-2 mb-0">
                                 <select id="pago" name="pago" class="ti-form-select rounded-sm !py-1 !px-2" >
                                    <option value="EFECTIVO">EFECTIVO</option>
                                    <option value="YAPE">YAPE</option>
                                    <option value="PLIN">PLIN</option>
                                    <option value="VISA">VISA</option>
                                    <option value="MASTERCARD">MASTERCARD</option>
                                 </select>
                              </div>
                           </div>
                              <div class="p-4 border-b border-defaultborder dark:border-defaultborder/10 border-dashed">
                                 <div class="overflow-hidden p-0 border-0" id="freeshipping-pane" role="tabpanel">
                                    <div class="flex items-center justify-between mb-3">
                                       <div class="text-textmuted dark:text-textmuted/50">Sub Total</div>
                                       <div id="subtotal" class="font-medium text-[14px]">S/. 0.00</div>
                                    </div>
                                    <div class="flex items-center justify-between mb-3">
                                       <div class="text-textmuted dark:text-textmuted/50">Descuento</div>
                                       <div id="descuento" class="font-medium text-[14px] text-success">S/. 0.00</div>
                                    </div>
                                    <div id="promoAppliedRow" class="hidden flex items-center justify-between mb-3 text-[12px]">
                                       <div class="text-textmuted dark:text-textmuted/50">Promocode</div>
                                       <div id="promoAppliedBadge" class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-2 py-1 font-medium text-primary">
                                          <span id="promoAppliedCode">-</span>
                                          <button type="button" id="promoAppliedRemove" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary text-white leading-none" aria-label="Quitar promocode">×</button>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="flex items-center justify-between mb-3">
                                    <div class="text-textmuted dark:text-textmuted/50">IGV (18%)</div>
                                    <div id="igv" class="font-medium text-[14px]">S/. 0.00</div>
                                 </div>
                                 <div class="flex items-center justify-between h5">
                                    <div class="text-[1rem]">Total :</div>
                                    <div id="total" class="font-semibold">S/. 0.00</div>
                                 </div>
                                 <div class="grid">
                                    <button type="submit" class="ti-btn ti-btn-primary ti-custom-validate-btn">Enviar</button>
                                    <a href="#" class="ti-btn ti-btn-soft-primary1 text-center btn-wave waves-effect waves-light">Continuar</a>
                                 </div>
                              </div>
                            </form>
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
      <script src="./assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
      <script src="./assets/js/choices_ticket.js?v=2"></script>
      <script src="./assets/libs/flatpickr/flatpickr.min.js"></script>
      <script src="./assets/js/custom-switcher.min.js"></script>
      <script src="./assets/libs/tabulator-tables/js/tabulator.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
      <script src="./assets/js/form-validation.js?v=2.1"></script>
      <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
      <script src="./assets/js/custom.js"></script>
   </body>
</html>