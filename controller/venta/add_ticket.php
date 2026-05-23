<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../controller/check_session.php';
require_once __DIR__ . '/../../model/ticket.php';
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
    $cliente    = trim($_POST['cliente'] ?? '');
    $usuario    = trim($_POST['personal'] ?? '');
    $fecha      = trim($_POST['date'] ?? '');
    $promoCode  = strtoupper(trim($_POST['promo-code'] ?? ''));
    $dscto      = 0;
    $tipo_dscto = 'NO APLICA';
    $pago       = trim($_POST['pago'] ?? '');
    $items      = json_decode($_POST['items'] ?? '[]', true);

    if ($cliente === '' || $usuario === '' || $fecha === '') {
        throw new Exception('Faltan campos obligatorios.');
    }

    if (!is_array($items)) {
        throw new Exception('Formato inválido en los ítems.');
    }

    $totalItems = 0.0;
    foreach ($items as $it) {
        $totalItems += (float)($it['subtotal'] ?? 0);
    }

    if ($promoCode !== '') {
        $promoMap = parsePromoCodes((string)($_ENV['PROMOCODE'] ?? ''));
        if (!isset($promoMap[$promoCode])) {
            throw new Exception('Código promocional no válido.');
        }

        $percent = (float)$promoMap[$promoCode];
        $dscto = round($totalItems * ($percent / 100), 2);
        $tipo_dscto = $promoCode;
    }

    $ticket = new Ticket();
    $id = $ticket->guardar([
        'cliente'       => $cliente,
        'usuario'       => $usuario,
        'user_registro' => $_SESSION['session_id'],
        'fecha'         => $fecha,
        'dscto'         => $dscto,
        'tipo_dscto'    => $tipo_dscto,
        'pago'          => $pago,
    ]);

    if (!empty($items)) {
        foreach ($items as $it) {
            $id_item = $it['id'];
            $cantidad = $it['cantidad'];
            $precio = $it['precio'];
            $subtotal = $it['subtotal'];

            if (isset($it['tipo']) && strtoupper($it['tipo']) === 'PRODUCTO') {
                $ticket->guardar_stock([
                    'id_product' => $id_item,
                    'id_pedido' => $id,
                    'tipo' => 'S',
                    'stock' => $cantidad,
                    'user' => $_SESSION['session_usuario'],
                ]);
            }

            $ticket->guardar_detalle([
                'id_pedido' => $id,
                'id_productservice' => $id_item,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ]);
        }
    }

    echo json_encode(['ok' => true, 'id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}
