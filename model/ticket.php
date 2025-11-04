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
        $sql = "UPDATE personal 
                SET APELLIDOS = :APELLIDOS,
                    NOMBRES = :NOMBRES,
                    EMAIL = :EMAIL,
                    DOC = :DOC,
                    TLF = :TLF,
                    SEXO = :SEXO,
                    IDESTADO = :IDESTADO
                WHERE MD5(IDPERSONAL) = :hash";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':APELLIDOS', $data['apellidos']);
        $stmt->bindValue(':NOMBRES', $data['nombres']);
        $stmt->bindValue(':EMAIL', $data['email']);
        $stmt->bindValue(':DOC', $data['documento']);
        $stmt->bindValue(':TLF', $data['telefono']);
        $stmt->bindValue(':SEXO', (int)$data['sexo'], PDO::PARAM_INT);
        $stmt->bindValue(':IDESTADO', (int)$data['estado'], PDO::PARAM_INT);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function guardar(array $data): int {
        $sql = "INSERT INTO personal 
                (APELLIDOS, NOMBRES, EMAIL, DOC, TLF, SEXO, fecha_registro, IDSUCURSAL, IDESTADO, CARGO, fecha_baja, id_cartera)
                VALUES 
                (:nombres, :apellidos, :email, :documento, :telefono, :sexo, :fecha_registro, 5, 1, 5, '1900-01-01 00:00:00', 5)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nombres',   $data['nombres'] ?? '');
        $stmt->bindValue(':apellidos', $data['apellidos'] ?? '');
        $stmt->bindValue(':email',     $data['email'] ?? '');
        $stmt->bindValue(':documento', $data['documento'] ?? '');
        $stmt->bindValue(':telefono',  $data['telefono'] ?? '');
        $stmt->bindValue(':sexo', (int)($data['sexo'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':fecha_registro', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function table_ticket(?string $fecha_inicio = null, ?string $fecha_fin = null): array {
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-7 days', strtotime($fecha_fin)));
        }

        $sql = "
            SELECT A.id, B.id_productservice as id_producto, date(A.fecha) as fecha_pedido, case when upper(C.USUARIO) is null then 'SALIDA INSUMOS' else upper(C.USUARIO) end as usuario,case when E.apellidos is null then 'INVENTARIO' else concat(E.nombres,' ',E.apellidos) end as cliente, GROUP_CONCAT('• ',D.nombre SEPARATOR ' </br> ') as productos, GROUP_CONCAT('S/.',B.precio,' x ',B.cantidad SEPARATOR ' </br> ') as precioxcant,
			    case when dscto > 0 and pago='EFECTIVO' then concat('S/.',sum(B.subtotal),'<br/><code>',tipo_dscto,'</code>','<br/><i style=color:#f00>S/.',dscto,'</i><br/><b>S/.',(sum(B.subtotal)-dscto),'<b>') 
			     when pago != 'EFECTIVO' and dscto=0 then concat('S/.',sum(B.subtotal),'<br/><code>',pago,'</code>','<br/><b>S/.',round((sum(B.subtotal)*1.05),2),'<b>')
			     when dscto > 0 and pago != 'EFECTIVO' then concat('S/.',sum(B.subtotal),'<br/><code>',tipo_dscto,'</code><br/><i style=color:#f00>S/.',dscto,'</i><br/><code>',pago,' 5%</code><br/><b>S/.',round((sum(B.subtotal)*1.05)-dscto,2),'<b>') 
			    else concat('S/.',sum(B.subtotal)) end as total,sum(B.cantidad) as cantidad
			    FROM pedido A 
			    LEFT JOIN detalle_pedido B ON A.id = B.id_pedido 
			    LEFT JOIN personal C ON A.usuario = C.IDPERSONAL 
			    LEFT JOIN product_service D ON B.id_productservice = D.id 
			    LEFT JOIN cliente E on E.id=A.cliente 
            WHERE DATE(A.fecha) BETWEEN :fecha_inicio AND :fecha_fin
            AND D.id_sucursal = 5 
            AND A.cliente > 0
            GROUP BY A.id 
            ORDER BY A.id DESC
        ";
        $this->conn->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorHash(string $hash): ?array {
        $sql = "SELECT 
                    IDPERSONAL,
                    NOMBRES,
                    APELLIDOS,
                    DOC,
                    EMAIL,
                    TLF,
                    SEXO,
                    IDESTADO
                FROM personal
                WHERE MD5(IDPERSONAL) = :hash
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerItem(string $categoria): ?array {
        $sql = "select t.id_product,
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