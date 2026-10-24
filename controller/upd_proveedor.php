<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/proveedor.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        throw new InvalidArgumentException('ID de proveedor inválido.');
    }
    $ok = (new Proveedor())->actualizar((int)$id, $_POST);
    echo json_encode(['ok' => $ok, 'message' => $ok ? 'Proveedor actualizado correctamente.' : 'No se realizaron cambios.'], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code($e->getCode() === '23000' ? 409 : 500);
    echo json_encode(['ok' => false, 'message' => $e->getCode() === '23000'
        ? 'Ya existe un proveedor con ese documento en esta sucursal.'
        : 'No se pudo actualizar el proveedor.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo actualizar el proveedor.'], JSON_UNESCAPED_UNICODE);
}
