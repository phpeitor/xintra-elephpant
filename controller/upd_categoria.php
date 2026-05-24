<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/categoria.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'message' => 'Método no permitido']);
        exit;
    }

    if (!isset($_POST['hash']) || strlen($_POST['hash']) !== 32) {
        echo json_encode(['ok' => false, 'message' => 'Hash no válido']);
        exit;
    }

    $hash = $_POST['hash'];
    $tpo = trim($_POST['tpo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $estado = ($_POST['estado'] ?? '1') === '1' ? 1 : 0;

    if ($tpo === '' || $nombre === '') {
        throw new Exception('Faltan campos obligatorios.');
    }

    $categoria = new Categoria();
    $existe = $categoria->valida_categoria_upd($tpo, $nombre, $hash);
    if ($existe) {
        echo json_encode(['ok' => false, 'message' => 'La categoría ya existe en otro registro']);
        exit;
    }

    $ok = $categoria->actualizarPorHash($hash, [
        'tpo' => $tpo,
        'nombre' => $nombre,
        'estado' => $estado,
    ]);

    echo json_encode([
        'ok' => $ok,
        'message' => $ok ? 'Categoría actualizada correctamente' : 'No se realizaron cambios'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
