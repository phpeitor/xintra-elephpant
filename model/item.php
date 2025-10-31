<?php
require_once "../database/conexion.php";

class Item {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function baja(int $id): bool {
        $sql = "UPDATE product_service 
                SET estado = 0
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function actualizarPorHash(string $hash, array $data): bool {
        $sql = "UPDATE product_service 
                SET nombre = :nombre,
                    categoria = :categoria,
                    precio = :precio,
                    estado = :estado
                WHERE MD5(id) = :hash";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':categoria', $data['categoria']);
        $stmt->bindValue(':precio', $data['precio']);
        $stmt->bindValue(':estado', (int)$data['estado'], PDO::PARAM_INT);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function guardar(array $data): int {
        $sql = "INSERT INTO product_service 
                (nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
                VALUES 
                (:nombre, :categoria, :precio, 1, :stock, :fecha_creacion, 5, '')";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nombre',   $data['nombre'] ?? '');
        $stmt->bindValue(':categoria', $data['categoria'] ?? '');
        $stmt->bindValue(':precio',     $data['precio'] ?? '');
        $stmt->bindValue(':stock',  $data['stock'] ?? '0');
        $stmt->bindValue(':fecha_creacion', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function guardar_stock(array $data): int {
        $sql = "INSERT INTO stock_black 
                (id_product, id_pedido, tipo, stock, fecha, user)
                VALUES 
                (:id_product, :id_pedido, :tipo, :stock, :fecha, 'admin')";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':id_product',   $data['id_product'] ?? '');
        $stmt->bindValue(':id_pedido', $data['id_pedido'] ?? '');
        $stmt->bindValue(':tipo',     $data['tipo'] ?? '');
        $stmt->bindValue(':stock',  $data['stock'] ?? '0');
        $stmt->bindValue(':fecha', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }


    public function table_item(string $tpo = ''): array {
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
                    where b.id_sucursal=5";

        if ($tpo !== '') {
            $sql .= " AND c.tpo = :tpo";
        }
        $sql .= " ORDER BY b.id DESC";

        $stmt = $this->conn->prepare($sql);
        if ($tpo !== '') {
            $stmt->bindValue(':tpo', $tpo, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorHash(string $hash): ?array {
        $sql = "SELECT b.*, c.tpo as grupo,
                GREATEST(IFNULL(stock1, 0) - IFNULL(stock2, 0), 0) AS stock_final
                FROM product_service b
                left join categoria c on b.categoria=c.id
                    left join (
                                SELECT a.id_product,sum(stock) as stock1,b.stock2 as stock2 
                                FROM stock_black a 
                                left join (SELECT id_product as id_product2,sum(stock) as stock2 
                                            FROM stock_black where tipo='S' 
                                            group by id_product) 
                                b on a.id_product=b.id_product2 where tipo='E' 
                                group by id_product) t on t.id_product=b.id 
                WHERE MD5(b.id) = :hash
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerCategoria(string $grupo): ?array {
        $sql = "SELECT *
                FROM categoria
                WHERE estado=1 and id_sucursal=5
                and tpo=:grupo";
        $stmt = $this->conn->prepare($sql);
        if ($grupo !== '') {
            $stmt->bindValue(':grupo', $grupo, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>