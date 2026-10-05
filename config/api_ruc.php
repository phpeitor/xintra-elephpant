<?php
require_once __DIR__ . '/bootstrap.php';
require_once ROOT . '/controller/check_session.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.']);
    exit;
}

$ruc = trim((string)($_GET['ruc'] ?? ''));
if (!preg_match('/^\d{11}$/', $ruc)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'El RUC debe contener 11 dígitos.']);
    exit;
}

function crearUrlConsultaRuc(string $endpoint, string $ruc): ?string
{
    $endpoint = trim($endpoint);
    if ($endpoint === '' || !filter_var($endpoint, FILTER_VALIDATE_URL) || parse_url($endpoint, PHP_URL_SCHEME) !== 'https') {
        return null;
    }

    $encodedRuc = rawurlencode($ruc);
    if (str_contains($endpoint, '{ruc}')) {
        return str_replace('{ruc}', $encodedRuc, $endpoint);
    }
    if (str_contains($endpoint, '{documento}')) {
        return str_replace('{documento}', $encodedRuc, $endpoint);
    }
    if (str_contains($endpoint, '%s')) {
        return str_replace('%s', $encodedRuc, $endpoint);
    }
    if (preg_match('/[?&][^=&]+=$/', $endpoint) || str_ends_with($endpoint, '=')) {
        return $endpoint . $encodedRuc;
    }

    return $endpoint . (str_contains($endpoint, '?') ? '&' : '?') . 'ruc=' . $encodedRuc;
}

function solicitarApiRuc(string $url): ?array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return null;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
        CURLOPT_TIMEOUT => 8,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);
    $caBundle = __DIR__ . '/cacert.pem';
    if (is_file($caBundle)) {
        curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
    }

    $body = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hasError = curl_errno($ch) !== 0;
    curl_close($ch);

    if ($hasError || $httpCode < 200 || $httpCode >= 300 || !is_string($body)) {
        return null;
    }
    $decoded = json_decode($body, true);
    return is_array($decoded) ? $decoded : null;
}

function extraerNombreRuc(?array $response, string $ruc): ?string
{
    if (!$response || (array_key_exists('success', $response) && !$response['success'])) {
        return null;
    }

    $payload = $response['data'] ?? $response['result'] ?? $response;
    if (!is_array($payload)) {
        return null;
    }

    $documentoRespuesta = (string)($payload['numeroDocumento'] ?? $payload['numero_documento'] ?? $payload['ruc'] ?? '');
    $documentoRespuesta = preg_replace('/\D+/', '', $documentoRespuesta);
    if ($documentoRespuesta !== '' && $documentoRespuesta !== $ruc) {
        return null;
    }
    $tipoDocumento = (string)($payload['tipoDocumento'] ?? $payload['tipo_documento'] ?? '');
    if ($tipoDocumento !== '' && $tipoDocumento !== '6') {
        return null;
    }

    foreach (['nombre', 'name', 'razonSocial', 'razon_social', 'denominacion', 'nombre_o_razon_social'] as $key) {
        $nombre = trim((string)($payload[$key] ?? ''));
        if ($nombre !== '') {
            return mb_substr($nombre, 0, 100);
        }
    }
    return null;
}

$api1 = crearUrlConsultaRuc((string)($_ENV['API_RUC_URL'] ?? ''), $ruc);
$api2 = crearUrlConsultaRuc((string)($_ENV['API_RUC_URL_2'] ?? ''), $ruc);
if (!$api1 && !$api2) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Configura API_RUC_URL o API_RUC_URL_2 en .env.']);
    exit;
}

foreach ([$api1, $api2] as $url) {
    if (!$url) {
        continue;
    }
    $nombre = extraerNombreRuc(solicitarApiRuc($url), $ruc);
    if ($nombre !== null) {
        echo json_encode([
            'ok' => true,
            'nombre' => $nombre,
            'tipoDocumento' => '6',
            'numeroDocumento' => $ruc,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

http_response_code(404);
echo json_encode(['ok' => false, 'message' => 'No se encontró información para ese RUC en las APIs configuradas.'], JSON_UNESCAPED_UNICODE);
