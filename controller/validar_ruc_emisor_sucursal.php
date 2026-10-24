<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/sucursal.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $idSucursal = filter_var($_GET['id_sucursal'] ?? null, FILTER_VALIDATE_INT);
    $ruc = preg_replace('/\D+/', '', (string)($_GET['ruc'] ?? ''));
    if ($idSucursal === false || $idSucursal < 1) {
        throw new InvalidArgumentException('ID de sucursal inválido.');
    }
    if (!preg_match('/^\d{11}$/', $ruc)) {
        throw new InvalidArgumentException('El RUC debe contener exactamente 11 dígitos.');
    }

    $result = (new Sucursal())->validarRucEmisorSucursal((int)$idSucursal, $ruc);
    echo json_encode([
        'ok' => true,
        'data' => $result,
        'message' => $result['coincide']
            ? 'RUC verificado: el nombre coincide con la sucursal.'
            : 'El nombre consultado no coincide al 90% con el nombre de la sucursal.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (RuntimeException $e) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(502);
    error_log('Error validando RUC emisor por sucursal: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'message' => 'No se pudo consultar el RUC. Revisa las APIs configuradas.'], JSON_UNESCAPED_UNICODE);
}
