<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/item.php';

try {
    $stock     = trim($_POST['stock'] ?? '');
    $fecha     = trim($_POST['fecha'] ?? '');   
    $hash      = trim($_POST['id'] ?? '');

    if ($stock === '' || !is_numeric($stock) || $stock <= 0) {
        throw new Exception('Cantidad de stock no válida.');
    }

    if ($hash === '') {
        throw new Exception('Hash del producto inválido.');
    }

    $item = new Item();
    $item->guardar_stock([
        'id_product' => $hash,
        'id_pedido'  => 0,
        'tipo'       => 'E',
        'stock'      => (float)$stock,
        'fecha'      => $fecha,
        'user'       => $_SESSION['session_usuario'],
    ]);


    echo json_encode(['ok' => true, 'id' => $hash]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
