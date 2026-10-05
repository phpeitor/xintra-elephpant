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

    public function actualizarPorHash(string $hash, array $data): bool {
        $data = $this->normalizarDocumento($data);
        $sql = "UPDATE cliente 
                SET apellidos = :apellidos,
                    nombres = :nombres,
                    email = :email,
                    documento = :documento,
                    tipo_documento = :tipo_documento,
                    telefono = :telefono,
                    sexo = :sexo
                WHERE MD5(id) = :hash AND id_sucursal = @id_sucursal";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':apellidos', $data['apellidos']);
        $stmt->bindValue(':nombres', $data['nombres']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':documento', $data['documento']);
        $stmt->bindValue(':tipo_documento', $data['tipo_documento']);
        $stmt->bindValue(':telefono', $data['telefono']);
        $stmt->bindValue(':sexo', (int)$data['sexo'], PDO::PARAM_INT);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function guardar(array $data): int {
        $data = $this->normalizarDocumento($data);
        $sql = "INSERT INTO cliente 
                (nombres, apellidos, email, documento, tipo_documento, telefono, sexo, fecha_creacion, id_sucursal)
                VALUES 
                (:nombres, :apellidos, :email, :documento, :tipo_documento, :telefono, :sexo, :fecha_creacion, @id_sucursal)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':nombres',   $data['nombres'] ?? '');
        $stmt->bindValue(':apellidos', $data['apellidos'] ?? '');
        $stmt->bindValue(':email',     $data['email'] ?? '');
        $stmt->bindValue(':documento', $data['documento'] ?? '');
        $stmt->bindValue(':tipo_documento', $data['tipo_documento']);
        $stmt->bindValue(':telefono',  $data['telefono'] ?? '');
        $stmt->bindValue(':sexo', (int)($data['sexo'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':fecha_creacion', $this->nowLima);

        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function table_cliente(): array{
         $sql = "SELECT
                id,
                TRIM(CONCAT_WS(' ', nombres, apellidos)) AS nombre_completo,
                documento,
                tipo_documento,
                email,
                telefono,
                sexo,
                fecha_creacion
            FROM cliente
            WHERE id_sucursal = @id_sucursal
            ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function normalizarDocumento(array $data): array
    {
        $tipo = strtoupper(trim((string)($data['tipo_documento'] ?? 'DNI')));
        $documento = trim((string)($data['documento'] ?? ''));
        $nombres = trim((string)($data['nombres'] ?? ''));
        $apellidos = trim((string)($data['apellidos'] ?? ''));

        if ($tipo === 'DNI') {
            if (!preg_match('/^\d{8}$/', $documento)) {
                throw new InvalidArgumentException('El DNI debe contener exactamente 8 dígitos.');
            }
            if (mb_strlen($nombres) < 2 || mb_strlen($apellidos) < 2) {
                throw new InvalidArgumentException('Para DNI ingresa nombres y apellidos.');
            }
            if (mb_strlen($nombres) > 50 || mb_strlen($apellidos) > 50) {
                throw new InvalidArgumentException('Los nombres o apellidos exceden el máximo permitido.');
            }
        } elseif ($tipo === 'RUC') {
            if (!preg_match('/^\d{11}$/', $documento)) {
                throw new InvalidArgumentException('El RUC debe contener exactamente 11 dígitos.');
            }
            if (mb_strlen($nombres) < 2 || mb_strlen($nombres) > 100) {
                throw new InvalidArgumentException('Ingresa una razón social válida (máximo 100 caracteres).');
            }
            $apellidos = '';
        } else {
            throw new InvalidArgumentException('El tipo de documento debe ser DNI o RUC.');
        }

        $data['tipo_documento'] = $tipo;
        $data['documento'] = $documento;
        $data['nombres'] = $nombres;
        $data['apellidos'] = $apellidos;
        return $data;
    }

    public function obtenerPorHash(string $hash): ?array {
        $sql = "SELECT *
                FROM cliente
                WHERE MD5(id) = :hash AND id_sucursal = @id_sucursal
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
}
?>
