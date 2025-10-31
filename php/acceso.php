<?php
session_start();
require_once __DIR__ . "/usuario.php";

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
