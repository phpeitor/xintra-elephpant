<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../model/ticket.php';

try {
    if (!isset($_GET['hash']) || strlen($_GET['hash']) !== 32) {
        echo json_encode(['ok' => false, 'message' => 'Hash no válido']);
        exit;
    }

    $hash = $_GET['hash'];
    $ticket = new Ticket();

    $data = $ticket->obtenerPorHash($hash);

    if ($data) {
        echo json_encode(['ok' => true, 'data' => $data]);
    } else {
        echo json_encode(['ok' => false, 'message' => 'Ticket no encontrado']);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
