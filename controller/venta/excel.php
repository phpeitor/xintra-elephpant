<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../model/ticket.php';

try {
    $fecha_inicio = $_GET['fecha_inicio'] ?? null;
    $fecha_fin    = $_GET['fecha_fin'] ?? null;

    $cli = new Ticket();
    $data = $cli->excel($fecha_inicio, $fecha_fin);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
