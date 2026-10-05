<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/permisos.php';

try {
    $permisos = new Permisos();
    if (!$permisos->esAdminOCargoUno((int)($_SESSION['session_id'] ?? 0))) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'No tienes permiso para cambiar sucursales o cargos.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $idPersonal = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $idSucursal = filter_var($_POST['id_sucursal'] ?? null, FILTER_VALIDATE_INT);
    $idCargo = filter_var($_POST['cargo'] ?? null, FILTER_VALIDATE_INT);
    if ($idPersonal === false || $idPersonal < 1 || $idSucursal === false || $idSucursal < 1 || $idCargo === false || $idCargo < 1) {
        throw new InvalidArgumentException('Selecciona un usuario, una sucursal y un cargo válidos.');
    }

    $usuario = new Usuario();
    $usuario->actualizarSucursalCargo((int)$idPersonal, (int)$idSucursal, (int)$idCargo, (int)$_SESSION['session_id']);
    echo json_encode(['ok' => true, 'message' => 'Sucursal y cargo del usuario actualizados. Si el usuario tiene sesión abierta, debe volver a iniciar sesión para cargar su nueva sucursal.'], JSON_UNESCAPED_UNICODE);
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
