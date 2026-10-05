<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/usuario.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $permisos = new Permisos();
    if (!$permisos->esAdminOCargoUno((int)($_SESSION['session_id'] ?? 0))) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'No tienes permiso para cambiar sucursales o cargos.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $usuario = new Usuario();
    echo json_encode(['ok' => true, 'data' => $usuario->catalogosSucursalCargo()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudieron cargar sucursales y cargos.'], JSON_UNESCAPED_UNICODE);
}
