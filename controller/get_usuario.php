<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/usuario.php';

try {
    if (!isset($_GET['hash']) || strlen($_GET['hash']) !== 32) {
        echo json_encode(['ok' => false, 'message' => 'Hash no válido']);
        exit;
    }

    $hash = $_GET['hash'];
    $usuario = new Usuario();

    $data = $usuario->obtenerPorHash($hash);

    if ($data) {
        echo json_encode(['ok' => true, 'data' => $data]);
    } else {
        echo json_encode(['ok' => false, 'message' => 'Usuario no encontrado']);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
