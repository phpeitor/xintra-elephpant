<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../controller/check_session.php';
require_once __DIR__ . '/../../config/bootstrap.php';

function parsePromoCodes(string $raw): array {
    $result = [];
    $raw = trim($raw);
    if ($raw === '') {
        return $result;
    }

    if (str_starts_with($raw, '[')) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            foreach ($decoded as $entry) {
                if (is_string($entry)) {
                    $code = strtoupper(trim($entry));
                    if ($code !== '') {
                        $result[$code] = 10.0;
                    }
                    continue;
                }

                if (is_array($entry)) {
                    $code = strtoupper(trim((string)($entry['code'] ?? $entry['codigo'] ?? '')));
                    if ($code === '') {
                        continue;
                    }
                    $percent = (float)($entry['percent'] ?? $entry['porcentaje'] ?? 10);
                    if ($percent <= 0) {
                        $percent = 10.0;
                    }
                    $result[$code] = $percent;
                }
            }
            return $result;
        }
    }

    $parts = array_filter(array_map('trim', explode(',', $raw)));
    foreach ($parts as $part) {
        $code = $part;
        $percent = 10.0;

        if (str_contains($part, ':')) {
            [$rawCode, $rawPercent] = array_map('trim', explode(':', $part, 2));
            $code = $rawCode;
            $percent = (float)$rawPercent;
            if ($percent <= 0) {
                $percent = 10.0;
            }
        }

        $code = strtoupper($code);
        if ($code !== '') {
            $result[$code] = $percent;
        }
    }

    return $result;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }

    $code = strtoupper(trim((string)($_GET['code'] ?? '')));
    $total = (float)($_GET['total'] ?? 0);

    if ($code === '') {
        throw new Exception('Ingresa un código promocional.');
    }

    $promoRaw = (string)($_ENV['PROMOCODE'] ?? '');
    $promoMap = parsePromoCodes($promoRaw);

    if (!isset($promoMap[$code])) {
        echo json_encode([
            'ok' => false,
            'message' => 'Código promocional no válido.'
        ]);
        exit;
    }

    $percent = (float)$promoMap[$code];
    $discount = round(max(0, $total) * ($percent / 100), 2);

    echo json_encode([
        'ok' => true,
        'code' => $code,
        'percent' => $percent,
        'discount' => $discount,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
