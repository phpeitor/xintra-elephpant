<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/item.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'message' => 'Método no permitido']);
        exit;
    }

    if (!isset($_POST['hash']) || strlen($_POST['hash']) !== 32) {
        echo json_encode(['ok' => false, 'message' => 'Hash no válido']);
        exit;
    }

    $hash = $_POST['hash'];

    $data = [
        'nombre'       => $_POST['nombre'] ?? '',
        'categoria'    => $_POST['categoria'] ?? '',
        'precio'       => $_POST['precio'] ?? '',
        'estado'       => ($_POST['estado'] ?? '0') === '1' ? 1 : 0,
    ];

    $item = new Item();
    $ok = $item->actualizarPorHash($hash, $data);

    echo json_encode([
        'ok' => $ok,
        'message' => $ok ? 'Item actualizado correctamente' : 'No se realizaron cambios'
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
