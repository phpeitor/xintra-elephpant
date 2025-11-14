<?php
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../config/bootstrap.php';
session_start();

if (isset($_SESSION['session_id'])) {
    $obj = new Usuario();

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }

    if (!$ip) {
        $apiUrl = $_ENV['IP_API_URL'];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $ip = '0.0.0.0';
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $ip = $httpCode === 200 ? trim($response) : '0.0.0.0';
        }

        curl_close($ch);
    }

    // Registrar salida
    $obj->guardar_session([
        'tipo' => 'OUT',
        'id_user' => $_SESSION['session_id'],
        'ip' => $ip
    ]);
}

// Eliminar la sesión
session_unset();
$_SESSION = [];
session_destroy();

header("Location: ../index.php");
exit;
