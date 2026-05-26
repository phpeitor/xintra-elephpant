<?php
$sessionStatus = session_status();
if ($sessionStatus !== PHP_SESSION_ACTIVE) {
    session_start();
}

$max_inactive = 90 * 60;

if (!isset($_SESSION['session_usuario'])) {
    header('Location: /xintra-elephpant/index.php');
    exit;
}

if (isset($_SESSION['session_time'])) {
    $inactive = time() - $_SESSION['session_time'];

    if ($inactive > $max_inactive) {
        session_unset();
        session_destroy();
        header('Location: /xintra-elephpant/index.php');
        exit;
    } else {
        $_SESSION['session_time'] = time();
    }
}
