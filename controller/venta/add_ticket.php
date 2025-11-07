<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../controller/check_session.php';
require_once __DIR__ . '/../../model/ticket.php';

try {
    $cliente    = trim($_POST['cliente'] ?? '');
    $usuario    = trim($_POST['personal'] ?? '');
    $fecha      = trim($_POST['date'] ?? '');
    $dscto      = trim($_POST['descuento'] ?? '0');
    $tipo_dscto = trim($_POST['promo-code'] ?? 'NO APLICA');
    $pago       = trim($_POST['pago'] ?? '');

    if ($cliente === '' || $usuario === '' || $fecha === '') {
        throw new Exception('Faltan campos obligatorios.');
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

    $items = json_decode($_POST['items'] ?? '[]', true);
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
