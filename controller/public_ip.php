<?php
require_once __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, max-age=300');

$apiUrl = trim((string)($_ENV['IP_API_URL'] ?? ''));
if ($apiUrl === '' || !function_exists('curl_init')) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'ip' => null]);
    exit;
}

$curl = curl_init($apiUrl);
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_FOLLOWLOCATION => false,
]);
$response = curl_exec($curl);
$status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_errno($curl);
curl_close($curl);

$ip = $curlError === 0 && $status === 200 ? trim((string)$response) : '';
if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'ip' => null]);
    exit;
}

echo json_encode(['ok' => true, 'ip' => $ip], JSON_UNESCAPED_SLASHES);
