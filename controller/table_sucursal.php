<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/sucursal.php';

try {
    $sucursal = new Sucursal();
    echo json_encode($sucursal->listar(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'No se pudieron cargar las sucursales.'], JSON_UNESCAPED_UNICODE);
}
