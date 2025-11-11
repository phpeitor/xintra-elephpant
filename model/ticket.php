<?php
require_once "../../database/conexion.php";

class Ticket {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function actualizarPorHash(string $hash, array $data): bool {
        $sql = "UPDATE pedido 
                SET cliente = :cliente,
                    usuario = :usuario,
                    fecha = :fecha,
                    dscto = :dscto,
                    tipo_dscto = :tipo_dscto,
                    pago = :pago
                WHERE MD5(id) = :hash";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':cliente', $data['cliente']);
        $stmt->bindValue(':usuario', $data['usuario']);
        $stmt->bindValue(':fecha', $data['fecha']);
        $stmt->bindValue(':dscto', $data['dscto'] ?? 0);    
        $stmt->bindValue(':tipo_dscto', $data['tipo_dscto'] ?? 'NO APLICA');
        $stmt->bindValue(':pago', $data['pago']);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() >= 0;
    }

    public function eliminar_pedido(string $hash): bool {
        $sql = "DELETE from detalle_pedido 
                WHERE MD5(id_pedido) = :hash";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function eliminar_stock(string $hash): bool {
        $sql = "DELETE from stock_black 
                WHERE MD5(id_pedido) = :hash and tipo='S'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function beginTransaction(): void {
        $this->conn->beginTransaction();
    }

    public function commit(): void {
        $this->conn->commit();
    }

    public function rollback(): void {
        $this->conn->rollBack();
    }

    public function guardar(array $data): int {
        $sql = "INSERT INTO pedido 
                (cliente,usuario,user_registro,fecha,fecha_registro,dscto,tipo_dscto,pago)
                VALUES 
                (:cliente, :usuario, :user_registro, :fecha, :fecha_registro, :dscto, :tipo_dscto, :pago)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':cliente',        $data['cliente'] ?? '');
        $stmt->bindValue(':usuario',        $data['usuario'] ?? '');
        $stmt->bindValue(':user_registro',  $data['user_registro'] ?? '');
        $stmt->bindValue(':fecha',          $data['fecha'] ?? '');
        $stmt->bindValue(':fecha_registro', $this->nowLima);
        $stmt->bindValue(':dscto',          $data['dscto'] ?? '');
        $stmt->bindValue(':tipo_dscto',     $data['tipo_dscto'] ?? 'NO APLICA');
        $stmt->bindValue(':pago',           $data['pago'] ?? '');
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function guardar_detalle(array $data): int {
        $id_pedido         = $data['id_pedido'] ?? '';
        $id_productservice = $data['id_productservice'] ?? '';
        $precio            = (float)($data['precio'] ?? 0);
        $cantidad          = (float)($data['cantidad'] ?? 0);
        $subtotal          = (float)($data['subtotal'] ?? 0);

        $sql = "INSERT INTO detalle_pedido (
                    id_pedido,
                    id_productservice,
                    precio,
                    cantidad,
                    subtotal
                )
                VALUES (
                    (CASE 
                        WHEN LENGTH(:id_pedido) = 32 THEN (
                            SELECT id FROM pedido WHERE MD5(id) = :id_pedido LIMIT 1
                        )
                        ELSE :id_pedido
                    END),
                    (CASE 
                        WHEN LENGTH(:id_productservice) = 32 THEN (
                            SELECT id FROM product_service WHERE MD5(id) = :id_productservice LIMIT 1
                        )
                        ELSE :id_productservice
                    END),
                    :precio,
                    :cantidad,
                    :subtotal
                )";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_pedido', $id_pedido);
        $stmt->bindValue(':id_productservice', $id_productservice);
        $stmt->bindValue(':precio', $precio);
        $stmt->bindValue(':cantidad', $cantidad);
        $stmt->bindValue(':subtotal', $subtotal);
        $stmt->execute();

        return (int)$this->conn->lastInsertId();
    }

    public function guardar_stock(array $data): int {
        $id_product = $data['id_product'] ?? '';
        $id_pedido  = $data['id_pedido'] ?? '';
        $tipo       = $data['tipo'] ?? 'E';
        $stock      = (float)($data['stock'] ?? 0);
        $fecha      = $data['fecha'] ?? $this->nowLima;
        $user       = $data['user'] ?? 'admin';

        $sql = "INSERT INTO stock_black (
                    id_product,
                    id_pedido,
                    tipo,
                    stock,
                    fecha,
                    user
                )
                VALUES (
                    (CASE 
                        WHEN LENGTH(:id_product) = 32 THEN (
                            SELECT id FROM product_service WHERE MD5(id) = :id_product LIMIT 1
                        )
                        ELSE :id_product
                    END),
                    (CASE 
                        WHEN LENGTH(:id_pedido) = 32 THEN (
                            SELECT id FROM pedido WHERE MD5(id) = :id_pedido LIMIT 1
                        )
                        ELSE :id_pedido
                    END),
                    :tipo,
                    :stock,
                    :fecha,
                    :user
                )";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_product', $id_product);
        $stmt->bindValue(':id_pedido', $id_pedido);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':stock', $stock);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':user', $user);
        $stmt->execute();

        return (int)$this->conn->lastInsertId();
    }

    public function table_ticket(?string $fecha_inicio = null, ?string $fecha_fin = null): array {
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-7 days', strtotime($fecha_fin)));
        }

        $sql = "SELECT a.id, B.id_productservice as id_producto, date(a.fecha) as fecha_pedido, case when upper(c.USUARIO) is null then 'SALIDA INSUMOS' else upper(c.USUARIO) end as usuario,
                case when E.apellidos is null then 'INVENTARIO' else concat(E.nombres,' ',E.apellidos) end as cliente, 
                GROUP_CONCAT('• ',d.nombre SEPARATOR ' </br> ') as productos, 
                GROUP_CONCAT('S/.',B.precio,' x ',B.cantidad SEPARATOR ' </br> ') as precioxcant,
                case when dscto > 0 and pago='EFECTIVO' then concat('S/.',sum(B.subtotal),'<br/><code>',tipo_dscto,
                '</code>','<br/><i style=color:#f00>S/.',dscto,'</i><br/><b>S/.',(sum(B.subtotal)-dscto),'<b>') 
                when pago not in ('EFECTIVO','YAPE','PLIN') and dscto=0 then concat('S/.',sum(B.subtotal),'<br/><code>',pago,'</code>',
                '<br/><b>S/.',round((sum(B.subtotal)*1.05),2),'<b>')
                when dscto > 0 and pago not in ('EFECTIVO','YAPE','PLIN') then concat('S/.',sum(B.subtotal),'<br/><code>',
                tipo_dscto,'</code><br/><i style=color:#f00>S/.',dscto,'</i><br/><code>',pago,
                ' 5%</code><br/><b>S/.',round((sum(B.subtotal)*1.05)-dscto,2),'<b>') 
                when pago ='EFECTIVO' then concat('S/.',sum(B.subtotal),'<br/><i class=ri-currency-line></i>')
                else concat('S/.',sum(B.subtotal),'<br/><code>', pago,'</code>') end as total,
                sum(B.cantidad) as cantidad
                    FROM pedido a 
                    LEFT JOIN detalle_pedido b ON a.id = b.id_pedido 
                    LEFT JOIN personal c ON a.usuario = c.IDPERSONAL 
                    LEFT JOIN product_service d ON B.id_productservice = d.id 
                    LEFT JOIN cliente e on e.id = a.cliente 
                WHERE DATE(a.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                AND d.id_sucursal = 5 
                AND a.cliente > 0
                GROUP BY a.id 
                ORDER BY a.id DESC
            ";
        $this->conn->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excel(?string $fecha_inicio = null, ?string $fecha_fin = null): array {
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-7 days', strtotime($fecha_fin)));
        }

        $sql = "SELECT 
                a.id, B.id_productservice as id_producto, date(a.fecha) as fecha_pedido, 
                c.USUARIO as usuario,
                concat(c.NOMBRES,' ',c.APELLIDOS) as personal,
                c.DOC as dni_personal,
                concat(E.nombres,' ',E.apellidos) as cliente,
                x.tpo as tipo, d.nombre as producto, 
                B.precio, B.cantidad,
                case when  d.nombre like 'corte%' then 0.45
                when d.nombre like 'caballero%' then 0.45
                when d.nombre like 'DEPILACION.HILO%' then 0.45
                when d.nombre like 'DEPILACION%' then 0.45
                when d.nombre like 'DEPIHILO%' then 0.45
                when d.nombre like 'ALISADO%' then 0.25
                when d.nombre like 'PIGMENTO DE CEJAS%' then 0.25
                when  d.nombre like 'ONDAS%' then 0.4
                when  d.nombre like 'DAMa.30%' then 0.45
                when  x.tpo ='PRODUCTO' then 0.01
                when  replace(d.nombre,'ñ','N') like '%PESTANA%' then 0.25
                when  replace(d.nombre,'ñ','N') like '%UNAS%' then 0.4
                when  d.nombre like 'cepillado%' then 0.4
                when  d.nombre like '%planchado de cejas%' then 0.3 
                when  d.nombre like 'planchado%' then 0.4
                when  d.nombre like '%navaja%' then 0.45
                when  d.nombre like '%maquillaje%' then 0.25 
                when  c.USUARIO='MARYRG' and d.nombre like 'DEPILACION HILO%' then 0.45
                when  d.nombre like 'DEPILACION HILO%' then 0.4
                when  c.USUARIO='MARYRG' and d.nombre like '%DEPI.HILO%' then 0.45
                when  d.nombre like '%DEPI.HILO%' then 0.4
                when  d.nombre like 'tinte%' then 0.3
                when  d.nombre like 'aplicacion%' then 0.4
                when  d.nombre like 'aplicacion tinte%' then 0.4
                when  c.USUARIO='MARYRG' and d.nombre like '%cera%' then 0.35
                when  d.nombre like '%cera%' then 0.3
                when  d.nombre like '%COLOCACION%' then 0.3
                when  d.nombre like '%1POR1%' then 0.3
                when  d.nombre like '%RIZADO%' then 0.25
                when  d.nombre like 'manicur%' then 0.4
                when  d.nombre like 'pedicur%' then 0.4
                when  d.nombre like 'peinado%' then 0.4
                when  d.nombre like 'mecha%' then 0.3
                when  c.USUARIO in ('JCALDERON','DIANASG') and d.nombre like 'facial%' then 0.3
                when  d.nombre like 'facial%' then 0.25
                when  c.USUARIO in ('MARYRG','Junior.S') and d.nombre like 'botox%' then 0.2
                when  d.nombre like 'botox%' then 0.25
                when  d.nombre like 'pigmenta%' then 0.25
                when  d.nombre like '%MANO%' then 0.40
                else 1 end as dscto
                FROM pedido a 
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido 
                LEFT JOIN personal c ON a.usuario = c.IDPERSONAL 
                LEFT JOIN product_service d ON b.id_productservice = d.id 
                left join categoria x on x.id = d.categoria
                left join cliente e on e.id=a.cliente
                where date(a.fecha) between :fecha_inicio AND :fecha_fin
                and d.id_sucursal=5
                ORDER BY a.id DESC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorHash(string $hash): ?array {
        $sql = "SELECT a.id,cliente,a.usuario,user_registro,fecha,a.fecha_registro,dscto,tipo_dscto,pago,
                concat(E.nombres,' ',E.apellidos)  as cliente_nombre,
                upper(c.USUARIO) as user
                from pedido a
                LEFT JOIN personal C ON a.usuario = c.IDPERSONAL 
                LEFT JOIN cliente e on e.id=a.cliente 
                where md5(a.id) = :hash
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerDetallePorHash(string $hash): ?array {
        $sql = "SELECT id_pedido,id_productservice,a.precio,cantidad,subtotal, b.nombre as item, t.*,
                GREATEST(IFNULL(stock1, 0) - IFNULL(stock2, 0), 0) AS stock_final,
                c.nombre as categoria, c.tpo
                from detalle_pedido a
                left join product_service b on a.id_productservice = b.id
                left join categoria c on b.categoria=c.id
                left join (
                            SELECT a.id_product,sum(stock) as stock1,b.stock2 as stock2 
                            FROM stock_black a 
                            left join (SELECT id_product as id_product2,sum(stock) as stock2 
                                        FROM stock_black where tipo='S' 
                                        group by id_product) 
                            b on a.id_product=b.id_product2 where tipo='E' 
                            group by id_product) t on t.id_product=b.id 
                WHERE MD5(id_pedido) = :hash";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerTotalMensual(): ?array {
        $sql = "SELECT 
                    IFNULL(SUM(b.subtotal), 0) AS total,
                    DATE_FORMAT(a.fecha, '%Y-%m') AS mes
                FROM pedido A
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service D ON b.id_productservice = d.id
                WHERE 
                    d.id_sucursal = 5
                    AND a.cliente > 0
                    AND a.fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(a.fecha, '%Y-%m')
                ORDER BY mes desc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerTotalDiario(): ?array {
        $sql = "SELECT 
                    IFNULL(SUM(b.subtotal), 0) AS total,
                    DATE_FORMAT(a.fecha, '%Y-%m-%d') AS dia
                FROM pedido a
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service d ON b.id_productservice = d.id
                WHERE 
                    d.id_sucursal = 5
                    AND a.cliente > 0
                GROUP BY DATE_FORMAT(a.fecha, '%Y-%m-%d')
                ORDER BY dia DESC
                LIMIT 7";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerTotalCliente(): ?array {
        $sql = "SELECT 
                    count(distinct a.cliente) as cliente,
                    count(distinct a.usuario) as usuario,
                    DATE_FORMAT(a.fecha, '%Y-%m') AS mes
                FROM pedido a
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service d ON b.id_productservice = d.id
                WHERE 
                    d.id_sucursal = 5
                    AND a.cliente > 0
                GROUP BY DATE_FORMAT(a.fecha, '%Y-%m')
                ORDER BY mes DESC
                LIMIT 7";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerTotalUsuario(): ?array {
        $sql = "SELECT 
                count( B.id_productservice) as tickets,
                sum(subtotal) as total,
                sum(cantidad) as items,
                c.usuario as usuario
                FROM pedido a
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service d ON b.id_productservice = d.id
                LEFT JOIN personal c ON a.usuario = c.IDPERSONAL 
                WHERE 
                d.id_sucursal = 5
                AND a.cliente > 0
                and a.fecha>= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                GROUP BY c.usuario
                ORDER BY c.usuario ASC
                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerTotalItem(): ?array {
        $sql = "SELECT 
                sum(subtotal) as total,
                d.nombre as item
                FROM pedido a
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service d ON b.id_productservice = d.id
                WHERE 
                d.id_sucursal = 5
                AND a.cliente > 0
                and a.fecha>= DATE_SUB(CURDATE(), INTERVAL 15 DAY)
                GROUP BY d.nombre
                ORDER BY d.nombre ASC
                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerTotaPedido(): ?array {
        $sql = "SELECT 
                (
                    SELECT COUNT(1)
                    FROM pedido ped
                    WHERE EXISTS (
                    SELECT 1 
                    FROM personal per 
                    WHERE per.idpersonal = ped.usuario
                    AND per.idsucursal = 5
                    )
                ) AS total_pedido,
                (
                    SELECT cuota 
                    FROM sucursal_cuota 
                    WHERE id_sucursal = 5
                    LIMIT 1
                ) AS cuota
                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerItem(string $categoria): ?array {
        $sql = "SELECT t.id_product,
                    GREATEST(IFNULL(stock1, 0) - IFNULL(stock2, 0), 0) AS stock_final,
                    c.tpo, b.*,
                    c.nombre as nom_categoria,
                    b.precio
                    from 
                    product_service b 
                    left join categoria c on b.categoria=c.id
                    left join (
                                SELECT a.id_product,sum(stock) as stock1,b.stock2 as stock2 
                                FROM stock_black a 
                                left join (SELECT id_product as id_product2,sum(stock) as stock2 
                                            FROM stock_black where tipo='S' 
                                            group by id_product) 
                                b on a.id_product=b.id_product2 where tipo='E' 
                                group by id_product) t on t.id_product=b.id 
                WHERE b.estado=1 
                and b.categoria=:categoria";
        $stmt = $this->conn->prepare($sql);
        if ($categoria !== '') {
            $stmt->bindValue(':categoria', $categoria, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>