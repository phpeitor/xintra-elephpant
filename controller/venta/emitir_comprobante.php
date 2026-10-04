<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../controller/check_session.php';
require_once __DIR__ . '/../../model/comprobante_electronico.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Método no permitido.');
    }

    $hash = trim((string)($_POST['hash'] ?? ''));
    $accion = trim((string)($_POST['accion'] ?? 'emitir'));
    if (!preg_match('/^[a-f0-9]{32}$/i', $hash)) {
        throw new InvalidArgumentException('Hash de ticket inválido.');
    }

    $comprobante = new ComprobanteElectronico();
    if ($accion === 'consultar') {
        $result = $comprobante->consultar($hash);
    } else {
        $tipo = filter_var($_POST['tipo'] ?? null, FILTER_VALIDATE_INT);
        if ($tipo === false || !in_array((int)$tipo, [1, 2], true)) {
            throw new InvalidArgumentException('Selecciona factura o boleta.');
        }
        $result = $comprobante->emitir($hash, (int)$tipo, [
            'denominacion' => trim((string)($_POST['denominacion'] ?? '')),
            'direccion' => trim((string)($_POST['direccion'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
        ], (int)$_SESSION['session_id']);
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'No se pudo guardar el estado del comprobante. Verifica que la migración NubeFact esté aplicada.',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    if (http_response_code() < 400) {
        http_response_code(400);
    }
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
