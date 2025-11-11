<?php
require_once __DIR__ . '/../model/usuario.php';
session_start();

if (isset($_SESSION['session_id'])) {
    $obj = new Usuario();
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }

    if (!$ip) {
        $ip = @file_get_contents('https://api.ipify.org');
    }

    $obj->guardar_session([
        'tipo' => 'OUT',
        'id_user' => $_SESSION['session_id'],
        'ip' => $ip
    ]);
}

session_unset();
$_SESSION = [];
session_destroy();
header("Location: ../index.php");
exit;
