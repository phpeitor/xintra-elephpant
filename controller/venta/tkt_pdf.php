<?php
require_once __DIR__ . '/../../model/ticket.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['hash']) || strlen($_GET['hash']) !== 32) {
    die('Hash no válido.');
}

$hash = $_GET['hash'];

$ticket   = new Ticket();
$data     = $ticket->obtenerPorHash($hash);
$detalles = $ticket->obtenerDetallePorHash($hash);

if (!$data) {
    die('No se encontró el ticket.');
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->setChroot(realpath(__DIR__ . '/../../'));
$dompdf = new Dompdf($options);
$logoFs = realpath(__DIR__ . '/../../assets/images/brand-logos/logo.png');
$logoFs = $logoFs ? 'file://' . str_replace('\\', '/', $logoFs) : '';

$logoBase64 = '';
if (!$logoFs) {
    $logoPath = __DIR__ . '/../../assets/images/brand-logos/logo.png';
    if (is_file($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
}
$logoSrc = $logoFs ?: $logoBase64; 

$logoTag = $logoSrc !== '' ? "<img src='" . $logoSrc . "' class='logo-img' />" : '';
$detalles  = is_array($detalles) ? $detalles : [];

$sumItems = 0.0;
foreach ($detalles as $d) {
    $sumItems += (float)($d['subtotal'] ?? 0);
}

$total    = round($sumItems, 2);
$subtotal = round($total / 1.18, 2);
$igv      = round($total - $subtotal, 2);
$fmtSubtotal = number_format($subtotal, 2, '.', ',');
$fmtIgv      = number_format($igv,      2, '.', ',');
$fmtTotal    = number_format($total,    2, '.', ',');

/* -------- HTML -------- */
$html  = '';
$html .= '
<html>
<head>
<meta charset=\'UTF-8\'>
<title>Xintra PDF</title>
<style>
@page { margin: 0 }
body{ margin:0; padding:0; width:80mm; font-family: Arial, sans-serif; font-size:9px; position:relative; line-height:1.2; }
.pos{ position:absolute; }
h1,h2,h3,p,div{ margin:0; }
.logo-img{ position:absolute; top:9mm; left:32mm; width:16mm; height:16mm; object-fit:contain; z-index:1; }
.linea{ position:absolute; left:3mm; width:74mm; border-top:.3px dashed #000; }
.texto{ font-size:8px; }
.col-item{ left:3mm;  width:36mm; box-sizing:border-box; overflow:hidden; white-space:nowrap; }
.col-pu  { left:39mm; width:16mm; text-align:left;  box-sizing:border-box; } 
.col-cant{ left:55mm; width:6mm;  text-align:left;  box-sizing:border-box; } 
.col-sub { left:62mm; width:14mm; text-align:left;  box-sizing:border-box; } 
.gracias{
  position:absolute;
  letter-spacing:1.2px;     
  word-spacing:3px;       
  padding:2mm 4mm;
  text-align:center;
}
</style>
</head>
<body>
';

$html .= $logoTag;

$html .= "
<div class='pos' style='top:22mm; left:8mm; font-weight:bold; z-index:2;'>*** TICKET DE VENTA EXPRESS ***</div>
<div class='pos texto' style='top:25.2mm; left:8mm; z-index:2;'>BLACK WHITE BARBERÍA SALÓN</div>
<div class='pos texto' style='top:27.4mm; left:8mm; z-index:2;'>LAMBAYEQUE - PERÚ</div>

<div class='linea' style='top:30.5mm;'></div>

<div class='pos texto' style='top:36mm; left:3mm;'>Número:</div>
<div class='pos texto' style='top:36mm; left:20mm;'>{$data['id']}</div>

<div class='pos texto' style='top:39mm; left:3mm;'>Usuario:</div>
<div class='pos texto' style='top:39mm; left:20mm;'>{$data['user']}</div>

<div class='pos texto' style='top:42mm; left:3mm;'>Cliente:</div>
<div class='pos texto' style='top:42mm; left:20mm;'>{$data['cliente_nombre']}</div>

<div class='pos texto' style='top:45mm; left:3mm;'>Fecha:</div>
<div class='pos texto' style='top:45mm; left:20mm;'>{$data['fecha']}</div>

<div class='linea' style='top:48mm;'></div>

<div class='pos texto' style='top:51mm; left:3mm;  font-weight:bold;'>ITEM</div>
<div class='pos texto' style='top:51mm; left:39mm; font-weight:bold;'>P.U</div>  <!-- antes 40mm -->
<div class='pos texto' style='top:51mm; left:55mm; font-weight:bold;'>#</div>
<div class='pos texto' style='top:51mm; left:62mm; font-weight:bold;'>IMP.</div> <!-- antes 63mm -->
";

$y = 54;
foreach ($detalles as $d) {
    $item     = $d['item']    ?? '';
    $precio   = number_format((float)($d['precio']   ?? 0), 2, '.', ',');
    $cantidad = (int)($d['cantidad'] ?? 0);
    $subfila  = number_format((float)($d['subtotal'] ?? 0), 2, '.', ',');

    $html .= "
    <div class='pos texto col-item' style='top: {$y}mm;'>{$item}</div>
    <div class='pos texto col-pu'   style='top: {$y}mm;'>S/ {$precio}</div>
    <div class='pos texto col-cant' style='top: {$y}mm;'>{$cantidad}</div>
    <div class='pos texto col-sub'  style='top: {$y}mm;'>S/ {$subfila}</div>";
    $y += 3;
}

$html .= "
<div class='linea' style='top: " . ($y + 2) . "mm;'></div>

<div class='pos texto' style='top: " . ($y + 5)  . "mm; left:45mm;'>Subtotal:</div>
<div class='pos texto' style='top: " . ($y + 5)  . "mm; left:63mm;'>S/ {$fmtSubtotal}</div>

<div class='pos texto' style='top: " . ($y + 8)  . "mm; left:45mm;'>IGV:</div>
<div class='pos texto' style='top: " . ($y + 8)  . "mm; left:63mm;'>S/ {$fmtIgv}</div>

<div class='pos texto' style='top: " . ($y + 11) . "mm; left:45mm; font-weight:bold;'>Total Venta:</div>
<div class='pos texto' style='top: " . ($y + 11) . "mm; left:63mm; font-weight:bold;'>S/ {$fmtTotal}</div>

<div class='pos texto' style='top: " . ($y + 17) . "mm; left:15mm;'><!-- espacio reservado --></div>
<div class='gracias' style='top: " . ($y + 17) . "mm; left:14mm;'>Gracias por su preferencia...</div>
</body></html>";

/* -------- Render -------- */
$dompdf->loadHtml($html);
$dompdf->setPaper([0, 0, 226.77, 566.93]); 
$dompdf->render();
$dompdf->stream('ticket_' . $data['id'] . '.pdf', ['Attachment' => false]);
exit;