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
    $turnstileToken = trim($_POST['cf-turnstile-response'] ?? '');

    if ($usuario === '' || $password === '') {
        throw new Exception('Debe ingresar usuario y contraseña.');
    }

    $turnstileSecret = $_ENV['TURNSTILE_SECRET_KEY'] ?? '';
    if ($turnstileSecret !== '') {
        if ($turnstileToken === '') {
            throw new Exception('Debe completar la verificación de seguridad.');
        }

        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        $caBundle = ROOT . '/config/cacert.pem';

        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => $turnstileSecret,
                'response' => $turnstileToken,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]),
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
        ];

        if (is_file($caBundle)) {
            $curlOptions[CURLOPT_CAINFO] = $caBundle;
        }

        curl_setopt_array($ch, $curlOptions);

        $turnstileResponse = curl_exec($ch);
        $turnstileError = curl_errno($ch);
        $turnstileErrorMessage = curl_error($ch);
        curl_close($ch);

        if ($turnstileError || !$turnstileResponse) {
            throw new Exception('No se pudo validar la verificación de seguridad: ' . $turnstileErrorMessage);
        }

        $turnstileData = json_decode($turnstileResponse, true);
        $expectedHostname = $_ENV['TURNSTILE_HOSTNAME'] ?? '';
        $hostname = $turnstileData['hostname'] ?? '';

        if (empty($turnstileData['success']) || ($expectedHostname !== '' && $hostname !== $expectedHostname)) {
            throw new Exception('Verificación de seguridad inválida.');
        }
    }

    $password = md5($password);
    $obj = new Usuario();
    $data = $obj->acceso_user([
        'usuario' => $usuario,
        'password' => $password
    ]);

    if ($data) {
        if ((int)($data['IDESTADO'] ?? 0) !== 1) {
            echo json_encode([
                'ok' => false,
                'message' => 'El usuario ha sido desactivado por inactividad'
            ]);
            exit;
        }

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
