<?php
require_once "../database/conexion.php";

class Cliente {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function guardar(array $data): int {
        $sql = "INSERT INTO cliente 
                (nombres, apellidos, email, documento, telefono, sexo, fecha_creacion, id_sucursal)
                VALUES 
                (:nombres, :apellidos, :email, :documento, :telefono, :sexo, :fecha_creacion, 5)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nombres',   $data['nombres'] ?? '');
        $stmt->bindValue(':apellidos', $data['apellidos'] ?? '');
        $stmt->bindValue(':email',     $data['email'] ?? '');
        $stmt->bindValue(':documento', $data['documento'] ?? '');
        $stmt->bindValue(':telefono',  $data['telefono'] ?? '');
        $stmt->bindValue(':sexo', (int)($data['sexo'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':fecha_creacion', $this->nowLima);

        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function table_cliente(): array{
         $sql = "SELECT
                id,
                CONCAT(nombres,' ',apellidos) AS nombre_completo,
                documento,
                email,
                telefono,
                sexo,
                fecha_creacion
            FROM cliente
            ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>