<?php
session_start();
$max_inactive = 90 * 60;

if (!isset($_SESSION['session_usuario'])) {
    header('Location: ../index.html');
    exit;
}

if (isset($_SESSION['session_time'])) {
    $inactive = time() - $_SESSION['session_time'];

    if ($inactive > $max_inactive) {
        session_unset();
        session_destroy();
        header('Location: ../index.html');
        exit;
    } else {
        // refrescar tiempo
        $_SESSION['session_time'] = time();
    }
}
