<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/cliente.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cli = new Cliente();

    $payload = [
        'nombres'   => $_POST['nombres']   ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
        'email'     => $_POST['email']     ?? '',
        'documento' => $_POST['documento'] ?? '',
        'telefono'  => $_POST['telefono']  ?? '',
        'sexo'      => $_POST['sexo']      ?? '',
    ];

    $id = $cli->guardar($payload);
    echo json_encode(['ok' => true, 'id' => $id]);

} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
