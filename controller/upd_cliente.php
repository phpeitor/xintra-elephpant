<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/cliente.php';

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
        'nombres'   => $_POST['nombres'] ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'documento' => $_POST['documento'] ?? '',
        'email'     => $_POST['email'] ?? '',
        'telefono'  => $_POST['telefono'] ?? '',
        'sexo'      => $_POST['sexo'] ?? 0,
    ];

    $cliente = new Cliente();
    $ok = $cliente->actualizarPorHash($hash, $data);

    echo json_encode([
        'ok' => $ok,
        'message' => $ok ? 'Cliente actualizado correctamente' : 'No se realizaron cambios'
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
