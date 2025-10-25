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

    public function baja(int $id): bool {
        $sql = "UPDATE personal 
                SET IDESTADO = 0, fecha_baja = :fecha_baja 
                WHERE IDPERSONAL = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':fecha_baja', $this->nowLima);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function actualizar(int $id): bool {
        $sql = "UPDATE personal SET IDESTADO = :IDESTADO, APELLIDOS = :APELLIDOS, NOMBRES = :NOMBRES, EMAIL = :EMAIL, DOC = :DOC, TLF = :TLF, SEXO = :SEXO WHERE id = :id";
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

    public function obtenerPorHash(string $hash): ?array {
        $sql = "SELECT 
                    IDPERSONAL,
                    NOMBRES,
                    APELLIDOS,
                    DOC,
                    EMAIL,
                    TLF,
                    SEXO
                FROM personal
                WHERE MD5(IDPERSONAL) = :hash
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

}
?>