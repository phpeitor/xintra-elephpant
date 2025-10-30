<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/item.php';

try {
    $cli = new Item();
    $payload = [
        'nombre'    => $_POST['nombre']   ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'precio'    => $_POST['precio']     ?? '',
        'stock'     => $_POST['stock'] ?? '',
    ];

    $id = $cli->guardar($payload);
    echo json_encode(['ok' => true, 'id' => $id]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
