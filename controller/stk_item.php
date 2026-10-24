<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/item.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $hash = trim((string)($_GET['hash'] ?? ''));
    if (!preg_match('/^[a-f0-9]{32}$/i', $hash)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Hash no válido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $item = new Item();
    if (!$item->obtenerPorHash($hash)) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'Item no encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $data = $item->GraficoPorHash($hash);
    $timeline = $item->TimelineStockPorHash($hash);
    echo json_encode([
        'ok' => true,
        'data' => $data ?? [],
        'timeline' => $timeline,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'No se pudo consultar el historial de stock.'
    ], JSON_UNESCAPED_UNICODE);
}
