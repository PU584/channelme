<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host = '127.0.0.1:3325';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'user_auth_system';

    private function __construct() {
        // Create a new connection
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Get the instance of the database connection
    public static function getInstance() {
        if (self::$instance == null) {  
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Return the connection
    public function getConnection() {
        return $this->conn;
    }
}
?>
