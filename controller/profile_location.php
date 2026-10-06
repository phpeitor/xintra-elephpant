<?php
require_once __DIR__ . '/check_session.php';
require_once __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, max-age=300');

function fetchProfileLocationApi(string $url): string|array|null
{
    if (!function_exists('curl_init')) return null;

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 7,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $response = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_errno($curl);
    curl_close($curl);

    if ($error !== 0 || $status !== 200 || !is_string($response)) return null;
    $data = json_decode($response, true);
    return is_array($data) ? $data : trim($response);
}

try {
    $ipApiUrl = trim((string)($_ENV['IP_API_URL'] ?? ''));
    $geoApiUrl = trim((string)($_ENV['IP_API_URL_2'] ?? ''));
    if ($ipApiUrl === '' || $geoApiUrl === '') {
        throw new RuntimeException('No está configurado el servicio de geolocalización.');
    }

    $ipResponse = fetchProfileLocationApi($ipApiUrl);
    $ip = is_string($ipResponse) ? trim($ipResponse) : '';
    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
        throw new RuntimeException('No se pudo determinar la IP pública.');
    }

    if (str_contains($geoApiUrl, '{ip}')) {
        $geoUrl = str_replace('{ip}', rawurlencode($ip), $geoApiUrl);
    } else {
        $geoBase = rtrim($geoApiUrl, '/');
        $lastSegment = basename((string)(parse_url($geoBase, PHP_URL_PATH) ?? ''));
        if (filter_var($lastSegment, FILTER_VALIDATE_IP) !== false) {
            $geoBase = substr($geoBase, 0, -strlen($lastSegment));
            $geoUrl = rtrim($geoBase, '/') . '/' . rawurlencode($ip);
        } else {
            $geoUrl = $geoBase . '/' . rawurlencode($ip);
        }
    }

    $separator = str_contains($geoUrl, '?') ? '&' : '?';
    $geoUrl .= $separator . 'fields=status,message,query,country,countryCode,regionName,city,lat,lon,timezone,isp';
    $location = fetchProfileLocationApi($geoUrl);
    if (!$location || ($location['status'] ?? '') !== 'success') {
        throw new RuntimeException('El servicio no devolvió datos de ubicación.');
    }

    $lat = filter_var($location['lat'] ?? null, FILTER_VALIDATE_FLOAT);
    $lon = filter_var($location['lon'] ?? null, FILTER_VALIDATE_FLOAT);
    if ($lat === false || $lon === false || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
        throw new RuntimeException('El servicio devolvió coordenadas inválidas.');
    }

    echo json_encode([
        'ok' => true,
        'ip' => $ip,
        'location' => [
            'country' => (string)($location['country'] ?? ''),
            'countryCode' => (string)($location['countryCode'] ?? ''),
            'regionName' => (string)($location['regionName'] ?? ''),
            'city' => (string)($location['city'] ?? ''),
            'lat' => (float)$lat,
            'lon' => (float)$lon,
            'timezone' => (string)($location['timezone'] ?? ''),
            'isp' => (string)($location['isp'] ?? ''),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'message' => 'No se pudieron obtener los datos de ubicación.']);
}
