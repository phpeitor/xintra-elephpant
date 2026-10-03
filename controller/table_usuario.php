<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/usuario.php';

try {
    $cli = new Usuario();
    $estado = strtoupper(trim($_GET['estado'] ?? 'ACTIVOS'));
    if (!in_array($estado, ['ACTIVOS', 'INACTIVOS', 'TODOS'], true)) {
        $estado = 'ACTIVOS';
    }
    $data = $cli->table_personal($estado);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
