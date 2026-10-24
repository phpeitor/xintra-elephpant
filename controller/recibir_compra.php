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
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        throw new InvalidArgumentException('ID de compra inválido.');
    }
    $result = (new Compra())->recibir(
        (int)$id,
        (int)($_SESSION['session_id'] ?? 0),
        (string)($_SESSION['session_usuario'] ?? 'usuario')
    );
    echo json_encode([
        'ok' => true,
        'ya_recibida' => $result['ya_recibida'],
        'message' => $result['ya_recibida']
            ? 'La compra ya había sido recibida; el inventario no se duplicó.'
            : 'Recepción confirmada. Se agregó el stock de los productos de la compra.',
    ], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo recibir la compra. Aplica las migraciones de compras y recepción de inventario.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(409);
    echo json_encode(['ok' => false, 'message' => $e->getMessage() ?: 'No se pudo confirmar la recepción.'], JSON_UNESCAPED_UNICODE);
}
