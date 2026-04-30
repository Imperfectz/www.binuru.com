<?php

class Database {
    private $host     = "127.0.0.1";
    private $dbname   = "biniru_proj";
    private $username = "root";
    private $password = "";
    private $port     = 3307;

    public function connect() {
        try {
            $conn = new PDO(
                "mysql:host=$this->host;port=$this->port;dbname=$this->dbname",
                $this->username,
                $this->password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $ex) {
            die("Connection error: " . $ex->getMessage());
        }
    }
}
