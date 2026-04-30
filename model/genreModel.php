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
        $stmt = $this->conn->prepare("SELECT * FROM genres ORDER BY genre_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name)
    {
        $stmt = $this->conn->prepare("INSERT INTO genres (genre_name) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        return $stmt->execute() ? $this->conn->lastInsertId() : false;
    }
}
