<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/ruc_lookup.php';
require_once ROOT . '/controller/check_session.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.']);
    exit;
}

$ruc = trim((string)($_GET['ruc'] ?? ''));
if (!preg_match('/^\d{11}$/', $ruc)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'El RUC debe contener 11 dígitos.']);
    exit;
}

if (trim((string)($_ENV['API_RUC_URL'] ?? '')) === '' && trim((string)($_ENV['API_RUC_URL_2'] ?? '')) === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Configura API_RUC_URL o API_RUC_URL_2 en .env.']);
    exit;
}

$nombre = consultarNombreRuc($ruc);
if ($nombre !== null) {
    echo json_encode([
        'ok' => true,
        'nombre' => $nombre,
        'tipoDocumento' => '6',
        'numeroDocumento' => $ruc,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(404);
echo json_encode(['ok' => false, 'message' => 'No se encontró información para ese RUC en las APIs configuradas.'], JSON_UNESCAPED_UNICODE);
