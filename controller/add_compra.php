<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/compra.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $id = (new Compra())->guardar($_POST, (int)($_SESSION['session_id'] ?? 0));
    echo json_encode(['ok' => true, 'id' => $id, 'message' => 'Compra registrada como pendiente de ingreso a inventario.'], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    $duplicate = $e->getCode() === '23000';
    http_response_code($duplicate ? 409 : 500);
    echo json_encode(['ok' => false, 'message' => $duplicate
        ? 'Ese comprobante ya está registrado para esta sucursal.'
        : 'No se pudo registrar la compra. Revisa que la migración de compras esté aplicada.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo registrar la compra.'], JSON_UNESCAPED_UNICODE);
}
