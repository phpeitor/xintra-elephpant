<?php
$sessionStatus = session_status();
if ($sessionStatus !== PHP_SESSION_ACTIVE) {
    session_start();
}

$max_inactive = 90 * 60;

if (!isset($_SESSION['session_usuario'])) {
    header('Location: ./index.php');
    exit;
}

require_once __DIR__ . '/../model/permisos.php';
$permisoRuta = Permisos::permisoRuta($_SERVER['SCRIPT_NAME'] ?? '');
if ($permisoRuta !== null) {
    try {
        $permisosUsuario = new Permisos();
        if (!$permisosUsuario->tiene((int)($_SESSION['session_id'] ?? 0), $permisoRuta)) {
            http_response_code(403);
            if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/controller/')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'message' => 'No tienes permiso para realizar esta acción.']);
            } else {
                require ROOT . '/404.html';
            }
            exit;
        }
    } catch (Throwable $e) {
        http_response_code(503);
        exit('No se pudo validar el permiso de acceso.');
    }
}

if (isset($_SESSION['session_time'])) {
    $inactive = time() - $_SESSION['session_time'];

    if ($inactive > $max_inactive) {
        session_unset();
        session_destroy();
        header('Location: ./index.php');
        exit;
    } else {
        $_SESSION['session_time'] = time();
    }
}
