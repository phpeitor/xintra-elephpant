<?php
require_once __DIR__ . '/../database/conexion.php';

class Horario
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
    }

    public function obtenerPorHash(string $hash): array
    {
        $sql = "SELECT p.IDPERSONAL, CONCAT(p.NOMBRES, ' ', p.APELLIDOS) AS nombre,
                       h.dia_semana, h.hora_inicio, h.hora_fin, h.activo
                FROM personal p
                LEFT JOIN horario_personal h ON h.id_personal = p.IDPERSONAL
                WHERE MD5(p.IDPERSONAL) = :hash
                ORDER BY h.dia_semana";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':hash', $hash);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarPorHash(string $hash, array $horarios): void
    {
        $this->conn->beginTransaction();
        try {
            $find = $this->conn->prepare('SELECT IDPERSONAL FROM personal WHERE MD5(IDPERSONAL) = :hash LIMIT 1');
            $find->bindValue(':hash', $hash);
            $find->execute();
            $idPersonal = $find->fetchColumn();

            if (!$idPersonal) {
                throw new RuntimeException('Usuario no encontrado.');
            }

            $delete = $this->conn->prepare('DELETE FROM horario_personal WHERE id_personal = :id_personal');
            $delete->bindValue(':id_personal', $idPersonal, PDO::PARAM_INT);
            $delete->execute();

            $insert = $this->conn->prepare(
                'INSERT INTO horario_personal (id_personal, dia_semana, hora_inicio, hora_fin, activo)
                 VALUES (:id_personal, :dia_semana, :hora_inicio, :hora_fin, :activo)'
            );

            foreach ($horarios as $horario) {
                $dia = (int)($horario['dia_semana'] ?? 0);
                $activo = (int)($horario['activo'] ?? 0) === 1 ? 1 : 0;
                $inicio = trim((string)($horario['hora_inicio'] ?? '')) ?: null;
                $fin = trim((string)($horario['hora_fin'] ?? '')) ?: null;

                if ($dia < 1 || $dia > 7) {
                    throw new RuntimeException('Día de semana inválido.');
                }
                if ($activo && (!$inicio || !$fin || $inicio >= $fin)) {
                    throw new RuntimeException('Completa correctamente las horas de cada día activo.');
                }

                $insert->bindValue(':id_personal', $idPersonal, PDO::PARAM_INT);
                $insert->bindValue(':dia_semana', $dia, PDO::PARAM_INT);
                $insert->bindValue(':hora_inicio', $inicio);
                $insert->bindValue(':hora_fin', $fin);
                $insert->bindValue(':activo', $activo, PDO::PARAM_INT);
                $insert->execute();
            }

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
