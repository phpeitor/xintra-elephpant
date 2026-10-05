<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/sucursal.php';

try {
    $sucursalId = filter_var($_GET['id'] ?? $_POST['id_sucursal'] ?? null, FILTER_VALIDATE_INT);
    if ($sucursalId === false || $sucursalId < 1) {
        throw new InvalidArgumentException('ID de sucursal inválido.');
    }

    $sucursal = new Sucursal();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode([
            'ok' => true,
            'data' => $sucursal->configuracionNubefact((int)$sucursalId),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sucursal->guardarConfiguracionNubefact((int)$sucursalId, (int)$_SESSION['session_id'], $_POST);
    echo json_encode(['ok' => true, 'message' => 'Configuración NubeFact guardada para esta sucursal.'], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo guardar la configuración. Aplica primero la migración NubeFact por sucursal.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo procesar la configuración NubeFact.'], JSON_UNESCAPED_UNICODE);
}
