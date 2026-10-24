<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/compra.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        throw new InvalidArgumentException('ID de compra inválido.');
    }
    $purchase = (new Compra())->obtener((int)$id);
    if (!$purchase) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'Compra no encontrada en la sucursal actual.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(['ok' => true, 'data' => $purchase], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo consultar el detalle de compra.'], JSON_UNESCAPED_UNICODE);
}
