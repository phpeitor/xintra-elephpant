<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/categoria.php';

try {
    if (!isset($_GET['hash']) || strlen($_GET['hash']) !== 32) {
        echo json_encode(['ok' => false, 'message' => 'Hash no válido']);
        exit;
    }

    $hash = $_GET['hash'];
    $categoria = new Categoria();
    $data = $categoria->obtenerPorHash($hash);

    if ($data) {
        echo json_encode(['ok' => true, 'data' => $data]);
    } else {
        echo json_encode(['ok' => false, 'message' => 'Categoría no encontrada']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
