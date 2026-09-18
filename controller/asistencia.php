<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/asistencia.php';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'events';
    $asistencia = new Asistencia();

    if ($action === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode(['ok' => true, 'data' => $asistencia->usuarios()]);
        exit;
    }
    if ($action === 'status' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $idUsuario = filter_var($_GET['usuario'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (!$idUsuario) throw new RuntimeException('Selecciona un usuario.');
        echo json_encode(['ok' => true, 'estado' => $asistencia->estado($idUsuario)]);
        exit;
    }
    if ($action === 'activity' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $idUsuario = filter_var($_GET['usuario'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        echo json_encode(['ok' => true, 'data' => $asistencia->recientes($idUsuario)]);
        exit;
    }
    if ($action === 'mark' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $idUsuario = filter_var($_POST['usuario'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (!$idUsuario) throw new RuntimeException('Selecciona un usuario.');
        $data = $asistencia->registrar($idUsuario, strtoupper(trim($_POST['tipo'] ?? '')));
        echo json_encode(['ok' => true, 'message' => $data['tipo'] === 'ENTRADA' ? 'Entrada registrada correctamente.' : 'Salida registrada correctamente.', 'data' => $data]);
        exit;
    }
    if ($action !== 'events' || $_SERVER['REQUEST_METHOD'] !== 'GET') throw new RuntimeException('Solicitud no válida.');

    $inicio = trim($_GET['start'] ?? '');
    $fin = trim($_GET['end'] ?? '');
    if ($inicio === '' || $fin === '') throw new RuntimeException('El rango de fechas es obligatorio.');
    // The database stores attendance in Lima time; preserve calendar boundaries exactly.
    $inicioDate = new DateTimeImmutable($inicio, new DateTimeZone('UTC'));
    $finDate = new DateTimeImmutable($fin, new DateTimeZone('UTC'));
    if ($finDate <= $inicioDate || $finDate->diff($inicioDate)->days > 62) {
        throw new RuntimeException('El rango de consulta no puede superar los dos meses.');
    }
    $idUsuario = filter_var($_GET['usuario'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    echo json_encode(['ok' => true, 'data' => $asistencia->eventos($inicioDate->format('Y-m-d H:i:s'), $finDate->format('Y-m-d H:i:s'), $idUsuario)]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
