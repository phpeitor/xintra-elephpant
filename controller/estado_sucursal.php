<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/sucursal.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $estado = filter_var($_POST['estado'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id < 1 || $estado === false || !in_array($estado, [0, 1], true)) {
        throw new InvalidArgumentException('Datos de sucursal inválidos.');
    }
    $sucursal = new Sucursal();
    $ok = $sucursal->cambiarEstado((int)$id, (int)$estado);
    echo json_encode(['ok' => $ok, 'message' => $estado === 1 ? 'Sucursal reactivada.' : 'Sucursal dada de baja.'], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo cambiar el estado de la sucursal.'], JSON_UNESCAPED_UNICODE);
}
