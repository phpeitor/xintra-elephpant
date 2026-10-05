<?php
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../model/permisos.php';
require_once __DIR__ . '/../database/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $actorId = (int)($_SESSION['session_id'] ?? 0);
    $permisos = new Permisos();
    if (!$permisos->tiene($actorId, 'usuarios')) {
        http_response_code(403);
        throw new RuntimeException('No tienes permiso para administrar usuarios.');
    }
    $puedeAdministrarSucursal = $permisos->esAdminOCargoUno($actorId);

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id === $actorId) {
        http_response_code(400);
        throw new InvalidArgumentException('Selecciona otro usuario válido.');
    }
    $conexion = (new Conexion())->conectar();
    $stmt = $conexion->prepare('SELECT 1 FROM personal WHERE IDPERSONAL = :id AND IDSUCURSAL = @id_sucursal LIMIT 1');
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetchColumn()) {
        http_response_code(404);
        throw new RuntimeException('No se encontró al usuario en la sucursal actual.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $seleccionados = $_POST['permisos'] ?? [];
        if (!is_array($seleccionados)) throw new InvalidArgumentException('La lista de permisos no es válida.');
        if (!$puedeAdministrarSucursal && $permisos->tiene((int)$id, 'sucursales')) {
            $seleccionados[] = 'sucursales';
        }
        $permisos->asignar((int)$id, $seleccionados);
        echo json_encode(['ok' => true, 'message' => 'Permisos actualizados.']);
        exit;
    }

    $catalogo = Permisos::CATALOGO;
    if (!$puedeAdministrarSucursal) unset($catalogo['sucursales']);

    echo json_encode([
        'ok' => true,
        'data' => [
            'catalogo' => $catalogo,
            'permisos' => $permisos->permitidos((int)$id),
        ],
    ]);
} catch (Throwable $e) {
    if (http_response_code() < 400) http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
