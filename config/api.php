<?php
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

$dni = $_GET['dni'] ?? null;

if (!$dni || !preg_match('/^\d{8,11}$/', $dni)) {
    http_response_code(400);
    echo json_encode(["error" => "Número de documento inválido"]);
    exit;
}

$apiUrl = $_ENV['API_CLINIC_STATUS_URL'] 
    . "?health_worker_id=" . $_ENV['HEALTH_WORKER_ID']
    . "&document_type_id=" . $_ENV['DOCUMENT_TYPE_ID']
    . "&document_number=" . urlencode($dni);

try {
    $response = file_get_contents($apiUrl);

    if ($response === false) {
        throw new Exception("No se pudo obtener respuesta del servidor externo.");
    }

    echo $response; 
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
