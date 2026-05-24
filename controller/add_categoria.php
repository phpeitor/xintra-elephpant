<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controller/check_session.php';
require_once __DIR__ . '/../model/categoria.php';

try {
    $tpo = trim($_POST['tpo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $estado = ($_POST['estado'] ?? '1') === '1' ? 1 : 0;

    if ($tpo === '' || $nombre === '') {
        throw new Exception('Faltan campos obligatorios.');
    }

    $categoria = new Categoria();
    $existe = $categoria->valida_categoria($tpo, $nombre);
    if ($existe) {
        echo json_encode(['ok' => false, 'message' => 'La categoría ya existe']);
        exit;
    }

    $id = $categoria->guardar([
        'tpo' => $tpo,
        'nombre' => $nombre,
        'estado' => $estado,
    ]);

    echo json_encode(['ok' => true, 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
