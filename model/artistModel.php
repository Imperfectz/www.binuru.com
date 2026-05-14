<?php

class Artist
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM artists ORDER BY artist_name";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name)
    {
        $query = "INSERT INTO artists (artist_name) VALUES (:name)";
        $response = $this->conn->prepare($query);
        $response->bindParam(':name', $name);
        return $response->execute() ? $this->conn->lastInsertId() : false;
    }
}
