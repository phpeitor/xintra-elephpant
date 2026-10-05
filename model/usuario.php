<?php
require_once __DIR__ . '/../database/conexion.php';

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
                WHERE IDPERSONAL = :id AND IDSUCURSAL = @id_sucursal";
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
                WHERE MD5(IDPERSONAL) = :hash AND IDSUCURSAL = @id_sucursal";
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
                (APELLIDOS, NOMBRES, EMAIL, DOC, TLF, SEXO, USUARIO, PASSWORD, fecha_registro, IDSUCURSAL, IDESTADO, CARGO, fecha_baja)
                VALUES 
                (:apellidos, :nombres, :email, :documento, :telefono, :sexo, :usuario, MD5(:documento), :fecha_registro, @id_sucursal, 1, 5, '1900-01-01 00:00:00')";
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

    public function guardar_session(array $data): int {
        $sql = "INSERT INTO login 
                (tipo, fecha, id_user, ip)
                VALUES 
                (:tipo, :fecha, :id_user, :ip)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':tipo', $data['tipo'] ?? 'IN');
        $stmt->bindValue(':fecha', $this->nowLima);
        $stmt->bindValue(':id_user', $data['id_user'] ?? null);
        $stmt->bindValue(':ip', $data['ip'] ?? '0.0.0.0');
        $stmt->execute();
        return (int)$this->conn->lastInsertId();
    }

    public function table_personal(string $estado = 'ACTIVOS'): array{
          $sql = "SELECT
                p.IDPERSONAL,
                p.NOMBRES,
                p.APELLIDOS,
                p.DOC,
                p.SEXO,
                p.IDESTADO,
                p.fecha_registro,
                p.USUARIO,
                CASE WHEN p.PASSWORD IS NULL OR p.PASSWORD = '' THEN 0 ELSE 1 END AS password_configurada,
                p.CARGO,
                p.IDSUCURSAL,
                c.nombre AS CARGO_NOMBRE,
                s.SUCURSAL AS SUCURSAL_NOMBRE,
                TRIM(CONCAT_WS(' ', p.NOMBRES, p.APELLIDOS)) AS nombre_completo
                FROM personal p
                LEFT JOIN cargo c ON c.id = p.CARGO
                LEFT JOIN sucursal s ON s.IDSUCURSAL = p.IDSUCURSAL
                WHERE p.IDSUCURSAL = @id_sucursal AND p.APELLIDOS <>'ERROR' AND p.IDPERSONAL > 1
                ";
         if ($estado === 'ACTIVOS') {
             $sql .= ' AND p.IDESTADO = 1';
         } elseif ($estado === 'INACTIVOS') {
             $sql .= ' AND p.IDESTADO = 0';
         }
          $sql .= ' ORDER BY p.IDPERSONAL DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function catalogosSucursalCargo(): array
    {
        $sucursales = $this->conn->query(
            'SELECT IDSUCURSAL AS id, SUCURSAL AS nombre FROM sucursal WHERE IDESTADO = 1 ORDER BY SUCURSAL'
        )->fetchAll(PDO::FETCH_ASSOC);
        $cargos = $this->conn->query(
            'SELECT id, nombre FROM cargo WHERE estado = 1 ORDER BY nombre'
        )->fetchAll(PDO::FETCH_ASSOC);

        return ['sucursales' => $sucursales, 'cargos' => $cargos];
    }

    public function actualizarSucursalCargo(int $idPersonal, int $idSucursal, int $idCargo, int $idActor): void
    {
        if ($idPersonal < 1 || $idSucursal < 1 || $idCargo < 1) {
            throw new InvalidArgumentException('Selecciona una sucursal y un cargo válidos.');
        }
        $this->conn->beginTransaction();
        try {
            $personal = $this->conn->prepare(
                'SELECT IDSUCURSAL FROM personal WHERE IDPERSONAL = :id AND IDSUCURSAL = @id_sucursal LIMIT 1 FOR UPDATE'
            );
            $personal->bindValue(':id', $idPersonal, PDO::PARAM_INT);
            $personal->execute();
            $sucursalActual = $personal->fetchColumn();
            if (!$sucursalActual) {
                throw new RuntimeException('Usuario no encontrado en la sucursal actual.');
            }

            $targetSucursal = $this->conn->prepare('SELECT 1 FROM sucursal WHERE IDSUCURSAL = :id AND IDESTADO = 1 LIMIT 1');
            $targetSucursal->bindValue(':id', $idSucursal, PDO::PARAM_INT);
            $targetSucursal->execute();
            if (!$targetSucursal->fetchColumn()) {
                throw new InvalidArgumentException('La sucursal seleccionada no existe o está inactiva.');
            }

            $targetCargo = $this->conn->prepare('SELECT 1 FROM cargo WHERE id = :id AND estado = 1 LIMIT 1');
            $targetCargo->bindValue(':id', $idCargo, PDO::PARAM_INT);
            $targetCargo->execute();
            if (!$targetCargo->fetchColumn()) {
                throw new InvalidArgumentException('El cargo seleccionado no existe o está inactivo.');
            }

            if ((int)$sucursalActual !== $idSucursal) {
                if ($idPersonal === $idActor) {
                    throw new InvalidArgumentException('No puedes transferir tu propio usuario de sucursal con la sesión activa.');
                }
                $tickets = $this->conn->prepare('SELECT 1 FROM pedido WHERE usuario = :id LIMIT 1');
                $tickets->bindValue(':id', $idPersonal, PDO::PARAM_INT);
                $tickets->execute();
                if ($tickets->fetchColumn()) {
                    throw new RuntimeException('No se puede transferir de sucursal a un usuario con tickets históricos; así se conserva el historial en su sucursal original.');
                }

                $asistencias = $this->conn->prepare('SELECT 1 FROM asistencia_personal WHERE id_personal = :id LIMIT 1');
                $asistencias->bindValue(':id', $idPersonal, PDO::PARAM_INT);
                $asistencias->execute();
                if ($asistencias->fetchColumn()) {
                    throw new RuntimeException('No se puede transferir de sucursal a un usuario con asistencias históricas.');
                }
            }

            $update = $this->conn->prepare(
                'UPDATE personal SET IDSUCURSAL = :sucursal, CARGO = :cargo WHERE IDPERSONAL = :id AND IDSUCURSAL = @id_sucursal'
            );
            $update->bindValue(':sucursal', $idSucursal, PDO::PARAM_INT);
            $update->bindValue(':cargo', $idCargo, PDO::PARAM_INT);
            $update->bindValue(':id', $idPersonal, PDO::PARAM_INT);
            $update->execute();
            $this->conn->commit();
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function obtenerPerfil(int $idPersonal): ?array {
        $sql = "SELECT
                    p.IDPERSONAL AS id,
                    p.NOMBRES AS nombres,
                    p.APELLIDOS AS apellidos,
                    p.USUARIO AS usuario,
                    p.DOC AS documento,
                    p.EMAIL AS email,
                    p.TLF AS telefono,
                    p.CEL AS celular,
                    p.FECHANAC AS fecha_nacimiento,
                    p.SEXO AS sexo,
                    p.DIRECCION AS direccion,
                    p.DISTRITO AS distrito,
                    p.DPTO AS departamento,
                    p.IDESTADO AS estado,
                    p.fecha_registro,
                    p.IDSUCURSAL AS id_sucursal,
                    s.SUCURSAL AS sucursal,
                    c.nombre AS cargo
                FROM personal p
                LEFT JOIN sucursal s ON s.IDSUCURSAL = p.IDSUCURSAL
                LEFT JOIN cargo c ON c.id = p.CARGO
                WHERE p.IDPERSONAL = :id
                  AND p.IDSUCURSAL = @id_sucursal
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $idPersonal, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
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
                    , IDSUCURSAL
                FROM personal
                WHERE MD5(IDPERSONAL) = :hash AND IDSUCURSAL = @id_sucursal
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
                    IDESTADO,
                    IDSUCURSAL
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
                WHERE DOC = :documento AND IDSUCURSAL = @id_sucursal
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
                WHERE DOC = :documento AND IDSUCURSAL = @id_sucursal
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
