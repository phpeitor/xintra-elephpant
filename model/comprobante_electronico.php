<?php
require_once __DIR__ . '/../database/conexion.php';

class ComprobanteElectronico
{
    private PDO $conn;
    private string $nowLima;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
        $this->nowLima = (new DateTimeImmutable('now', new DateTimeZone('America/Lima')))
            ->format('Y-m-d H:i:s');
    }

    public function emitir(string $hashTicket, int $tipo, array $datosCliente, int $idUsuario): array
    {
        if (!in_array($tipo, [1, 2], true)) {
            throw new InvalidArgumentException('Selecciona boleta o factura.');
        }

        $config = $this->configuracion();
        $ticket = $this->obtenerTicket($hashTicket);
        if (!$ticket) {
            throw new RuntimeException('Ticket no encontrado en la sucursal actual.');
        }

        $this->conn->beginTransaction();
        try {
            $existingStmt = $this->conn->prepare(
                'SELECT * FROM nubefact_comprobante WHERE id_pedido = :id_pedido LIMIT 1 FOR UPDATE'
            );
            $existingStmt->bindValue(':id_pedido', (int)$ticket['id'], PDO::PARAM_INT);
            $existingStmt->execute();
            $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing && $existing['estado'] === 'EMITIDO') {
                $this->conn->commit();
                return $this->resultadoExistente($existing);
            }
            if ($existing && $existing['estado'] === 'PENDIENTE') {
                throw new RuntimeException('La emisión de este ticket ya está en proceso. Actualiza el listado antes de reintentar.');
            }

            if ($existing && $existing['estado'] === 'ERROR') {
                $payload = json_decode((string)$existing['request_json'], true);
                if (!is_array($payload)) {
                    throw new RuntimeException('No se pudo recuperar la solicitud pendiente para reintento.');
                }
                $update = $this->conn->prepare(
                    "UPDATE nubefact_comprobante SET estado = 'PENDIENTE', mensaje = NULL, fecha_respuesta = NULL WHERE id = :id"
                );
                $update->bindValue(':id', (int)$existing['id'], PDO::PARAM_INT);
                $update->execute();
                $comprobanteId = (int)$existing['id'];
            } else {
                $serie = $tipo === 1 ? $config['serie_factura'] : $config['serie_boleta'];
                $payloadBase = $this->construirPayload($ticket, $tipo, $serie, $datosCliente);
                $primerNumero = $tipo === 1 ? $config['primer_numero_factura'] : $config['primer_numero_boleta'];
                $numero = $this->reservarNumero($tipo, $serie, $primerNumero);
                $payloadBase['numero'] = $numero;
                $payloadBase['codigo_unico'] = 'X' . $ticket['id'] . $tipo;
                $payload = $payloadBase;
                $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

                $insert = $this->conn->prepare(
                    "INSERT INTO nubefact_comprobante
                        (id_pedido, tipo_de_comprobante, serie, numero, codigo_unico, estado, request_json, id_usuario_emision, fecha_emision)
                     VALUES
                        (:id_pedido, :tipo, :serie, :numero, :codigo_unico, 'PENDIENTE', :request_json, :id_usuario, :fecha_emision)"
                );
                $insert->bindValue(':id_pedido', (int)$ticket['id'], PDO::PARAM_INT);
                $insert->bindValue(':tipo', $tipo, PDO::PARAM_INT);
                $insert->bindValue(':serie', $serie);
                $insert->bindValue(':numero', $numero, PDO::PARAM_INT);
                $insert->bindValue(':codigo_unico', $payload['codigo_unico']);
                $insert->bindValue(':request_json', $json);
                $insert->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
                $insert->bindValue(':fecha_emision', $this->nowLima);
                $insert->execute();
                $comprobanteId = (int)$this->conn->lastInsertId();
            }
            $this->conn->commit();
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }

        return $this->enviarNubeFact($comprobanteId, $payload, $config);
    }

    public function consultar(string $hashTicket): array
    {
        $config = $this->configuracion();
        $ticket = $this->obtenerTicket($hashTicket);
        if (!$ticket) {
            throw new RuntimeException('Ticket no encontrado en la sucursal actual.');
        }

        $stmt = $this->conn->prepare('SELECT * FROM nubefact_comprobante WHERE id_pedido = :id_pedido LIMIT 1');
        $stmt->bindValue(':id_pedido', (int)$ticket['id'], PDO::PARAM_INT);
        $stmt->execute();
        $comprobante = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$comprobante || $comprobante['estado'] !== 'EMITIDO') {
            throw new RuntimeException('No hay un comprobante emitido para consultar.');
        }

        $payload = [
            'operacion' => 'consultar_comprobante',
            'tipo_de_comprobante' => (int)$comprobante['tipo_de_comprobante'],
            'serie' => (string)$comprobante['serie'],
            'numero' => (int)$comprobante['numero'],
        ];
        $ch = curl_init($config['url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => [
                'Authorization: Token token="' . $config['token'] . '"',
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $caBundle = dirname(__DIR__) . '/config/cacert.pem';
        if (is_file($caBundle)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }

        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($response)) {
            throw new RuntimeException($curlError !== '' ? 'Error consultando NubeFact: ' . $curlError : 'NubeFact devolvió una respuesta no válida (HTTP ' . $httpCode . ').');
        }
        if ($httpCode < 200 || $httpCode >= 300 || !empty($response['errors'])) {
            throw new RuntimeException($this->formatearError($response['errors'] ?? 'No se pudo consultar el comprobante en NubeFact.'));
        }

        $accepted = array_key_exists('aceptada_por_sunat', $response) ? (bool)$response['aceptada_por_sunat'] : null;
        $sunatDescription = trim((string)($response['sunat_description'] ?? ''));
        $sunatCode = trim((string)($response['sunat_responsecode'] ?? ''));
        $soapError = trim((string)($response['sunat_soap_error'] ?? ''));
        if ($accepted === true) {
            $message = $sunatDescription !== '' ? $sunatDescription : 'Aceptado por SUNAT.';
        } elseif ($sunatDescription !== '' || $soapError !== '' || ($sunatCode !== '' && $sunatCode !== '0')) {
            $message = $sunatDescription ?: ($soapError ?: 'SUNAT rechazó el comprobante.');
        } else {
            $message = 'Pendiente de respuesta de SUNAT.';
        }

        $normalized = array_intersect_key($response, array_flip([
            'tipo_de_comprobante', 'serie', 'numero', 'enlace', 'enlace_del_pdf', 'enlace_del_xml', 'enlace_del_cdr',
            'aceptada_por_sunat', 'sunat_description', 'sunat_note', 'sunat_responsecode', 'sunat_soap_error',
            'cadena_para_codigo_qr', 'codigo_hash', 'anulado',
        ]));
        $link = trim((string)($response['enlace'] ?? '')) ?: (string)$comprobante['enlace'];
        $pdf = trim((string)($response['enlace_del_pdf'] ?? '')) ?: (string)$comprobante['enlace_del_pdf'];
        $xml = trim((string)($response['enlace_del_xml'] ?? '')) ?: (string)$comprobante['enlace_del_xml'];
        $cdr = trim((string)($response['enlace_del_cdr'] ?? '')) ?: (string)$comprobante['enlace_del_cdr'];
        $update = $this->conn->prepare(
            "UPDATE nubefact_comprobante
             SET response_json = :response,
                 enlace = :enlace,
                 enlace_del_pdf = :pdf,
                 enlace_del_xml = :xml,
                 enlace_del_cdr = :cdr,
                 aceptada_por_sunat = :aceptada,
                 mensaje = :mensaje,
                 fecha_respuesta = :fecha_respuesta
             WHERE id = :id"
        );
        $update->bindValue(':response', json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $update->bindValue(':enlace', $link);
        $update->bindValue(':pdf', $pdf);
        $update->bindValue(':xml', $xml);
        $update->bindValue(':cdr', $cdr);
        $update->bindValue(':aceptada', $accepted === null ? null : (int)$accepted, $accepted === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $update->bindValue(':mensaje', $message);
        $update->bindValue(':fecha_respuesta', $this->nowLima);
        $update->bindValue(':id', (int)$comprobante['id'], PDO::PARAM_INT);
        $update->execute();

        return [
            'ok' => true,
            'sunat_estado' => $accepted === true ? 'ACEPTADO' : (($sunatDescription !== '' || $soapError !== '' || ($sunatCode !== '' && $sunatCode !== '0')) ? 'RECHAZADO' : 'PENDIENTE'),
            'mensaje' => $message,
            'serie' => (string)$comprobante['serie'],
            'numero' => (int)$comprobante['numero'],
        ];
    }

    private function configuracion(): array
    {
        $url = trim((string)($_ENV['NUBEFACT_URL'] ?? ''));
        $token = trim((string)($_ENV['NUBEFACT_TOKEN'] ?? ''));
        $environment = strtolower(trim((string)($_ENV['NUBEFACT_ENV'] ?? 'demo')));
        $serieBoleta = strtoupper(trim((string)($_ENV['NUBEFACT_SERIE_BOLETA'] ?? '')));
        $serieFactura = strtoupper(trim((string)($_ENV['NUBEFACT_SERIE_FACTURA'] ?? '')));
        $primerNumeroBoletaConfig = $_ENV['NUBEFACT_NUMERO_INICIAL_BOLETA'] ?? null;
        $primerNumeroFacturaConfig = $_ENV['NUBEFACT_NUMERO_INICIAL_FACTURA'] ?? null;

        if ($url === '' || $token === '' || stripos($url, 'REEMPLAZAR') !== false || stripos($token, 'REEMPLAZAR') !== false) {
            throw new RuntimeException('Configura NUBEFACT_URL y NUBEFACT_TOKEN en .env.');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw new RuntimeException('NUBEFACT_URL debe ser una URL HTTPS válida.');
        }

        $host = strtolower((string)parse_url($url, PHP_URL_HOST));
        $allowedHosts = $environment === 'demo'
            ? ['demo.nubefact.com', 'api.nubefact.com']
            : ['api.nubefact.com'];
        if (!in_array($environment, ['demo', 'production'], true) || !in_array($host, $allowedHosts, true)) {
            throw new RuntimeException('La URL de NubeFact no coincide con NUBEFACT_ENV. Verifica el entorno antes de emitir.');
        }
        if ($environment === 'production' && ($primerNumeroBoletaConfig === null || $primerNumeroFacturaConfig === null)) {
            throw new RuntimeException('Antes de producción configura en .env el próximo correlativo de ambas series.');
        }
        if (!preg_match('/^B[A-Z0-9]{3}$/', $serieBoleta) || !preg_match('/^F[A-Z0-9]{3}$/', $serieFactura)) {
            throw new RuntimeException('Configura series NubeFact válidas de cuatro caracteres para boleta y factura.');
        }
        $primerNumeroBoleta = filter_var($primerNumeroBoletaConfig ?? 1, FILTER_VALIDATE_INT);
        $primerNumeroFactura = filter_var($primerNumeroFacturaConfig ?? 1, FILTER_VALIDATE_INT);
        if ($primerNumeroBoleta === false || $primerNumeroBoleta < 1 || $primerNumeroBoleta > 99999999
            || $primerNumeroFactura === false || $primerNumeroFactura < 1 || $primerNumeroFactura > 99999999) {
            throw new RuntimeException('Los números iniciales de NubeFact deben ser enteros entre 1 y 99,999,999.');
        }

        return [
            'url' => $url,
            'token' => $token,
            'environment' => $environment,
            'serie_boleta' => $serieBoleta,
            'serie_factura' => $serieFactura,
            'primer_numero_boleta' => $primerNumeroBoleta,
            'primer_numero_factura' => $primerNumeroFactura,
        ];
    }

    private function obtenerTicket(string $hash): ?array
    {
        if (!preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            throw new InvalidArgumentException('Hash de ticket inválido.');
        }

        $sql = "SELECT p.id, p.cliente, p.fecha, p.dscto, p.tipo_dscto, p.pago,
                       c.documento AS cliente_documento,
                       c.nombres AS cliente_nombres,
                       c.apellidos AS cliente_apellidos,
                       c.email AS cliente_email,
                       c.id_sucursal AS cliente_sucursal,
                       u.IDSUCURSAL AS personal_sucursal
                FROM pedido p
                INNER JOIN personal u ON u.IDPERSONAL = p.usuario
                INNER JOIN cliente c ON c.id = p.cliente
                WHERE MD5(p.id) = :hash
                  AND u.IDSUCURSAL = @id_sucursal
                  AND c.id_sucursal = @id_sucursal
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ticket) {
            return null;
        }

        $details = $this->conn->prepare(
            "SELECT d.precio, d.cantidad, d.subtotal, s.nombre, c.tpo
             FROM detalle_pedido d
             INNER JOIN product_service s ON s.id = d.id_productservice AND s.id_sucursal = @id_sucursal
             INNER JOIN categoria c ON c.id = s.categoria AND c.id_sucursal = @id_sucursal
             WHERE d.id_pedido = :id_pedido
             ORDER BY s.id"
        );
        $details->bindValue(':id_pedido', (int)$ticket['id'], PDO::PARAM_INT);
        $details->execute();
        $ticket['items'] = $details->fetchAll(PDO::FETCH_ASSOC);
        return $ticket;
    }

    private function construirPayload(array $ticket, int $tipo, string $serie, array $datosCliente): array
    {
        $documento = preg_replace('/\D+/', '', (string)$ticket['cliente_documento']);
        $denominacion = trim((string)($datosCliente['denominacion'] ?? ''));
        if ($denominacion === '') {
            $denominacion = trim($ticket['cliente_nombres'] . ' ' . $ticket['cliente_apellidos']);
        }
        $direccion = trim((string)($datosCliente['direccion'] ?? ''));
        $email = trim((string)($datosCliente['email'] ?? $ticket['cliente_email'] ?? ''));

        if ($denominacion === '') {
            throw new RuntimeException('El cliente necesita una denominación para emitir el comprobante.');
        }
        if ($tipo === 1 && (!preg_match('/^\d{11}$/', $documento) || $direccion === '')) {
            throw new RuntimeException('Para emitir factura, el cliente debe tener RUC de 11 dígitos y dirección fiscal.');
        }

        $tipoDocumento = match (strlen($documento)) {
            8 => '1',
            11 => '6',
            default => '-',
        };
        if ($tipo === 2 && $tipoDocumento === '-' && array_sum(array_map(
            static fn(array $item): float => (float)$item['subtotal'],
            $ticket['items']
        )) >= 700) {
            throw new RuntimeException('La boleta requiere DNI/RUC para ventas iguales o superiores a S/ 700.');
        }
        if ($tipoDocumento === '-') {
            $documento = '-';
        }
        if ($tipo === 1) {
            $tipoDocumento = '6';
        }
        if (!$ticket['items']) {
            throw new RuntimeException('El ticket no tiene detalle para emitir.');
        }

        $items = [];
        $totalGravadaAntesDescuento = 0.0;
        $totalIgvAntesDescuento = 0.0;
        foreach ($ticket['items'] as $item) {
            $cantidad = (float)$item['cantidad'];
            $totalBruto = round((float)$item['subtotal'], 2);
            if ($cantidad <= 0 || $totalBruto < 0) {
                throw new RuntimeException('El ticket contiene un detalle con cantidad o importe inválido.');
            }

            $subtotal = round($totalBruto / 1.18, 2);
            $igv = round($totalBruto - $subtotal, 2);
            $totalGravadaAntesDescuento += $subtotal;
            $totalIgvAntesDescuento += $igv;
            $precioUnitario = round($totalBruto / $cantidad, 10);
            $valorUnitario = round($subtotal / $cantidad, 10);
            $items[] = [
                'unidad_de_medida' => strtoupper((string)$item['tpo']) === 'PRODUCTO' ? 'NIU' : 'ZZ',
                'codigo' => (string)$item['nombre'],
                'descripcion' => mb_substr(trim((string)$item['nombre']), 0, 250),
                'cantidad' => $cantidad,
                'valor_unitario' => $valorUnitario,
                'precio_unitario' => $precioUnitario,
                'descuento' => '',
                'subtotal' => $subtotal,
                'tipo_de_igv' => 1,
                'igv' => $igv,
                'total' => round($subtotal + $igv, 2),
                'anticipo_regularizacion' => false,
                'anticipo_documento_serie' => '',
                'anticipo_documento_numero' => '',
            ];
        }

        $descuentoTicket = round(max(0, (float)$ticket['dscto']), 2);
        $totalBruto = round(array_sum(array_column($ticket['items'], 'subtotal')), 2);
        if ($descuentoTicket > $totalBruto) {
            throw new RuntimeException('El descuento del ticket supera el importe de sus items.');
        }

        // Los precios del sistema incluyen IGV; el descuento global de NubeFact se expresa antes de IGV.
        $descuentoGlobal = round($descuentoTicket / 1.18, 2);
        $totalGravada = round($totalGravadaAntesDescuento - $descuentoGlobal, 2);
        $descuentoIgv = round($descuentoTicket - $descuentoGlobal, 2);
        $totalIgv = round($totalIgvAntesDescuento - $descuentoIgv, 2);
        $total = round($totalGravada + $totalIgv, 2);
        $totalEsperado = round($totalBruto - $descuentoTicket, 2);
        if (abs($total - $totalEsperado) > 0.02) {
            throw new RuntimeException('Los totales del ticket no cuadran con IGV 18%; revisa los importes antes de emitir.');
        }

        return [
            'operacion' => 'generar_comprobante',
            'tipo_de_comprobante' => $tipo,
            'serie' => $serie,
            'numero' => 0,
            'sunat_transaction' => 1,
            'cliente_tipo_de_documento' => $tipoDocumento,
            'cliente_numero_de_documento' => $documento,
            'cliente_denominacion' => mb_substr($denominacion, 0, 100),
            'cliente_direccion' => mb_substr($direccion, 0, 100),
            'cliente_email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '',
            'cliente_email_1' => '',
            'cliente_email_2' => '',
            'fecha_de_emision' => (new DateTimeImmutable('now', new DateTimeZone('America/Lima')))->format('d-m-Y'),
            'fecha_de_vencimiento' => '',
            'moneda' => 1,
            'tipo_de_cambio' => '',
            'porcentaje_de_igv' => '18.00',
            'descuento_global' => $descuentoGlobal > 0 ? $descuentoGlobal : '',
            'total_descuento' => $descuentoGlobal > 0 ? $descuentoGlobal : '',
            'total_anticipo' => '',
            'total_gravada' => $totalGravada,
            'total_inafecta' => '',
            'total_exonerada' => '',
            'total_igv' => $totalIgv,
            'total_gratuita' => '',
            'total_otros_cargos' => '',
            'total' => $total,
            'percepcion_tipo' => '',
            'percepcion_base_imponible' => '',
            'total_percepcion' => '',
            'total_incluido_percepcion' => '',
            'detraccion' => false,
            'observaciones' => 'Ticket Xintra #' . $ticket['id'],
            'documento_que_se_modifica_tipo' => '',
            'documento_que_se_modifica_serie' => '',
            'documento_que_se_modifica_numero' => '',
            'tipo_de_nota_de_credito' => '',
            'tipo_de_nota_de_debito' => '',
            'enviar_automaticamente_a_la_sunat' => true,
            'enviar_automaticamente_al_cliente' => false,
            'codigo_unico' => '',
            'condiciones_de_pago' => '',
            'medio_de_pago' => mb_substr((string)$ticket['pago'], 0, 250),
            'cancelado' => true,
            'placa_vehiculo' => '',
            'orden_compra_servicio' => '',
            'formato_de_pdf' => '',
            'items' => $items,
        ];
    }

    private function reservarNumero(int $tipo, string $serie, int $primerNumero): int
    {
        $insert = $this->conn->prepare(
            'INSERT IGNORE INTO nubefact_secuencia (tipo_de_comprobante, serie, ultimo_numero) VALUES (:tipo, :serie, :ultimo_numero)'
        );
        $insert->execute([':tipo' => $tipo, ':serie' => $serie, ':ultimo_numero' => $primerNumero - 1]);

        $select = $this->conn->prepare(
            'SELECT ultimo_numero FROM nubefact_secuencia WHERE tipo_de_comprobante = :tipo AND serie = :serie FOR UPDATE'
        );
        $select->execute([':tipo' => $tipo, ':serie' => $serie]);
        $next = (int)$select->fetchColumn() + 1;
        if ($next > 99999999) {
            throw new RuntimeException('Se agotó el correlativo configurado para la serie NubeFact.');
        }

        $update = $this->conn->prepare(
            'UPDATE nubefact_secuencia SET ultimo_numero = :numero WHERE tipo_de_comprobante = :tipo AND serie = :serie'
        );
        $update->execute([':numero' => $next, ':tipo' => $tipo, ':serie' => $serie]);
        return $next;
    }

    private function enviarNubeFact(int $comprobanteId, array $payload, array $config): array
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $ch = curl_init($config['url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => [
                'Authorization: Token token="' . $config['token'] . '"',
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $caBundle = dirname(__DIR__) . '/config/cacert.pem';
        if (is_file($caBundle)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }

        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($response)) {
            $message = $curlError !== '' ? 'Error de conexión con NubeFact: ' . $curlError : 'NubeFact devolvió una respuesta no válida (HTTP ' . $httpCode . ').';
            $this->guardarRespuesta($comprobanteId, 'ERROR', null, $message);
            throw new RuntimeException($message);
        }

        $errors = $response['errors'] ?? null;
        $pdf = trim((string)($response['enlace_del_pdf'] ?? ''));
        $link = trim((string)($response['enlace'] ?? ''));
        if ($pdf === '' && $link !== '') {
            $pdf = str_ends_with(strtolower($link), '.pdf') ? $link : rtrim($link, '/') . '.pdf';
        }
        $normalized = array_intersect_key($response, array_flip([
            'tipo_de_comprobante', 'serie', 'numero', 'enlace', 'enlace_del_pdf', 'enlace_del_xml', 'enlace_del_cdr',
            'aceptada_por_sunat', 'sunat_description', 'sunat_note', 'sunat_responsecode', 'sunat_soap_error',
            'cadena_para_codigo_qr', 'codigo_hash', 'errors',
        ]));
        $hasErrors = !($errors === null || $errors === '' || $errors === [] || $errors === false);
        if ($httpCode < 200 || $httpCode >= 300) {
            $hasErrors = true;
        }
        if (!$hasErrors && empty($response['serie']) && empty($response['enlace'])) {
            $hasErrors = true;
            $errors = 'NubeFact no confirmó la emisión del documento.';
            $normalized['errors'] = $errors;
        }

        $message = $hasErrors
            ? $this->formatearError($errors ?? $response['sunat_description'] ?? 'Error al emitir el comprobante.')
            : trim((string)($response['sunat_description'] ?? 'Comprobante emitido.'));
        $state = $hasErrors ? 'ERROR' : 'EMITIDO';
        $this->guardarRespuesta($comprobanteId, $state, $normalized, $message, $pdf);

        if ($hasErrors) {
            throw new RuntimeException($message);
        }

        return [
            'ok' => true,
            'estado' => $state,
            'mensaje' => $message,
            'tipo_de_comprobante' => (int)($response['tipo_de_comprobante'] ?? $payload['tipo_de_comprobante']),
            'serie' => (string)($response['serie'] ?? $payload['serie']),
            'numero' => (int)($response['numero'] ?? $payload['numero']),
            'enlace_pdf' => $pdf,
            'aceptada_por_sunat' => $response['aceptada_por_sunat'] ?? null,
        ];
    }

    private function guardarRespuesta(int $id, string $state, ?array $response, string $message, string $pdf = ''): void
    {
        $data = $response ?? [];
        $sql = "UPDATE nubefact_comprobante
                SET estado = :estado,
                    response_json = :response_json,
                    enlace = :enlace,
                    enlace_del_pdf = :pdf,
                    enlace_del_xml = :xml,
                    enlace_del_cdr = :cdr,
                    aceptada_por_sunat = :aceptada,
                    mensaje = :mensaje,
                    fecha_respuesta = :fecha_respuesta
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':estado', $state);
        $stmt->bindValue(':response_json', $response ? json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null);
        $stmt->bindValue(':enlace', (string)($data['enlace'] ?? ''));
        $stmt->bindValue(':pdf', $pdf !== '' ? $pdf : (string)($data['enlace_del_pdf'] ?? ''));
        $stmt->bindValue(':xml', (string)($data['enlace_del_xml'] ?? ''));
        $stmt->bindValue(':cdr', (string)($data['enlace_del_cdr'] ?? ''));
        $accepted = array_key_exists('aceptada_por_sunat', $data) ? (int)(bool)$data['aceptada_por_sunat'] : null;
        $stmt->bindValue(':aceptada', $accepted, $accepted === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':mensaje', $message);
        $stmt->bindValue(':fecha_respuesta', $this->nowLima);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function resultadoExistente(array $row): array
    {
        return [
            'ok' => true,
            'ya_emitido' => true,
            'estado' => $row['estado'],
            'mensaje' => 'Este ticket ya tiene comprobante electrónico.',
            'tipo_de_comprobante' => (int)$row['tipo_de_comprobante'],
            'serie' => $row['serie'],
            'numero' => (int)$row['numero'],
            'enlace_pdf' => $row['enlace_del_pdf'],
            'aceptada_por_sunat' => $row['aceptada_por_sunat'] === null ? null : (bool)$row['aceptada_por_sunat'],
        ];
    }

    private function formatearError($errors): string
    {
        if (is_array($errors)) {
            $scalarErrors = array_filter($errors, static fn($error): bool => is_scalar($error));
            if ($scalarErrors) {
                return implode(' ', array_map('strval', $scalarErrors));
            }
            return json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'Error de NubeFact.';
        }
        return trim((string)$errors) ?: 'No se pudo emitir el comprobante electrónico.';
    }
}
