<?php
require_once __DIR__ . '/../database/conexion.php';

class Categoria {
    private PDO $conn;
    private string $nowLima;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
        $tz = new DateTimeZone('America/Lima');
        $this->nowLima = (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
    }

    public function baja(int $id): bool {
        $sql = "UPDATE categoria
                SET estado = 0
                WHERE id = :id AND id_sucursal = 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function guardar(array $data): int {
        $sql = "INSERT INTO categoria
                (tpo, nombre, estado, fecha_creacion, id_sucursal)
                VALUES
                (:tpo, :nombre, :estado, :fecha_creacion, 5)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tpo', strtoupper(trim($data['tpo'] ?? '')));
        $stmt->bindValue(':nombre', trim($data['nombre'] ?? ''));
        $stmt->bindValue(':estado', (int)($data['estado'] ?? 1), PDO::PARAM_INT);
        $stmt->bindValue(':fecha_creacion', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function actualizarPorHash(string $hash, array $data): bool {
        $sql = "UPDATE categoria
                SET tpo = :tpo,
                    nombre = :nombre,
                    estado = :estado,
                    fecha_creacion = :fecha_creacion
                WHERE MD5(id) = :hash AND id_sucursal = 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tpo', strtoupper(trim($data['tpo'] ?? '')));
        $stmt->bindValue(':nombre', trim($data['nombre'] ?? ''));
        $stmt->bindValue(':estado', (int)($data['estado'] ?? 1), PDO::PARAM_INT);
        $stmt->bindValue(':fecha_creacion', $this->nowLima);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function table_categoria(): array {
        $sql = "SELECT id, tpo, nombre, estado, fecha_creacion, id_sucursal
                FROM categoria
                WHERE id_sucursal = 5
                ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorHash(string $hash): ?array {
        $sql = "SELECT id, tpo, nombre, estado, fecha_creacion, id_sucursal
                FROM categoria
                WHERE MD5(id) = :hash AND id_sucursal = 5
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function valida_categoria(string $tpo, string $nombre): ?array {
        $sql = "SELECT id
                FROM categoria
                WHERE UPPER(tpo) = UPPER(:tpo)
                AND UPPER(nombre) = UPPER(:nombre)
                AND id_sucursal = 5
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tpo', trim($tpo));
        $stmt->bindValue(':nombre', trim($nombre));
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function valida_categoria_upd(string $tpo, string $nombre, string $hash): ?array {
        $sql = "SELECT id
                FROM categoria
                WHERE UPPER(tpo) = UPPER(:tpo)
                AND UPPER(nombre) = UPPER(:nombre)
                AND id_sucursal = 5
                AND MD5(id) <> :hash
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tpo', trim($tpo));
        $stmt->bindValue(':nombre', trim($nombre));
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
}
?>