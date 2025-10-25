<?php
require_once "conexion.php";

class Usuario {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function confirmarSiPendiente(int $id): bool {
        $sql = "UPDATE cliente SET contacto = 'CONFIRMADO' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
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

    public function table_personal(): array{
         $sql = "SELECT
                *,
                CONCAT(nombres,' ',apellidos) AS nombre_completo
                FROM bd_black.personal
                WHERE IDSUCURSAL = 5 AND APELLIDOS <>'ERROR' AND IDPERSONAL > 1
                ORDER BY idpersonal DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservados($fecha, $profesional) {
        $ini = $fecha . " 00:00:00";
        $fin = $fecha . " 23:59:59";
        
        $sql = "SELECT DATE_FORMAT(fecha_cita, '%H:%i') AS hhmm
                FROM citas
                WHERE profesional = :prof
                AND fecha_cita BETWEEN :ini AND :fin";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":prof", $profesional);
        $stmt->bindParam(":ini", $ini);
        $stmt->bindParam(":fin", $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>