<?php
class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;

    public $conn;

    public function __construct() {
        // Cargar variables de entorno desde el archivo .env
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile);
            $this->host = $env['DB_HOST'] ?? '127.0.0.1';
            $this->port = $env['DB_PORT'] ?? '3320';
            $this->db_name = $env['DB_NAME'] ?? 'asadero_el_carbon';
            $this->username = $env['DB_USER'] ?? 'root';
            $this->password = $env['DB_PASS'] ?? '';
        } else {
            // Valores por defecto si no existe el archivo .env (no recomendado para producción)
            $this->host = "127.0.0.1";
            $this->port = "3320";
            $this->db_name = "asadero_el_carbon";
            $this->username = "root";
            $this->password = "";
        }
    }

    public function conectar()
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>