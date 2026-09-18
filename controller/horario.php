<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/horario.php';

try {
    $hash = (string)($_GET['hash'] ?? $_POST['hash'] ?? '');
    if (!preg_match('/^[a-f0-9]{32}$/', $hash)) {
        throw new RuntimeException('Hash no válido.');
    }

    $horario = new Horario();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $rows = $horario->obtenerPorHash($hash);
        $nombre = $rows[0]['nombre'] ?? null;
        echo json_encode(['ok' => (bool)$nombre, 'nombre' => $nombre, 'data' => $rows]);
        exit;
    }

    $data = json_decode($_POST['horarios'] ?? '[]', true);
    if (!is_array($data) || count($data) !== 7) {
        throw new RuntimeException('El horario debe contener los siete días.');
    }

    $horario->guardarPorHash($hash, $data);
    echo json_encode(['ok' => true, 'message' => 'Horario guardado correctamente.']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
