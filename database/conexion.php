<?php
require_once __DIR__ . '/../config/bootstrap.php';

class Conexion {
    private $conn;

    public function conectar() {
        try {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $dbname = $_ENV['DB_NAME'] ?? 'bd_black';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';

            $this->conn = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conn;
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error de conexión: " . $e->getMessage()
            ]);
            exit;
        }
    }
}