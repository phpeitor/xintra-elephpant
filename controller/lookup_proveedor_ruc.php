<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../config/ruc_lookup.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$ruc = trim((string)($_GET['ruc'] ?? ''));
if (!preg_match('/^\d{11}$/', $ruc)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'El RUC debe contener exactamente 11 dígitos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (trim((string)($_ENV['API_RUC_URL'] ?? '')) === '' && trim((string)($_ENV['API_RUC_URL_2'] ?? '')) === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Configura API_RUC_URL o API_RUC_URL_2 en .env.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nombre = consultarNombreRuc($ruc);
if ($nombre === null) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'No se encontró información para ese RUC.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['ok' => true, 'nombre' => $nombre, 'tipoDocumento' => '6', 'numeroDocumento' => $ruc], JSON_UNESCAPED_UNICODE);
