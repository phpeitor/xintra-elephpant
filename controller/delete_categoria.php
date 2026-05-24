<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/categoria.php';

try {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(['ok' => false, 'message' => 'ID no válido']);
        exit;
    }

    $id = (int) $_POST['id'];
    $categoria = new Categoria();

    if ($categoria->baja($id)) {
        echo json_encode(['ok' => true, 'message' => 'Categoría de baja correctamente']);
    } else {
        echo json_encode(['ok' => false, 'message' => 'No se pudo dar de baja la categoría']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
