<?php
require_once "../../database/conexion.php";

class Dashboard {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function obtenerContadores(): ?array {
        $sql = "SELECT 
                (SELECT COUNT(1)
                FROM personal p
                WHERE p.idestado = 1 
                AND p.idsucursal = 5) AS total_personal,

                (SELECT COUNT(1)
                FROM product_service a
                LEFT JOIN categoria b ON a.categoria = b.id
                WHERE a.estado = 1 
                AND a.id_sucursal = 5 
                AND b.tpo = 'PRODUCTO') AS total_productos,

                (SELECT COUNT(1)
                FROM product_service a
                LEFT JOIN categoria b ON a.categoria = b.id
                WHERE a.estado = 1 
                AND a.id_sucursal = 5 
                AND b.tpo = 'SERVICIO') AS total_servicios,

                (SELECT COUNT(1)
                FROM pedido ped
                WHERE EXISTS (
                SELECT 1 
                FROM personal per 
                WHERE per.idpersonal = ped.usuario
                AND per.idsucursal = 5
                )) AS total_pedidos
             ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerGrafSales(): ?array {
        $sql = "SELECT 
                IFNULL(SUM(b.subtotal), 0) AS total,
                SUM(CASE WHEN c.tpo = 'PRODUCTO' THEN b.subtotal END) AS producto,
                SUM(CASE WHEN c.tpo = 'SERVICIO' THEN b.subtotal END) AS servicio,
                DATE_FORMAT(a.fecha, '%Y-%m') AS mes
                FROM pedido a
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service d ON b.id_productservice = d.id
                LEFT JOIN categoria c ON Dd.categoria = c.id
                WHERE 
                d.id_sucursal = 5
                AND a.cliente > 0
                AND a.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(a.fecha, '%Y-%m')
                ORDER BY mes desc
             ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function obtenerGrafUser(): ?array {
        $sql = "SELECT 
                    c.usuario,
                    SUM(CASE WHEN YEAR(a.fecha) = YEAR(CURDATE()) 
                            AND MONTH(a.fecha) = MONTH(CURDATE()) THEN 1 ELSE 0 END) AS tickets_actuales,
                    SUM(CASE WHEN YEAR(a.fecha) = YEAR(CURDATE()) 
                            AND MONTH(a.fecha) = MONTH(CURDATE()) THEN b.subtotal ELSE 0 END) AS total_actual,
                    SUM(CASE WHEN YEAR(a.fecha) = YEAR(CURDATE()) 
                            AND MONTH(a.fecha) = MONTH(CURDATE()) THEN b.cantidad ELSE 0 END) AS items_actuales,

                    SUM(CASE WHEN YEAR(a.fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                            AND MONTH(a.fecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN 1 ELSE 0 END) AS tickets_anteriores,
                    SUM(CASE WHEN YEAR(a.fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                            AND MONTH(a.fecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN b.subtotal ELSE 0 END) AS total_anteriores,
                    SUM(CASE WHEN YEAR(a.fecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                            AND MONTH(a.fecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN b.cantidad ELSE 0 END) AS items_anteriores
                FROM pedido a
                LEFT JOIN detalle_pedido b ON a.id = b.id_pedido
                LEFT JOIN product_service d ON b.id_productservice = d.id
                LEFT JOIN personal c ON a.usuario = c.IDPERSONAL
                WHERE 
                    d.id_sucursal = 5
                    AND a.cliente > 0
                    AND a.fecha >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                GROUP BY c.usuario
                ORDER BY c.usuario ASC
             ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
}
?>