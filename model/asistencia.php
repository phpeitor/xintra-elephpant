<?php
require_once __DIR__ . '/../database/conexion.php';

class Asistencia
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
    }

    public function usuarios(): array
    {
        $stmt = $this->conn->query("SELECT IDPERSONAL AS id, CONCAT(NOMBRES, ' ', APELLIDOS) AS nombre FROM personal WHERE IDSUCURSAL = 5 AND APELLIDOS <> 'ERROR' AND IDPERSONAL > 1 AND IDESTADO = 1 ORDER BY NOMBRES, APELLIDOS");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estado(int $idUsuario): ?string
    {
        $stmt = $this->conn->prepare('SELECT tipo FROM asistencia_personal WHERE id_personal = :id ORDER BY fecha DESC, id DESC LIMIT 1');
        $stmt->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: null;
    }

    public function registrar(int $idUsuario, string $tipo): array
    {
        if (!in_array($tipo, ['ENTRADA', 'SALIDA'], true)) throw new RuntimeException('Tipo de marcación no válido.');
        $this->conn->beginTransaction();
        try {
            $usuario = $this->conn->prepare("SELECT CONCAT(NOMBRES, ' ', APELLIDOS) FROM personal WHERE IDPERSONAL = :id AND IDESTADO = 1 LIMIT 1");
            $usuario->bindValue(':id', $idUsuario, PDO::PARAM_INT);
            $usuario->execute();
            $nombre = $usuario->fetchColumn();
            if (!$nombre) throw new RuntimeException('El usuario no existe o está inactivo.');

            $ultimo = $this->conn->prepare('SELECT tipo FROM asistencia_personal WHERE id_personal = :id ORDER BY fecha DESC, id DESC LIMIT 1 FOR UPDATE');
            $ultimo->bindValue(':id', $idUsuario, PDO::PARAM_INT);
            $ultimo->execute();
            $ultimoTipo = $ultimo->fetchColumn() ?: null;
            if ($tipo === 'ENTRADA' && $ultimoTipo === 'ENTRADA') throw new RuntimeException("$nombre ya tiene una entrada abierta. Registra la salida antes de marcar otra entrada.");
            if ($tipo === 'SALIDA' && $ultimoTipo !== 'ENTRADA') throw new RuntimeException("$nombre no tiene una entrada abierta. Primero registra la entrada.");

            $now = new DateTimeImmutable('now', new DateTimeZone('America/Lima'));
            $insert = $this->conn->prepare('INSERT INTO asistencia_personal (id_personal, tipo, fecha) VALUES (:id, :tipo, :fecha)');
            $insert->bindValue(':id', $idUsuario, PDO::PARAM_INT);
            $insert->bindValue(':tipo', $tipo);
            $insert->bindValue(':fecha', $now->format('Y-m-d H:i:s'));
            $insert->execute();
            $this->conn->commit();
            return ['nombre' => $nombre, 'tipo' => $tipo, 'fecha' => $now->format('Y-m-d H:i:s')];
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function eventos(string $inicio, string $fin, ?int $idUsuario = null): array
    {
        $sql = "SELECT a.id, a.tipo, a.fecha, a.id_personal AS usuario_id, CONCAT(p.NOMBRES, ' ', p.APELLIDOS) AS usuario FROM asistencia_personal a INNER JOIN personal p ON p.IDPERSONAL = a.id_personal WHERE a.fecha >= :inicio AND a.fecha < :fin";
        if ($idUsuario !== null) $sql .= ' AND a.id_personal = :id_usuario';
        $sql .= ' ORDER BY a.fecha ASC, a.id ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':inicio', $inicio);
        $stmt->bindValue(':fin', $fin);
        if ($idUsuario !== null) $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $this->formatRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function recientes(?int $idUsuario = null): array
    {
        $sql = "SELECT a.id, a.tipo, a.fecha, a.id_personal AS usuario_id, CONCAT(p.NOMBRES, ' ', p.APELLIDOS) AS usuario FROM asistencia_personal a INNER JOIN personal p ON p.IDPERSONAL = a.id_personal";
        if ($idUsuario !== null) $sql .= ' WHERE a.id_personal = :id_usuario';
        $sql .= ' ORDER BY a.fecha DESC, a.id DESC LIMIT 10';
        $stmt = $this->conn->prepare($sql);
        if ($idUsuario !== null) $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $this->formatRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function formatRows(array $rows): array
    {
        return array_map(static function (array $row): array {
            $entrada = $row['tipo'] === 'ENTRADA';
            $hora = substr($row['fecha'], 11, 8);
            return ['id' => 'asistencia-' . $row['id'], 'title' => $hora . ' · ' . ($entrada ? 'Entrada' : 'Salida') . ' · ' . $row['usuario'], 'start' => substr($row['fecha'], 0, 10), 'tipo' => $row['tipo'], 'usuario' => $row['usuario'], 'usuarioId' => (int)$row['usuario_id'], 'fecha' => $row['fecha'], 'allDay' => true, 'className' => [$entrada ? 'attendance-entry' : 'attendance-exit']];
        }, $rows);
    }
}
