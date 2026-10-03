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
                WHERE id = :id AND id_sucursal = @id_sucursal";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function actualizarPorHash(string $hash, array $data): bool {
        $this->validarCategoria((string)($data['categoria'] ?? ''));
        $sql = "UPDATE product_service 
                SET nombre = :nombre,
                    categoria = :categoria,
                    precio = :precio,
                    estado = :estado
                WHERE MD5(id) = :hash AND id_sucursal = @id_sucursal";
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
        $this->validarCategoria((string)($data['categoria'] ?? ''));
        $sql = "INSERT INTO product_service 
                (nombre, categoria, precio, estado, stock, fecha_creacion, id_sucursal, medida)
                VALUES 
                (:nombre, :categoria, :precio, 1, :stock, :fecha_creacion, @id_sucursal, '')";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nombre',   $data['nombre'] ?? '');
        $stmt->bindValue(':categoria', $data['categoria'] ?? '');
        $stmt->bindValue(':precio',     $data['precio'] ?? '');
        $stmt->bindValue(':stock',  $data['stock'] ?? '0');
        $stmt->bindValue(':fecha_creacion', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    private function validarCategoria(string $categoria): void {
        $numericId = ctype_digit($categoria) ? (int)$categoria : null;
        $stmt = $this->conn->prepare('SELECT 1 FROM categoria WHERE (id = :id OR MD5(id) = :hash) AND id_sucursal = @id_sucursal LIMIT 1');
        $stmt->bindValue(':id', $numericId, $numericId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':hash', $numericId === null ? $categoria : '');
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            throw new RuntimeException('La categoría no pertenece a la sucursal actual.');
        }
    }

    public function guardar_stock(array $data): int {
        $id_product = $data['id_product'] ?? '';
        $numericId = ctype_digit((string)$id_product) ? (int)$id_product : null;
        $productCheck = $this->conn->prepare('SELECT 1 FROM product_service WHERE (id = :id OR MD5(id) = :hash) AND id_sucursal = @id_sucursal LIMIT 1');
        $productCheck->bindValue(':id', $numericId, $numericId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $productCheck->bindValue(':hash', $numericId === null ? (string)$id_product : '');
        $productCheck->execute();
        if (!$productCheck->fetchColumn()) {
            throw new RuntimeException('El ítem no pertenece a la sucursal actual.');
        }
        $id_pedido  = (int)($data['id_pedido'] ?? 0);
        $tipo       = $data['tipo'] ?? 'E';
        $stock      = (float)($data['stock'] ?? 0);
        $fecha      = $data['fecha'] ?? $this->nowLima;
        $user       = $data['user'] ?? 'admin';

        $sql = "INSERT INTO stock_black (id_product, id_pedido, tipo, stock, fecha, user)
                VALUES (
                    (CASE 
                        WHEN LENGTH(:id_product) = 32 THEN (
                            SELECT id FROM product_service WHERE MD5(id) = :id_product AND id_sucursal = @id_sucursal LIMIT 1
                        )
                        ELSE :id_product
                    END),
                    :id_pedido, :tipo, :stock, :fecha, :user
                )";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_product', $id_product);
        $stmt->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':stock', $stock);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':user', $user);
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
                     where b.id_sucursal=@id_sucursal";

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
                GREATEST(IFNULL(stock1, 0) - IFNULL(stock2, 0), 0) AS stock_final,
                c.nombre as nom_categoria,
                IFNULL(stock1, 0) as stock1,
                IFNULL(stock2, 0) as stock2
                FROM product_service b
                left join categoria c on b.categoria=c.id
                left join (
                            SELECT a.id_product,sum(stock) as stock1,b.stock2 as stock2 
                            FROM stock_black a 
                            left join (SELECT id_product as id_product2,sum(stock) as stock2 
                                        FROM stock_black where tipo='S' 
                                        group by id_product) 
                            b on a.id_product=b.id_product2 where tipo='E' 
                            group by id_product
                        ) t on t.id_product=b.id 
                WHERE MD5(b.id) = :hash AND b.id_sucursal = @id_sucursal
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function GraficoPorHash(string $hash): ?array {
        $sql = "SELECT *,
                case when tipo='E' then 'Almacen' else 'Venta' end as Tipo, 
                date(fecha) as Fecha, 
                date_format(fecha, '%b-%y') as Date,stock as Total 
                FROM stock_black 
                WHERE EXISTS (SELECT 1 FROM product_service p WHERE p.id = stock_black.id_product AND MD5(p.id) = :hash AND p.id_sucursal = @id_sucursal)
                order by fecha desc";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerCategoria(string $grupo): ?array {
        $sql = "SELECT *
                FROM categoria
                WHERE estado=1 and id_sucursal=@id_sucursal
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
