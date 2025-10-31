<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/usuario.php';

try {
    $cli = new Usuario();

    $payload = [
        'nombres'   => $_POST['nombres']   ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'email'     => $_POST['email']     ?? '',
        'documento' => $_POST['documento'] ?? '',
        'telefono'  => $_POST['telefono']  ?? '',
        'sexo'      => $_POST['sexo']      ?? '',
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
