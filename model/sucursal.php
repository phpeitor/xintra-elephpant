<?php
require_once __DIR__ . '/../database/conexion.php';

class Sucursal
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
    }

    public function listar(): array
    {
        $stmt = $this->conn->query(
            'SELECT IDSUCURSAL AS id, SUCURSAL AS nombre, DISTRITO AS distrito, DPTO AS departamento,
                    DIRECCION AS direccion, TLF AS telefono, IDESTADO AS estado
             FROM sucursal
             ORDER BY IDSUCURSAL DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT IDSUCURSAL AS id, SUCURSAL AS nombre, DISTRITO AS distrito, DPTO AS departamento,
                    DIRECCION AS direccion, TLF AS telefono, IDESTADO AS estado
             FROM sucursal
             WHERE IDSUCURSAL = :id
             LIMIT 1'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function guardar(array $data): int
    {
        $data = $this->normalizar($data);
        $stmt = $this->conn->prepare(
            'INSERT INTO sucursal (SUCURSAL, DISTRITO, DPTO, DIRECCION, TLF, IDESTADO)
             VALUES (:nombre, :distrito, :departamento, :direccion, :telefono, :estado)'
        );
        $this->bindData($stmt, $data);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $data = $this->normalizar($data);
        $stmt = $this->conn->prepare(
            'UPDATE sucursal
             SET SUCURSAL = :nombre,
                 DISTRITO = :distrito,
                 DPTO = :departamento,
                 DIRECCION = :direccion,
                 TLF = :telefono,
                 IDESTADO = :estado
             WHERE IDSUCURSAL = :id'
        );
        $this->bindData($stmt, $data);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function cambiarEstado(int $id, int $estado): bool
    {
        if (!in_array($estado, [0, 1], true)) {
            throw new InvalidArgumentException('Estado de sucursal inválido.');
        }
        $stmt = $this->conn->prepare('UPDATE sucursal SET IDESTADO = :estado WHERE IDSUCURSAL = :id');
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function normalizar(array $data): array
    {
        $result = [
            'nombre' => trim((string)($data['nombre'] ?? '')),
            'distrito' => trim((string)($data['distrito'] ?? '')),
            'departamento' => trim((string)($data['departamento'] ?? '')),
            'direccion' => trim((string)($data['direccion'] ?? '')),
            'telefono' => trim((string)($data['telefono'] ?? '')),
            'estado' => (int)($data['estado'] ?? 1),
        ];

        if ($result['nombre'] === '') {
            throw new InvalidArgumentException('El nombre de la sucursal es obligatorio.');
        }
        if (mb_strlen($result['nombre']) > 100 || mb_strlen($result['distrito']) > 200
            || mb_strlen($result['departamento']) > 200 || mb_strlen($result['direccion']) > 200
            || mb_strlen($result['telefono']) > 20) {
            throw new InvalidArgumentException('Uno de los campos supera la longitud máxima permitida.');
        }
        if (!in_array($result['estado'], [0, 1], true)) {
            throw new InvalidArgumentException('Estado de sucursal inválido.');
        }
        return $result;
    }

    private function bindData(PDOStatement $stmt, array $data): void
    {
        $stmt->bindValue(':nombre', $data['nombre']);
        foreach (['distrito', 'departamento', 'direccion', 'telefono'] as $field) {
            $value = $data[$field] !== '' ? $data[$field] : null;
            $stmt->bindValue(':' . $field, $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        }
        $stmt->bindValue(':estado', $data['estado'], PDO::PARAM_INT);
    }
}
