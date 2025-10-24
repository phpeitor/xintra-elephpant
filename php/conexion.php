<?php
class Conexion {
    private $host = "127.0.0.1";
    private $dbname = "bd_black";
    private $user = "root";
    private $pass = "admin123";
    private $conn;

    public function conectar() {
        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname.";charset=utf8", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error de conexión: " . $e->getMessage()]);
            exit;
        }
    }
}
?>