<?php
require_once __DIR__ . '/../database/conexion.php';

class Compra
{
    private PDO $conn;
    private string $nowLima;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
        $this->nowLima = (new DateTimeImmutable('now', new DateTimeZone('America/Lima')))->format('Y-m-d H:i:s');
    }

    public function catalogos(): array
    {
        $suppliers = $this->conn->query(
            "SELECT id, tipo_documento, documento, razon_social
             FROM proveedor
             WHERE id_sucursal = @id_sucursal AND estado = 1
             ORDER BY razon_social"
        )->fetchAll(PDO::FETCH_ASSOC);
        $products = $this->conn->query(
            "SELECT p.id, p.nombre
             FROM product_service p
             INNER JOIN categoria c ON c.id = p.categoria AND c.id_sucursal = p.id_sucursal
             WHERE p.id_sucursal = @id_sucursal
               AND p.estado = 1
               AND UPPER(c.tpo) = 'PRODUCTO'
             ORDER BY p.nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
        return ['proveedores' => $suppliers, 'productos' => $products];
    }

    public function listar(): array
    {
        $stmt = $this->conn->query(
            'SELECT MD5(c.id) AS hash, c.tipo_comprobante, c.serie, c.numero, c.fecha, c.moneda,
                    c.subtotal, c.igv, c.total, c.estado, c.fecha_registro, c.fecha_recepcion,
                    p.razon_social AS proveedor, p.tipo_documento, p.documento
             FROM compra c
             INNER JOIN proveedor p ON p.id = c.id_proveedor AND p.id_sucursal = c.id_sucursal
             WHERE c.id_sucursal = @id_sucursal
             ORDER BY c.id DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener(int $id): ?array
    {
        $header = $this->conn->prepare(
            'SELECT c.id, c.id_proveedor, c.tipo_comprobante, c.serie, c.numero, c.fecha,
                    c.moneda, c.subtotal, c.igv, c.total, c.estado, c.observaciones,
                    c.recibido_por, c.fecha_recepcion, c.fecha_registro,
                    p.razon_social AS proveedor, p.tipo_documento, p.documento,
                    TRIM(CONCAT_WS(\' \', r.NOMBRES, r.APELLIDOS)) AS recibido_por_nombre
             FROM compra c
             INNER JOIN proveedor p ON p.id = c.id_proveedor AND p.id_sucursal = c.id_sucursal
             LEFT JOIN personal r ON r.IDPERSONAL = c.recibido_por AND r.IDSUCURSAL = c.id_sucursal
             WHERE c.id = :id AND c.id_sucursal = @id_sucursal
             LIMIT 1'
        );
        $header->execute([':id' => $id]);
        $purchase = $header->fetch(PDO::FETCH_ASSOC);
        if (!$purchase) {
            return null;
        }
        $details = $this->conn->prepare(
            'SELECT descripcion, cantidad, costo_unitario, porcentaje_igv, subtotal, igv, total
             FROM compra_detalle
             WHERE id_compra = :id AND id_sucursal = @id_sucursal
             ORDER BY id'
        );
        $details->execute([':id' => $id]);
        $purchase['items'] = $details->fetchAll(PDO::FETCH_ASSOC);
        return $purchase;
    }

    public function obtenerPorHash(string $hash): ?array
    {
        if (!preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            return null;
        }
        $stmt = $this->conn->prepare(
            'SELECT id FROM compra WHERE MD5(id) = :hash AND id_sucursal = @id_sucursal LIMIT 1'
        );
        $stmt->execute([':hash' => $hash]);
        $id = $stmt->fetchColumn();
        return $id === false ? null : $this->obtener((int)$id);
    }

    public function recibirPorHash(string $hash, int $idUsuario, string $nombreUsuario): array
    {
        if (!preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            throw new InvalidArgumentException('Identificador de compra inválido.');
        }
        $this->conn->beginTransaction();
        try {
            $purchaseStmt = $this->conn->prepare(
                'SELECT id, estado FROM compra
                 WHERE MD5(id) = :hash AND id_sucursal = @id_sucursal
                 LIMIT 1 FOR UPDATE'
            );
            $purchaseStmt->execute([':hash' => $hash]);
            $purchase = $purchaseStmt->fetch(PDO::FETCH_ASSOC);
            if (!$purchase) {
                throw new RuntimeException('Compra no encontrada en la sucursal actual.');
            }
            $idCompra = (int)$purchase['id'];
            $estado = (string)$purchase['estado'];
            if ($estado === 'RECIBIDA') {
                $this->conn->commit();
                return ['ya_recibida' => true];
            }
            if ($estado !== 'PENDIENTE') {
                throw new InvalidArgumentException('Solo se pueden recibir compras pendientes.');
            }

            $userStmt = $this->conn->prepare('SELECT 1 FROM personal WHERE IDPERSONAL = :id AND IDSUCURSAL = @id_sucursal LIMIT 1');
            $userStmt->execute([':id' => $idUsuario]);
            if (!$userStmt->fetchColumn()) {
                throw new RuntimeException('El usuario no pertenece a la sucursal actual.');
            }

            $detailsStmt = $this->conn->prepare(
                "SELECT d.id_productservice, d.cantidad
                 FROM compra_detalle d
                 INNER JOIN product_service p
                    ON p.id = d.id_productservice
                   AND p.id_sucursal = d.id_sucursal
                   AND p.estado = 1
                 INNER JOIN categoria cat
                    ON cat.id = p.categoria
                   AND cat.id_sucursal = p.id_sucursal
                   AND UPPER(cat.tpo) = 'PRODUCTO'
                 WHERE d.id_compra = :id_compra
                   AND d.id_sucursal = @id_sucursal
                 ORDER BY d.id
                 FOR UPDATE"
            );
            $detailsStmt->execute([':id_compra' => $idCompra]);
            $lines = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);
            $countStmt = $this->conn->prepare('SELECT COUNT(1) FROM compra_detalle WHERE id_compra = :id_compra AND id_sucursal = @id_sucursal');
            $countStmt->execute([':id_compra' => $idCompra]);
            $detailsCount = (int)$countStmt->fetchColumn();
            if ($detailsCount < 1 || count($lines) !== $detailsCount) {
                throw new RuntimeException('No se pueden recibir todos los productos: alguno ya no está activo como producto de inventario. Reactívalo y vuelve a intentar.');
            }

            $stockExists = $this->conn->prepare('SELECT id FROM stock_black WHERE id_compra = :id_compra LIMIT 1 FOR UPDATE');
            $stockExists->execute([':id_compra' => $idCompra]);
            if ($stockExists->fetchColumn()) {
                throw new RuntimeException('Ya existen movimientos vinculados a esta compra; no se duplicó la recepción.');
            }

            $insertStock = $this->conn->prepare(
                "INSERT INTO stock_black (id_product, id_pedido, id_compra, tipo, stock, fecha, user)
                 VALUES (:producto, 0, :compra, 'E', :cantidad, :fecha, :usuario)"
            );
            foreach ($lines as $line) {
                $insertStock->execute([
                    ':producto' => (int)$line['id_productservice'],
                    ':compra' => $idCompra,
                    ':cantidad' => (int)$line['cantidad'],
                    ':fecha' => $this->nowLima,
                    ':usuario' => mb_substr($nombreUsuario, 0, 100),
                ]);
            }

            $update = $this->conn->prepare(
                "UPDATE compra
                 SET estado = 'RECIBIDA', recibido_por = :usuario, fecha_recepcion = :fecha
                 WHERE id = :id AND id_sucursal = @id_sucursal AND estado = 'PENDIENTE'"
            );
            $update->execute([':usuario' => $idUsuario, ':fecha' => $this->nowLima, ':id' => $idCompra]);
            if ($update->rowCount() !== 1) {
                throw new RuntimeException('No se pudo completar el cambio de estado de la compra.');
            }

            $this->conn->commit();
            return ['ya_recibida' => false, 'productos_recibidos' => count($lines)];
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function guardar(array $input, int $idUsuario): int
    {
        $data = $this->normalizarCabecera($input);
        $items = $this->normalizarItems($input['items'] ?? null);
        $this->validarProveedor($data['proveedor_id']);
        $this->validarUsuario($idUsuario);
        $products = $this->validarProductos(array_column($items, 'producto_id'));

        $lines = [];
        $subtotalCompra = 0.0;
        $igvCompra = 0.0;
        foreach ($items as $item) {
            $product = $products[$item['producto_id']] ?? null;
            if (!$product) {
                throw new InvalidArgumentException('Selecciona productos vigentes de esta sucursal en todas las líneas.');
            }
            $subtotal = round($item['cantidad'] * $item['costo_unitario'], 2);
            $igv = round($subtotal * $item['porcentaje_igv'] / 100, 2);
            $total = round($subtotal + $igv, 2);
            $lines[] = [
                'producto_id' => $item['producto_id'],
                'descripcion' => mb_substr(trim((string)$product['nombre']), 0, 250),
                'cantidad' => $item['cantidad'],
                'costo_unitario' => $item['costo_unitario'],
                'porcentaje_igv' => $item['porcentaje_igv'],
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
            ];
            $subtotalCompra += $subtotal;
            $igvCompra += $igv;
        }
        $subtotalCompra = round($subtotalCompra, 2);
        $igvCompra = round($igvCompra, 2);
        $totalCompra = round($subtotalCompra + $igvCompra, 2);

        $this->conn->beginTransaction();
        try {
            $insert = $this->conn->prepare(
                "INSERT INTO compra
                    (id_sucursal, id_proveedor, id_usuario, tipo_comprobante, serie, numero, fecha, moneda,
                     subtotal, igv, total, estado, observaciones, fecha_registro)
                 VALUES
                    (@id_sucursal, :proveedor, :usuario, :tipo, :serie, :numero, :fecha, 'PEN',
                     :subtotal, :igv, :total, 'PENDIENTE', :observaciones, :fecha_registro)"
            );
            $insert->execute([
                ':proveedor' => $data['proveedor_id'],
                ':usuario' => $idUsuario,
                ':tipo' => $data['tipo_comprobante'],
                ':serie' => $data['serie'],
                ':numero' => $data['numero'],
                ':fecha' => $data['fecha'],
                ':subtotal' => $subtotalCompra,
                ':igv' => $igvCompra,
                ':total' => $totalCompra,
                ':observaciones' => $data['observaciones'] !== '' ? $data['observaciones'] : null,
                ':fecha_registro' => $this->nowLima,
            ]);
            $idCompra = (int)$this->conn->lastInsertId();

            $insertLine = $this->conn->prepare(
                'INSERT INTO compra_detalle
                    (id_compra, id_sucursal, id_productservice, descripcion, cantidad, costo_unitario,
                     porcentaje_igv, subtotal, igv, total)
                 VALUES
                    (:compra, @id_sucursal, :producto, :descripcion, :cantidad, :costo,
                     :porcentaje_igv, :subtotal, :igv, :total)'
            );
            foreach ($lines as $line) {
                $insertLine->execute([
                    ':compra' => $idCompra,
                    ':producto' => $line['producto_id'],
                    ':descripcion' => $line['descripcion'],
                    ':cantidad' => $line['cantidad'],
                    ':costo' => $line['costo_unitario'],
                    ':porcentaje_igv' => $line['porcentaje_igv'],
                    ':subtotal' => $line['subtotal'],
                    ':igv' => $line['igv'],
                    ':total' => $line['total'],
                ]);
            }
            $this->conn->commit();
            return $idCompra;
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    private function normalizarCabecera(array $input): array
    {
        $supplier = filter_var($input['proveedor_id'] ?? null, FILTER_VALIDATE_INT);
        $tipo = strtoupper(trim((string)($input['tipo_comprobante'] ?? '')));
        $serie = strtoupper(trim((string)($input['serie'] ?? '')));
        $numero = trim((string)($input['numero'] ?? ''));
        $fecha = trim((string)($input['fecha'] ?? ''));
        $observaciones = trim((string)($input['observaciones'] ?? ''));
        if ($supplier === false || $supplier < 1) {
            throw new InvalidArgumentException('Selecciona un proveedor válido.');
        }
        if (!in_array($tipo, ['FACTURA', 'BOLETA', 'TICKET', 'OTRO'], true)) {
            throw new InvalidArgumentException('Selecciona un tipo de comprobante válido.');
        }
        if ($serie === '' || mb_strlen($serie) > 20 || $numero === '' || mb_strlen($numero) > 20) {
            throw new InvalidArgumentException('Ingresa una serie y un número de comprobante válidos.');
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
        $dateErrors = DateTimeImmutable::getLastErrors();
        if (!$date || ($dateErrors && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $date->format('Y-m-d') !== $fecha) {
            throw new InvalidArgumentException('La fecha de compra no es válida.');
        }
        if (mb_strlen($observaciones) > 500) {
            throw new InvalidArgumentException('Las observaciones no deben superar 500 caracteres.');
        }
        return [
            'proveedor_id' => (int)$supplier,
            'tipo_comprobante' => $tipo,
            'serie' => $serie,
            'numero' => $numero,
            'fecha' => $fecha,
            'observaciones' => $observaciones,
        ];
    }

    private function normalizarItems($raw): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (!is_array($raw) || count($raw) < 1 || count($raw) > 100) {
            throw new InvalidArgumentException('Agrega de 1 a 100 productos a la compra.');
        }
        $items = [];
        $productosAgregados = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Una línea de compra no es válida.');
            }
            $productId = filter_var($item['producto_id'] ?? null, FILTER_VALIDATE_INT);
            $quantityRaw = $item['cantidad'] ?? null;
            $costRaw = $item['costo_unitario'] ?? null;
            $rateRaw = $item['porcentaje_igv'] ?? null;
            if ($productId === false || $productId < 1 || !is_numeric($quantityRaw) || !is_numeric($costRaw)) {
                throw new InvalidArgumentException('Completa el producto, la cantidad y el costo de cada línea.');
            }
            $quantity = (float)$quantityRaw;
            $cost = (float)$costRaw;
            $rate = filter_var($rateRaw, FILTER_VALIDATE_FLOAT);
            if (isset($productosAgregados[(int)$productId])) {
                throw new InvalidArgumentException('No puedes agregar el mismo producto más de una vez.');
            }
            if (!is_finite($quantity) || $quantity <= 0 || $quantity > 999999 || floor($quantity) !== $quantity
                || !is_finite($cost) || $cost < 0 || $cost > 99999999
                || !in_array($rate, [0.0, 18.0], true)) {
                throw new InvalidArgumentException('La cantidad debe ser un entero positivo; revisa también el costo y el IGV.');
            }
            $productosAgregados[(int)$productId] = true;
            $items[] = [
                'producto_id' => (int)$productId,
                'cantidad' => round($quantity, 3),
                'costo_unitario' => round($cost, 4),
                'porcentaje_igv' => $rate,
            ];
        }
        return $items;
    }

    private function validarProveedor(int $idProveedor): void
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM proveedor WHERE id = :id AND id_sucursal = @id_sucursal AND estado = 1 LIMIT 1');
        $stmt->execute([':id' => $idProveedor]);
        if (!$stmt->fetchColumn()) {
            throw new InvalidArgumentException('El proveedor no está activo en la sucursal actual.');
        }
    }

    private function validarUsuario(int $idUsuario): void
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM personal WHERE IDPERSONAL = :id AND IDSUCURSAL = @id_sucursal LIMIT 1');
        $stmt->execute([':id' => $idUsuario]);
        if (!$stmt->fetchColumn()) {
            throw new RuntimeException('El usuario no pertenece a la sucursal actual.');
        }
    }

    private function validarProductos(array $ids): array
    {
        $ids = array_values(array_unique($ids));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->conn->prepare(
            "SELECT p.id, p.nombre
             FROM product_service p
             INNER JOIN categoria c ON c.id = p.categoria AND c.id_sucursal = p.id_sucursal
             WHERE p.id_sucursal = @id_sucursal AND p.estado = 1
               AND UPPER(c.tpo) = 'PRODUCTO'
               AND p.id IN ($placeholders)"
        );
        $stmt->execute($ids);
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $product) {
            $result[(int)$product['id']] = $product;
        }
        return $result;
    }
}
