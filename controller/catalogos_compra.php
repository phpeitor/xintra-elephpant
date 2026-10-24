<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/compra.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(['ok' => true, 'data' => (new Compra())->catalogos()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudieron cargar proveedores y productos para compras.'], JSON_UNESCAPED_UNICODE);
}
