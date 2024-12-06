<?php
class Database {
    private $host = "localhost";
    private $db_name = "agendamentoDeViagens";
    private $username = "agendador";
    private $password = "launer@2020";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysqli:hostName={$this->host};dbName={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
        return $this->conn;
    }
}