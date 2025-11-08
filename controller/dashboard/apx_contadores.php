<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../model/dashboard.php';

try {
    $cli = new Dashboard();
    $data1 = $cli->obtenerContadores();
    $data2 = $cli->obtenerGrafSales();
    $data3 = $cli->obtenerGrafUser();
    echo json_encode([
        'contadores' => $data1,
        'grafico'    => $data2,
        'versus'    => $data3
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
