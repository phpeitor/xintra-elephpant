<?php
require_once __DIR__ . '/../../model/ticket.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['hash']) || strlen($_GET['hash']) !== 32) {
    die('Hash no valido.');
}

$hash = $_GET['hash'];

$ticket   = new Ticket();
$data     = $ticket->obtenerPorHash($hash);
$detalles = $ticket->obtenerDetallePorHash($hash);

if (!$data) {
    die('No se encontro el ticket.');
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
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'file://' . $logoPath,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $imageData = curl_exec($ch);

        if (!curl_errno($ch) && $imageData !== false) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode($imageData);
        }

        curl_close($ch);
    }
}

$e = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

$logoSrc = $logoFs ?: $logoBase64;
$logoTag = $logoSrc !== '' ? "<img src='" . $e($logoSrc) . "' class='logo-img' />" : '';
$detalles = is_array($detalles) ? $detalles : [];

$barcode39 = function ($value, $label = null) use ($e) {
    $patterns = [
        '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn', 'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw',
        'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw', 'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn',
        'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn', 'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn',
        'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww', 'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww',
        'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn', 'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn',
        'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn', 'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw',
        'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw', 'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn', '$' => 'nwnwnwnnn',
        '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn', '*' => 'nwnnwnwnn',
    ];

    $value = strtoupper(preg_replace('/[^0-9A-Z\. \-\/\+\$%]/', '', (string)$value));
    $value = $value !== '' ? '*' . $value . '*' : '*TICKET*';
    $html = '';

    for ($i = 0, $len = strlen($value); $i < $len; $i++) {
        $pattern = $patterns[$value[$i]] ?? $patterns['-'];
        for ($j = 0; $j < 9; $j++) {
            $widthClass = $pattern[$j] === 'w' ? 'wide' : 'narrow';
            if ($j % 2 === 0) {
                $html .= "<span class='bar {$widthClass}'></span>";
            } else {
                $html .= "<span class='gap {$widthClass}'></span>";
            }
        }
        $html .= "<span class='gap char-gap'></span>";
    }

    $label = $label ?? trim($value, '*');

    return "<div class='barcode'>{$html}</div><div class='barcode-label'>" . $e($label) . '</div>';
};

$sumItems = 0.0;
foreach ($detalles as $d) {
    $sumItems += (float)($d['subtotal'] ?? 0);
}

$total    = round($sumItems, 2);
$subtotal = round($total / 1.18, 2);
$igv      = round($total - $subtotal, 2);
$fmtSubtotal = number_format($subtotal, 2, '.', ',');
$fmtIgv      = number_format($igv, 2, '.', ',');
$fmtTotal    = number_format($total, 2, '.', ',');

$barcodeValue = strtoupper(substr($hash, 0, 16));
$mainBarcode = $barcode39($barcodeValue, $hash);
$paperHeightMm = max(180, 88 + (count($detalles) * 4.6));
$paperHeightPt = $paperHeightMm * 2.83465;
$cssPath = realpath(__DIR__ . '/../../assets/css/tkt_pdf.css');
$cssHref = $cssPath ? 'file://' . str_replace('\\', '/', $cssPath) : '';

$html = '
<html>
<head>
<meta charset="UTF-8">
<title>Xintra PDF</title>
<link rel="stylesheet" href="' . $e($cssHref) . '">
</head>
<body>';

$html .= "
<div class='ticket'>
    <div class='stub'>
        <div class='stub-title'>XINTRA AMVSOFT</div>
        <div class='stub-number'>NRO " . $e($data['id'] ?? '') . ' - ' . $e($data['fecha'] ?? '') . "</div>
    </div>
    <div class='pixel p1'></div><div class='pixel p2'></div><div class='pixel p3'></div>
    <div class='pixel p4'></div><div class='pixel p5'></div><div class='pixel p6'></div>
    <div class='content'>
        <div class='topline'>Black White Barberia Salon</div>
        <div class='brand'>
            {$logoTag}
            <div class='admit'>Ticket de Venta</div>
            <div class='title'>TICKET</div>
            <div class='subtitle'>Lambayeque - Peru</div>
        </div>
        <div class='meta-card'>
            <div class='meta-row'><span class='meta-label'>Numero</span><span class='meta-value'>" . $e($data['id'] ?? '') . "</span></div>
            <div class='meta-row'><span class='meta-label'>Usuario</span><span class='meta-value'>" . $e($data['user'] ?? '') . "</span></div>
            <div class='meta-row'><span class='meta-label'>Cliente</span><span class='meta-value'>" . $e($data['cliente_nombre'] ?? '') . "</span></div>
            <div class='meta-row'><span class='meta-label'>Fecha</span><span class='meta-value'>" . $e($data['fecha'] ?? '') . "</span></div>
        </div>
        <div class='section-label'>Detalle</div>
        <table>
            <thead>
                <tr><th>ITEM</th><th class='money'>P.U</th><th class='qty'>#</th><th class='money'>IMP.</th></tr>
            </thead>
            <tbody>";

foreach ($detalles as $d) {
    $item     = $e($d['item'] ?? '');
    $precio   = number_format((float)($d['precio'] ?? 0), 2, '.', ',');
    $cantidad = (int)($d['cantidad'] ?? 0);
    $subfila  = number_format((float)($d['subtotal'] ?? 0), 2, '.', ',');

    $html .= "
                <tr>
                    <td class='item'>{$item}</td>
                    <td class='money'>S/ {$precio}</td>
                    <td class='qty'>{$cantidad}</td>
                    <td class='money'>S/ {$subfila}</td>
                </tr>";
}

$html .= "
            </tbody>
        </table>
        <div class='totals'>
            <div class='total-row'><span>Subtotal</span><span>S/ {$fmtSubtotal}</span></div>
            <div class='total-row'><span>IGV</span><span>S/ {$fmtIgv}</span></div>
            <div class='total-row grand'><span>Total</span><span>S/ {$fmtTotal}</span></div>
        </div>
        <div class='barcode-wrap'>{$mainBarcode}</div>
        <div class='footer'>Gracias por su preferencia</div>
        <div class='website'>www.sales.metadatape.com</div>
    </div>
</div>
</body></html>";

$dompdf->loadHtml($html);
$dompdf->setPaper([0, 0, 226.77, $paperHeightPt]);
$dompdf->render();
$dompdf->stream('ticket_' . $data['id'] . '.pdf', ['Attachment' => false]);
exit;
