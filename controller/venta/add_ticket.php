<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/item.php';

try {
    $nombre    = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $precio    = trim($_POST['precio'] ?? '');
    $stock     = trim($_POST['stock'] ?? '');
    $grupo     = trim($_POST['grupo'] ?? '');

    if ($nombre === '' || $categoria === '' || $precio === '' || $grupo === '') {
        throw new Exception('Faltan campos obligatorios.');
    }

    $item = new Item();
    $id = $item->guardar([
        'nombre'    => $nombre,
        'categoria' => $categoria,
        'precio'    => $precio,
        'stock'     => $stock,
        'grupo'     => $grupo,
    ]);

    if ($grupo === 'PRODUCTO') {
        $item->guardar_stock([
            'id_product'    => $id,
            'id_pedido'     => 0,
            'tipo'          => 'E',
            'stock'         => $stock,
            'user'         => $_SESSION['session_usuario'],
        ]);
    }

    echo json_encode(['ok' => true, 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
