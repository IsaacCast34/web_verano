<?php 
// classConnectionMySQL.php
class ConnectionMySQL {
    private $host;
    private $user;
    private $password;
    private $database;
    private $conn;

    public function __construct() {
        require_once "config_db.php";
        $this->host = HOST;
        $this->user = USER;
        $this->password = PASSWORD;
        $this->database = DATABASE;
    }

    public function CreateConnection() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Error al conectarse a MySQL: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    public function CloseConnection() {
        $this->conn->close();
    }

    public function ExecuteQuery($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Error en consulta SQL: " . $this->conn->error);
        }
        return $result;
    }

    public function GetLastId() {
        return $this->conn->insert_id;
    }

    public function GetCountAffectedRows() {
        return $this->conn->affected_rows;
    }

    public function GetRows($result) {
        return $result->fetch_row();
    }

    public function SetFreeResult($result) {
        $result->free_result();
    }

    public function getConn() {
        return $this->conn;
    }

    // Nuevo método para consultas preparadas
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
}
?>
