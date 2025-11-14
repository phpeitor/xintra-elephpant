<?php
session_start();
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../config/bootstrap.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === '' || $password === '') {
        throw new Exception('Debe ingresar usuario y contraseña.');
    }

    $password = md5($password);
    $obj = new Usuario();
    $data = $obj->acceso_user([
        'usuario' => $usuario,
        'password' => $password
    ]);

    if ($data) {
        $_SESSION['session_usuario'] = $data['USUARIO'];
        $_SESSION['session_id'] = $data['IDPERSONAL'];
        $_SESSION['session_nombre'] = $data['NOMBRES'];
        $_SESSION['session_time'] = time(); 

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

        // Guardar registro de la sesión
        $obj->guardar_session([
            'tipo' => 'IN',
            'id_user' => $data['IDPERSONAL'],
            'ip' => $ip
        ]);
        
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'message' => '🚫 Usuario o contraseña incorrectos']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
