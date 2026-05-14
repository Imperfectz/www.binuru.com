<?php

class Genre
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM genres ORDER BY genre_name";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name)
    {
        $query = "INSERT INTO genres (genre_name) VALUES (:name)";
        $response = $this->conn->prepare($query);
        $response->bindParam(':name', $name);
        return $response->execute() ? $this->conn->lastInsertId() : false;
    }
}
