<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../controller/check_session.php';
require_once __DIR__ . '/../../model/ticket.php';

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'message' => 'Método no permitido']);
        exit;
    }

    if (!isset($_POST['hash']) || strlen($_POST['hash']) !== 32) {
        echo json_encode(['ok' => false, 'message' => 'Hash no válido']);
        exit;
    }

    $hash = $_POST['hash'];
    $data = [
        'cliente'    => trim($_POST['cliente'] ?? ''),
        'usuario'    => trim($_POST['personal'] ?? ''),
        'fecha'      => trim($_POST['date'] ?? ''),
        'dscto'      => trim($_POST['descuento'] ?? '0'),
        'tipo_dscto' => trim($_POST['promo-code'] ?? 'NO APLICA'),
        'pago'       => trim($_POST['pago'] ?? ''),
    ];

    if ($data['cliente'] === '' || $data['usuario'] === '' || $data['fecha'] === '') {
        throw new Exception('Faltan campos obligatorios.');
    }

    $ticket = new Ticket();
    $ticket->beginTransaction();
    $ok = $ticket->actualizarPorHash($hash, $data);
    if (!$ok) {
        throw new Exception('No se pudo actualizar el ticket.');
    }

    $items = json_decode($_POST['items'] ?? '[]', true);
    if (!is_array($items)) {
        throw new Exception('Formato inválido en los ítems.');
    }

    if (!empty($items)) {
        $ticket->eliminar_pedido($hash);
        $ticket->eliminar_stock($hash);

        foreach ($items as $it) {
            $id_item = $it['id'];
            $cantidad = $it['cantidad'];
            $precio = $it['precio'];
            $subtotal = $it['subtotal'];

            if (isset($it['tipo']) && strtoupper($it['tipo']) === 'PRODUCTO') {
                $ticket->guardar_stock([
                    'id_product' => $id_item,
                    'id_pedido' => $hash,
                    'tipo' => 'S',
                    'stock' => $cantidad,
                    'user' => $_SESSION['session_usuario'],
                ]);
            }

            $ticket->guardar_detalle([
                'id_pedido' => $hash,
                'id_productservice' => $id_item,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ]);
        }
    }

   $ticket->commit();
   echo json_encode([
        'ok' => true,
        'id' => $hash,
        'message' => 'Ticket actualizado correctamente.'
    ]);
} catch (Throwable $e) {
    if (isset($ticket)) $ticket->rollback();
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ]);
}