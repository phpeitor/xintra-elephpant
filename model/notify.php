<?php
require_once __DIR__ . '/../database/conexion.php';

class Notify {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function obtenerNotificacion(): ?array{
        $limit = rand(2, 5);
        $fechaHoy = substr($this->nowLima, 0, 10); 

        $sql = "SELECT 
                'login' AS origen,
                b.usuario,
                b.sexo,
                a.fecha AS fecha
                FROM login a
                LEFT JOIN personal b ON a.id_user = b.idpersonal
                WHERE DATE(a.fecha) = :fechaHoy
                AND a.tipo = 'IN'

                UNION ALL

                SELECT distinct
                'pedido' AS origen,
                b.usuario,
                b.sexo,
                CAST(CONCAT(a.fecha, ' 00:00:00') AS DATETIME) AS fecha
                FROM pedido a
                LEFT JOIN personal b ON a.usuario = b.idpersonal

                ORDER BY fecha DESC
                LIMIT :limit
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':fechaHoy', $fechaHoy);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

}
?>