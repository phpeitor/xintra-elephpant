<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/item.php';

try {
    $tpo = $_GET['tpo'] ?? ''; 
    $cli = new Item();
    $data = $cli->obtenerCategoria($tpo);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
