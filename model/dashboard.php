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
                LEFT JOIN categoria c ON d.categoria = c.id
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
                $sql = "
                        WITH ventas AS (
                            SELECT
                                c.usuario,
                                DATE_FORMAT(a.fecha, '%Y-%m') AS mes,
                                COUNT(b.id_productservice) AS tickets,
                                COALESCE(SUM(b.subtotal), 0) AS total,
                                COALESCE(SUM(b.cantidad), 0) AS items
                            FROM pedido a
                            LEFT JOIN detalle_pedido b 
                                ON a.id = b.id_pedido
                            LEFT JOIN product_service d 
                                ON b.id_productservice = d.id
                            LEFT JOIN personal c 
                                ON a.usuario = c.IDPERSONAL
                            WHERE
                                d.id_sucursal = 5
                                AND a.cliente > 0
                                AND a.fecha >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
                            GROUP BY
                                c.usuario,
                                DATE_FORMAT(a.fecha, '%Y-%m')
                        ),
                        
                        ultimos AS (
                            SELECT
                                *,
                                ROW_NUMBER() OVER (
                                    PARTITION BY usuario
                                    ORDER BY mes DESC
                                ) AS rn
                            FROM ventas
                        ),
                        
                        usuarios_mes_actual AS (
                            SELECT usuario
                            FROM ultimos
                            WHERE rn = 1
                            AND mes = DATE_FORMAT(CURDATE(), '%Y-%m')
                        )
                        
                        SELECT
                            u.usuario,
                            u.mes,
                            u.tickets,
                            u.total,
                            u.items
                        FROM ultimos u
                        INNER JOIN usuarios_mes_actual m
                            ON u.usuario = m.usuario
                        WHERE u.rn <= 2
                        ORDER BY u.usuario ASC, u.mes DESC
                ";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$rows) {
                        return null;
                }

                $porUsuario = [];

                foreach ($rows as $row) {
                        $usuario = (string)($row['usuario'] ?? '');
                        if ($usuario === '') {
                                continue;
                        }

                        $porUsuario[$usuario][] = [
                                'mes' => (string)($row['mes'] ?? ''),
                                'tickets' => (int)($row['tickets'] ?? 0),
                                'total' => (float)($row['total'] ?? 0),
                                'items' => (float)($row['items'] ?? 0),
                        ];
                }

                $resultado = [];

                foreach ($porUsuario as $usuario => $mensual) {
                        if (empty($mensual)) {
                                continue;
                        }

                        $ultimo = null;
                        $anterior = null;

                        foreach ($mensual as $registro) {
                                if ((float)$registro['items'] <= 0 && (float)$registro['total'] <= 0 && (int)$registro['tickets'] <= 0) {
                                        continue;
                                }

                                $anterior = $ultimo;
                                $ultimo = $registro;
                        }

                        if ($ultimo === null) {
                                continue;
                        }

                        $resultado[] = [
                                'usuario' => $usuario,
                                'mes_actual' => $ultimo['mes'],
                                'mes_anterior' => $anterior['mes'] ?? null,
                                'tickets_actuales' => $ultimo['tickets'],
                                'tickets_anteriores' => $anterior['tickets'] ?? 0,
                                'total_actual' => $ultimo['total'],
                                'total_anteriores' => $anterior['total'] ?? 0,
                                'items_actuales' => $ultimo['items'],
                                'items_anteriores' => $anterior['items'] ?? 0,
                        ];
                }

                usort($resultado, static function (array $left, array $right): int {
                        return $right['items_actuales'] <=> $left['items_actuales']
                                ?: $right['total_actual'] <=> $left['total_actual']
                                ?: strcmp($left['usuario'], $right['usuario']);
                });

                return $resultado ?: null;
    }
}
?>