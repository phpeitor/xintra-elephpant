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

    $id = filter_var($_POST['id_sucursal'] ?? $_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $incremento = filter_var($_POST['incremento'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id < 1 || $incremento === false || $incremento < 1) {
        throw new InvalidArgumentException('Indica una sucursal y una cantidad entera positiva.');
    }

    $sucursal = new Sucursal();
    $result = $sucursal->aumentarCuota(
        (int)$id,
        (int)$incremento,
        (int)$_SESSION['session_id'],
        trim((string)($_POST['motivo'] ?? ''))
    );
    echo json_encode(['ok' => true, 'data' => $result, 'message' => 'Cuota incrementada y movimiento registrado.'], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo guardar el incremento. Verifica que la migración de cuotas esté aplicada.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
