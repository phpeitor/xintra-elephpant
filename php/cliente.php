<?php
require_once "conexion.php";

class Cliente {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->conectar();
    }

    public function confirmarSiPendiente(int $id): bool {
        $sql = "UPDATE cliente SET contacto = 'CONFIRMADO' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function guardar($data) {
        $sql = "INSERT INTO citas 
                (nombre, email, dni, telefono, fecha_nacimiento, direccion, mensaje, fecha_cita, precio, fecha_registro, profesional, status, sede, tipo) 
                VALUES ( :nombre, :email, :dni, :telefono, :fecha_nacimiento, :direccion, :mensaje, :fecha_cita, :precio, :fecha_registro, :profesional, :status, :sede, :tipo)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nombre", $data["nombre"]);
        $stmt->bindParam(":email", $data["email"]);
        $stmt->bindParam(":dni", $data["dni"]);
        $stmt->bindParam(":telefono", $data["telefono"]);
        $stmt->bindParam(":fecha_nacimiento", $data["fecha_nacimiento"]);
        $stmt->bindParam(":direccion", $data["direccion"]);
        $stmt->bindParam(":mensaje", $data["mensaje"]);
        $stmt->bindParam(":fecha_cita", $data["fecha_cita"]);
        $stmt->bindParam(":precio", $data["precio"]);
        $stmt->bindParam(":fecha_registro", $data["fecha_registro"]);
        $stmt->bindParam(":profesional", $data["profesional"]); 
        $stmt->bindParam(":status", $data["status"]);
        $stmt->bindParam(":sede", $data["sede"]);
        $stmt->bindParam(":tipo", $data["tipo"]);
        //$stmt->debugDumpParams();
        if (!$stmt->execute()) return false;
        return (int)$this->conn->lastInsertId();
    }

    public function table_cliente(): array{
         $sql = "SELECT
                id,
                CONCAT(nombres,' ',apellidos) AS nombre_completo,
                documento,
                contacto,
                sexo,
                fecha_creacion
            FROM bd_black.cliente
            ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservados($fecha, $profesional) {
        $ini = $fecha . " 00:00:00";
        $fin = $fecha . " 23:59:59";
        
        $sql = "SELECT DATE_FORMAT(fecha_cita, '%H:%i') AS hhmm
                FROM citas
                WHERE profesional = :prof
                AND fecha_cita BETWEEN :ini AND :fin";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":prof", $profesional);
        $stmt->bindParam(":ini", $ini);
        $stmt->bindParam(":fin", $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>