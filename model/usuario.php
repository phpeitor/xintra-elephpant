<?php
require_once "../database/conexion.php";

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
        $nombres = trim($data['nombres'] ?? '');
        $apellidos = trim($data['apellidos'] ?? '');
        $primerNombre = explode(' ', $nombres)[0] ?? '';
        $primerApellido = explode(' ', $apellidos)[0] ?? '';
        $usuario = strtolower($primerNombre . '.' . $primerApellido);

        $sql = "INSERT INTO personal 
                (APELLIDOS, NOMBRES, EMAIL, DOC, TLF, SEXO, USUARIO, PASSWORD, fecha_registro, IDSUCURSAL, IDESTADO, CARGO, fecha_baja, id_cartera)
                VALUES 
                (:apellidos, :nombres, :email, :documento, :telefono, :sexo, :usuario, MD5(:documento), :fecha_registro, 5, 1, 5, '1900-01-01 00:00:00', 5)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nombres',   $data['nombres'] ?? '');
        $stmt->bindValue(':apellidos', $data['apellidos'] ?? '');
        $stmt->bindValue(':email',     $data['email'] ?? '');
        $stmt->bindValue(':documento', $data['documento'] ?? '');
        $stmt->bindValue(':telefono',  $data['telefono'] ?? '');
        $stmt->bindValue(':sexo', (int)($data['sexo'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':fecha_registro', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function table_personal(): array{
         $sql = "SELECT
                *,
                CONCAT(nombres,' ',apellidos) AS nombre_completo
                FROM personal
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

    public function acceso_user(array $data):  ?array {
        $sql = "SELECT 
                    IDPERSONAL,
                    DOC,
                    NOMBRES,
                    USUARIO,
                    PASSWORD,
                    IDESTADO
                FROM personal
                WHERE USUARIO = :USUARIO
                AND PASSWORD= :PASSWORD
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':USUARIO', $data['usuario']);
        $stmt->bindValue(':PASSWORD', $data['password']);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function valida_documento(string $documento): ?array
    {
        $sql = "SELECT IDPERSONAL
                FROM personal
                WHERE DOC = :documento
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':documento', $documento);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function valida_documento_upd(string $documento, string $hash): ?array
    {
        $sql = "SELECT IDPERSONAL
                FROM personal
                WHERE DOC = :documento
                AND MD5(IDPERSONAL) <> :hash
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':documento', $documento);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

}
?>