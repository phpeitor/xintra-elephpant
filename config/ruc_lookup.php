<?php

function crearUrlConsultaRuc(string $endpoint, string $ruc): ?string
{
    $endpoint = trim($endpoint);
    if ($endpoint === '' || !filter_var($endpoint, FILTER_VALIDATE_URL) || parse_url($endpoint, PHP_URL_SCHEME) !== 'https') {
        return null;
    }

    $encodedRuc = rawurlencode($ruc);
    foreach (['{ruc}', '{documento}', '%s'] as $placeholder) {
        if (str_contains($endpoint, $placeholder)) {
            return str_replace($placeholder, $encodedRuc, $endpoint);
        }
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
    $documentoRespuesta = preg_replace('/\D+/', '', (string)($payload['numeroDocumento'] ?? $payload['numero_documento'] ?? $payload['ruc'] ?? ''));
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

function consultarNombreRuc(string $ruc): ?string
{
    $urls = [
        crearUrlConsultaRuc((string)($_ENV['API_RUC_URL'] ?? ''), $ruc),
        crearUrlConsultaRuc((string)($_ENV['API_RUC_URL_2'] ?? ''), $ruc),
    ];
    foreach ($urls as $url) {
        if ($url === null) {
            continue;
        }
        $name = extraerNombreRuc(solicitarApiRuc($url), $ruc);
        if ($name !== null) {
            return $name;
        }
    }
    return null;
}

function porcentajeSimilitudNombres(string $primero, string $segundo): float
{
    $normalizar = static function (string $value): string {
        $value = mb_strtoupper(trim($value), 'UTF-8');
        $value = strtr($value, [
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
            'Ñ' => 'N',
        ]);
        $value = preg_replace('/[^A-Z0-9]+/', ' ', $value) ?? '';
        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    };
    $a = $normalizar($primero);
    $b = $normalizar($segundo);
    if ($a === '' || $b === '') {
        return 0.0;
    }
    similar_text($a, $b, $percentage);
    return round($percentage, 2);
}

function validarRucEmisorContraSucursal(string $ruc, string $nombreSucursal): array
{
    if (!preg_match('/^\d{11}$/', $ruc)) {
        throw new InvalidArgumentException('El RUC del emisor debe tener exactamente 11 dígitos.');
    }
    $nombreRuc = consultarNombreRuc($ruc);
    if ($nombreRuc === null) {
        throw new RuntimeException('No se pudo consultar el RUC en las APIs configuradas. Verifica el RUC y la disponibilidad de API_RUC_URL/API_RUC_URL_2.');
    }
    $similitud = porcentajeSimilitudNombres($nombreSucursal, $nombreRuc);
    return [
        'ruc' => $ruc,
        'nombre_ruc' => $nombreRuc,
        'similitud' => $similitud,
        'coincide' => $similitud >= 90.0,
    ];
}
