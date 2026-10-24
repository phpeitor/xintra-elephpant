<?php
require_once __DIR__ . '/../database/conexion.php';

class Proveedor
{
    private PDO $conn;
    private string $nowLima;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
        $this->nowLima = (new DateTimeImmutable('now', new DateTimeZone('America/Lima')))->format('Y-m-d H:i:s');
    }

    public function listar(): array
    {
        $stmt = $this->conn->query(
            'SELECT id, tipo_documento, documento, razon_social, email, telefono, direccion, estado, fecha_creacion
             FROM proveedor
             WHERE id_sucursal = @id_sucursal
             ORDER BY id DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT id, tipo_documento, documento, razon_social, email, telefono, direccion, estado
             FROM proveedor
             WHERE id = :id AND id_sucursal = @id_sucursal
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function guardar(array $input): int
    {
        $data = $this->normalizar($input);
        $stmt = $this->conn->prepare(
            'INSERT INTO proveedor
                (id_sucursal, tipo_documento, documento, razon_social, email, telefono, direccion, estado, fecha_creacion)
             VALUES
                (@id_sucursal, :tipo_documento, :documento, :razon_social, :email, :telefono, :direccion, :estado, :fecha_creacion)'
        );
        $this->bindData($stmt, $data);
        $stmt->bindValue(':fecha_creacion', $this->nowLima);
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function actualizar(int $id, array $input): bool
    {
        $data = $this->normalizar($input);
        $stmt = $this->conn->prepare(
            'UPDATE proveedor
             SET tipo_documento = :tipo_documento,
                 documento = :documento,
                 razon_social = :razon_social,
                 email = :email,
                 telefono = :telefono,
                 direccion = :direccion,
                 estado = :estado,
                 fecha_actualizacion = :fecha_actualizacion
             WHERE id = :id AND id_sucursal = @id_sucursal'
        );
        $this->bindData($stmt, $data);
        $stmt->bindValue(':fecha_actualizacion', $this->nowLima);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function cambiarEstado(int $id, int $estado): bool
    {
        if (!in_array($estado, [0, 1], true)) {
            throw new InvalidArgumentException('Estado de proveedor inválido.');
        }
        $stmt = $this->conn->prepare(
            'UPDATE proveedor SET estado = :estado, fecha_actualizacion = :fecha
             WHERE id = :id AND id_sucursal = @id_sucursal'
        );
        $stmt->execute([':estado' => $estado, ':fecha' => $this->nowLima, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private function normalizar(array $input): array
    {
        $tipo = strtoupper(trim((string)($input['tipo_documento'] ?? 'RUC')));
        $documento = preg_replace('/\D+/', '', (string)($input['documento'] ?? ''));
        $razonSocial = trim((string)($input['razon_social'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));
        $telefono = trim((string)($input['telefono'] ?? ''));
        $direccion = trim((string)($input['direccion'] ?? ''));
        $estado = filter_var($input['estado'] ?? 1, FILTER_VALIDATE_INT);

        if (!in_array($tipo, ['RUC', 'DNI'], true)) {
            throw new InvalidArgumentException('Selecciona RUC o DNI como tipo de documento.');
        }
        $longitudDocumento = $tipo === 'RUC' ? 11 : 8;
        if (!preg_match('/^\d{' . $longitudDocumento . '}$/', $documento)) {
            throw new InvalidArgumentException('El ' . $tipo . ' debe contener exactamente ' . $longitudDocumento . ' dígitos.');
        }
        if (mb_strlen($razonSocial) < 2 || mb_strlen($razonSocial) > 150) {
            throw new InvalidArgumentException('Ingresa un nombre o razón social de 2 a 150 caracteres.');
        }
        if ($email !== '' && (mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new InvalidArgumentException('Ingresa un correo válido de hasta 150 caracteres.');
        }
        if (mb_strlen($telefono) > 20 || mb_strlen($direccion) > 200) {
            throw new InvalidArgumentException('El teléfono o la dirección superan la longitud máxima permitida.');
        }
        if (!in_array($estado, [0, 1], true)) {
            throw new InvalidArgumentException('Estado de proveedor inválido.');
        }

        return compact('tipo', 'documento', 'razonSocial', 'email', 'telefono', 'direccion', 'estado');
    }

    private function bindData(PDOStatement $stmt, array $data): void
    {
        $stmt->bindValue(':tipo_documento', $data['tipo']);
        $stmt->bindValue(':documento', $data['documento']);
        $stmt->bindValue(':razon_social', $data['razonSocial']);
        foreach (['email', 'telefono', 'direccion'] as $field) {
            $value = $data[$field] !== '' ? $data[$field] : null;
            $stmt->bindValue(':' . $field, $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        }
        $stmt->bindValue(':estado', $data['estado'], PDO::PARAM_INT);
    }
}
