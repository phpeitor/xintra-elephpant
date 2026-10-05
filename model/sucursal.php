<?php
require_once __DIR__ . '/../database/conexion.php';

class Sucursal
{
    private PDO $conn;
    private string $nowLima;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
        $this->nowLima = (new DateTimeImmutable('now', new DateTimeZone('America/Lima')))
            ->format('Y-m-d H:i:s');
    }

    public function listar(): array
    {
        $stmt = $this->conn->query(
            "SELECT s.IDSUCURSAL AS id, s.SUCURSAL AS nombre, s.DISTRITO AS distrito, s.DPTO AS departamento,
                    s.DIRECCION AS direccion, s.TLF AS telefono, s.IDESTADO AS estado,
                    COALESCE(q.cuota, 0) AS cuota,
                    (SELECT COUNT(1)
                     FROM pedido p
                     INNER JOIN personal per ON per.IDPERSONAL = p.usuario
                     WHERE per.IDSUCURSAL = s.IDSUCURSAL) AS tickets_usados,
                    nf.entorno_activo AS nubefact_entorno,
                    CASE
                        WHEN nf.entorno_activo = 'demo' AND nf.demo_url IS NOT NULL AND nf.demo_token IS NOT NULL THEN 1
                        WHEN nf.entorno_activo = 'production' AND nf.production_url IS NOT NULL AND nf.production_token IS NOT NULL THEN 1
                        ELSE 0
                    END AS nubefact_configurada
             FROM sucursal s
             LEFT JOIN sucursal_cuota q ON q.id_sucursal = s.IDSUCURSAL
             LEFT JOIN sucursal_nubefact nf ON nf.id_sucursal = s.IDSUCURSAL
              ORDER BY s.IDSUCURSAL DESC"
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

    public function aumentarCuota(int $idSucursal, int $incremento, int $idUsuario, string $motivo = ''): array
    {
        if ($idSucursal < 1 || $incremento < 1) {
            throw new InvalidArgumentException('Indica la sucursal y un incremento de cuota mayor que cero.');
        }
        $motivo = trim($motivo);
        if (mb_strlen($motivo) > 250) {
            throw new InvalidArgumentException('El motivo no puede superar 250 caracteres.');
        }

        $this->conn->beginTransaction();
        try {
            $branch = $this->conn->prepare('SELECT IDSUCURSAL FROM sucursal WHERE IDSUCURSAL = :id FOR UPDATE');
            $branch->bindValue(':id', $idSucursal, PDO::PARAM_INT);
            $branch->execute();
            if (!$branch->fetchColumn()) {
                throw new RuntimeException('Sucursal no encontrada.');
            }

            $currentStmt = $this->conn->prepare(
                'SELECT id, cuota FROM sucursal_cuota WHERE id_sucursal = :id_sucursal LIMIT 1 FOR UPDATE'
            );
            $currentStmt->bindValue(':id_sucursal', $idSucursal, PDO::PARAM_INT);
            $currentStmt->execute();
            $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
            $previous = $current ? (int)$current['cuota'] : 0;
            $newQuota = $previous + $incremento;
            if ($newQuota > 2147483647) {
                throw new InvalidArgumentException('El total de cuota supera el límite permitido.');
            }

            if ($current) {
                $quotaId = (int)$current['id'];
                $update = $this->conn->prepare(
                    "UPDATE sucursal_cuota SET cuota = :cuota, fecha = :fecha, tipo = 'Ventas' WHERE id = :id"
                );
                $update->bindValue(':cuota', $newQuota, PDO::PARAM_INT);
                $update->bindValue(':fecha', $this->nowLima);
                $update->bindValue(':id', $quotaId, PDO::PARAM_INT);
                $update->execute();
            } else {
                $insertQuota = $this->conn->prepare(
                    "INSERT INTO sucursal_cuota (id_sucursal, cuota, fecha, tipo) VALUES (:id_sucursal, :cuota, :fecha, 'Ventas')"
                );
                $insertQuota->bindValue(':id_sucursal', $idSucursal, PDO::PARAM_INT);
                $insertQuota->bindValue(':cuota', $newQuota, PDO::PARAM_INT);
                $insertQuota->bindValue(':fecha', $this->nowLima);
                $insertQuota->execute();
                $quotaId = (int)$this->conn->lastInsertId();
            }

            $log = $this->conn->prepare(
                'INSERT INTO sucursal_cuota_log
                    (id_sucursal, id_sucursal_cuota, cuota_anterior, incremento, cuota_nueva, id_usuario, motivo, fecha)
                 VALUES
                    (:id_sucursal, :id_cuota, :anterior, :incremento, :nueva, :id_usuario, :motivo, :fecha)'
            );
            $log->bindValue(':id_sucursal', $idSucursal, PDO::PARAM_INT);
            $log->bindValue(':id_cuota', $quotaId, PDO::PARAM_INT);
            $log->bindValue(':anterior', $previous, PDO::PARAM_INT);
            $log->bindValue(':incremento', $incremento, PDO::PARAM_INT);
            $log->bindValue(':nueva', $newQuota, PDO::PARAM_INT);
            $log->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $log->bindValue(':motivo', $motivo);
            $log->bindValue(':fecha', $this->nowLima);
            $log->execute();
            $this->conn->commit();

            return ['cuota_anterior' => $previous, 'incremento' => $incremento, 'cuota_nueva' => $newQuota];
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function historialCuota(int $idSucursal): array
    {
        $stmt = $this->conn->prepare(
            "SELECT l.fecha, l.cuota_anterior, l.incremento, l.cuota_nueva, l.motivo,
                    COALESCE(CONCAT_WS(' ', p.NOMBRES, p.APELLIDOS), 'Usuario no disponible') AS usuario
             FROM sucursal_cuota_log l
             LEFT JOIN personal p ON p.IDPERSONAL = l.id_usuario
             WHERE l.id_sucursal = :id_sucursal
             ORDER BY l.id DESC
             LIMIT 50"
        );
        $stmt->bindValue(':id_sucursal', $idSucursal, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function configuracionNubefact(int $idSucursal): array
    {
        $stmt = $this->conn->prepare('SELECT * FROM sucursal_nubefact WHERE id_sucursal = :id LIMIT 1');
        $stmt->bindValue(':id', $idSucursal, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $result = ['entorno_activo' => $row['entorno_activo'] ?? 'demo'];
        foreach (['demo', 'production'] as $environment) {
            $result[$environment] = [
                'url' => (string)($row[$environment . '_url'] ?? ''),
                'token_configurado' => trim((string)($row[$environment . '_token'] ?? '')) !== '',
                'serie_boleta' => (string)($row[$environment . '_serie_boleta'] ?? ''),
                'serie_factura' => (string)($row[$environment . '_serie_factura'] ?? ''),
                'numero_inicial_boleta' => (int)($row[$environment . '_numero_inicial_boleta'] ?? 1),
                'numero_inicial_factura' => (int)($row[$environment . '_numero_inicial_factura'] ?? 1),
            ];
        }
        return $result;
    }

    public function guardarConfiguracionNubefact(int $idSucursal, int $idUsuario, array $data): void
    {
        $activeEnvironment = strtolower(trim((string)($data['entorno_activo'] ?? 'demo')));
        if (!in_array($activeEnvironment, ['demo', 'production'], true)) {
            throw new InvalidArgumentException('Selecciona el entorno DEMO o PRODUCCIÓN.');
        }

        $branch = $this->conn->prepare('SELECT 1 FROM sucursal WHERE IDSUCURSAL = :id');
        $branch->execute([':id' => $idSucursal]);
        if (!$branch->fetchColumn()) {
            throw new RuntimeException('Sucursal no encontrada.');
        }

        $currentStmt = $this->conn->prepare('SELECT * FROM sucursal_nubefact WHERE id_sucursal = :id LIMIT 1');
        $currentStmt->execute([':id' => $idSucursal]);
        $current = $currentStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $values = ['entorno_activo' => $activeEnvironment];

        foreach (['demo', 'production'] as $environment) {
            $prefix = $environment . '_';
            $url = trim((string)($data[$prefix . 'url'] ?? ''));
            $tokenInput = trim((string)($data[$prefix . 'token'] ?? ''));
            $token = $tokenInput !== '' ? $tokenInput : (string)($current[$prefix . 'token'] ?? '');
            $seriesBoleta = strtoupper(trim((string)($data[$prefix . 'serie_boleta'] ?? '')));
            $seriesFactura = strtoupper(trim((string)($data[$prefix . 'serie_factura'] ?? '')));
            $numberBoleta = filter_var($data[$prefix . 'numero_inicial_boleta'] ?? 1, FILTER_VALIDATE_INT);
            $numberFactura = filter_var($data[$prefix . 'numero_inicial_factura'] ?? 1, FILTER_VALIDATE_INT);

            $isConfigured = $url !== '' || $token !== '' || $seriesBoleta !== '' || $seriesFactura !== '';
            if ($environment === $activeEnvironment && !$isConfigured) {
                throw new InvalidArgumentException('Completa la configuración del entorno activo.');
            }
            if ($isConfigured) {
                $host = strtolower((string)parse_url($url, PHP_URL_HOST));
                $allowedHosts = $environment === 'demo'
                    ? ['demo.nubefact.com', 'api.nubefact.com']
                    : ['api.nubefact.com'];
                if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https'
                    || !in_array($host, $allowedHosts, true)) {
                    throw new InvalidArgumentException('La URL de ' . strtoupper($environment) . ' debe ser HTTPS y corresponder a NubeFact.');
                }
                if ($token === '') {
                    throw new InvalidArgumentException('Ingresa el token de ' . strtoupper($environment) . ' o deja el token guardado sin cambios.');
                }
                if (!preg_match('/^B[A-Z0-9]{3}$/', $seriesBoleta) || !preg_match('/^F[A-Z0-9]{3}$/', $seriesFactura)) {
                    throw new InvalidArgumentException('Las series deben tener 4 caracteres: boleta inicia con B y factura con F.');
                }
                if ($numberBoleta === false || $numberBoleta < 1 || $numberBoleta > 99999999
                    || $numberFactura === false || $numberFactura < 1 || $numberFactura > 99999999) {
                    throw new InvalidArgumentException('Los correlativos iniciales deben ser enteros entre 1 y 99,999,999.');
                }
            } else {
                $url = '';
                $token = '';
                $seriesBoleta = '';
                $seriesFactura = '';
                $numberBoleta = $numberBoleta === false ? 1 : max(1, (int)$numberBoleta);
                $numberFactura = $numberFactura === false ? 1 : max(1, (int)$numberFactura);
            }

            $values[$prefix . 'url'] = $url !== '' ? $url : null;
            $values[$prefix . 'token'] = $token !== '' ? $token : null;
            $values[$prefix . 'serie_boleta'] = $seriesBoleta !== '' ? $seriesBoleta : null;
            $values[$prefix . 'serie_factura'] = $seriesFactura !== '' ? $seriesFactura : null;
            $values[$prefix . 'numero_inicial_boleta'] = $numberBoleta;
            $values[$prefix . 'numero_inicial_factura'] = $numberFactura;
        }

        $values['id_sucursal'] = $idSucursal;
        $values['actualizado_por'] = $idUsuario;
        $values['actualizado_en'] = $this->nowLima;
        $columns = array_keys($values);
        $quotedColumns = implode(', ', array_map(static fn(string $column): string => '`' . $column . '`', $columns));
        $placeholders = implode(', ', array_map(static fn(string $column): string => ':' . $column, $columns));
        $updates = [];
        foreach ($columns as $column) {
            if ($column !== 'id_sucursal') {
                $updates[] = '`' . $column . '` = VALUES(`' . $column . '`)';
            }
        }
        $sql = 'INSERT INTO sucursal_nubefact (' . $quotedColumns . ') VALUES (' . $placeholders . ') '
            . 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
        $stmt = $this->conn->prepare($sql);
        foreach ($values as $column => $value) {
            $type = $value === null ? PDO::PARAM_NULL : (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $stmt->bindValue(':' . $column, $value, $type);
        }
        $stmt->execute();
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
