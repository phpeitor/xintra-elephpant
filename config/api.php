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

$baseUrl = $_ENV['UBUNTUX_API_URL'] ?? '';
if (!$baseUrl) {
    http_response_code(500);
    echo json_encode(["error" => "Falta configurar UBUNTUX_API_URL en .env"]);
    exit;
}

$apiUrl = rtrim($baseUrl, "=") . "=" . urlencode($dni);

function pickValue(array $source, array $keys, $default = "") {
    foreach ($keys as $key) {
        if (array_key_exists($key, $source) && $source[$key] !== null) {
            return $source[$key];
        }
    }
    return $default;
}

try {
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,  
        CURLOPT_CONNECTTIMEOUT => 5
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Error en cURL: " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("El servidor externo devolvió código HTTP $httpCode");
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new Exception("Respuesta inválida del servicio UBUNTUX");
    }

    $payload = $decoded;
    if (isset($decoded['found_patient']) && is_array($decoded['found_patient'])) {
        $payload = $decoded['found_patient'];
    } elseif (isset($decoded['data']) && is_array($decoded['data'])) {
        $payload = $decoded['data'];
    }

    $normalized = [
        "DNI" => (string) pickValue($payload, ["DNI", "dni", "document_number", "numero_documento"]),
        "VERIFICADOR" => (string) pickValue($payload, ["VERIFICADOR", "verificador"]),
        "UBIGEO" => (string) pickValue($payload, ["UBIGEO", "ubigeo"]),
        "PATERNO" => (string) pickValue($payload, ["PATERNO", "paterno", "apellido_paterno"]),
        "MATERNO" => (string) pickValue($payload, ["MATERNO", "materno", "apellido_materno"]),
        "NOMBRES" => (string) pickValue($payload, ["NOMBRES", "nombres", "name", "nombre"]),
        "NACIMIENTO" => (string) pickValue($payload, ["NACIMIENTO", "nacimiento", "birth_date"]),
        "SEXO" => (string) pickValue($payload, ["SEXO", "sexo", "gender"]),
        "TIPODOC" => (string) pickValue($payload, ["TIPODOC", "tipodoc", "document_type"]),
        "DIRECCION" => (string) pickValue($payload, ["DIRECCION", "direccion", "address"]),
        "DEPARTAMENTO" => (string) pickValue($payload, ["DEPARTAMENTO", "departamento"]),
        "PROVINCIA" => (string) pickValue($payload, ["PROVINCIA", "provincia"]),
        "DISTRITO" => (string) pickValue($payload, ["DISTRITO", "distrito"]),
        "GRADO" => (string) pickValue($payload, ["GRADO", "grado"]),
        "EST_CIVIL" => (string) pickValue($payload, ["EST_CIVIL", "est_civil", "estado_civil"]),
        "MADRE" => (string) pickValue($payload, ["MADRE", "madre"]),
        "PADRE" => (string) pickValue($payload, ["PADRE", "padre"]),
        "UBIGEONAC" => (string) pickValue($payload, ["UBIGEONAC", "ubigeonac"]),
        "DEPARTAMENTONAC" => (string) pickValue($payload, ["DEPARTAMENTONAC", "departamentonac"]),
        "PROVINCIANAC" => (string) pickValue($payload, ["PROVINCIANAC", "provincianac"]),
        "DISTRITONAC" => (string) pickValue($payload, ["DISTRITONAC", "distritonac"]),
        "FALLECIDO" => (string) pickValue($payload, ["FALLECIDO", "fallecido"], "False"),
    ];

    echo json_encode($normalized, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
