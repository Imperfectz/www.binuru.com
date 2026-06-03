<?php

class VinylRecord
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllVinyls()
    {
        $query = "SELECT v.*, a.artist_name, g.genre_name
                  FROM vinyl_records_tbl v
                  LEFT JOIN artists_tbl a ON v.artist_id = a.artist_id
                  LEFT JOIN genres_tbl g ON v.genre_id = g.genre_id
                  ORDER BY v.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVinylById($id)
    {
        $query = "SELECT v.*, a.artist_name, g.genre_name
                  FROM vinyl_records_tbl v
                  LEFT JOIN artists_tbl a ON v.artist_id = a.artist_id
                  LEFT JOIN genres_tbl g ON v.genre_id = g.genre_id
                  WHERE v.vinyl_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countVinyls()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM vinyl_records_tbl");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function createVinyl($title, $artist_id, $genre_id, $price, $stock, $release_year, $description, $cover_image = null, $vinyl_image = null)
    {
        $query = "INSERT INTO vinyl_records_tbl (title, artist_id, genre_id, price, stock, release_year, description, cover_image, vinyl_image)
                  VALUES (:title, :artist_id, :genre_id, :price, :stock, :release_year, :description, :cover_image, :vinyl_image)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':title',        $title,        PDO::PARAM_STR);
        $stmt->bindValue(':artist_id',    $artist_id,    $artist_id    === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':genre_id',     $genre_id,     $genre_id     === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':price',        $price,        PDO::PARAM_STR);
        $stmt->bindValue(':stock',        $stock,        PDO::PARAM_INT);
        $stmt->bindValue(':release_year', $release_year, $release_year === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':description',  $description,  PDO::PARAM_STR);
        $stmt->bindValue(':cover_image',  $cover_image,  $cover_image  === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':vinyl_image',  $vinyl_image,  $vinyl_image  === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateVinyl($id, $title, $artist_id, $genre_id, $price, $stock, $release_year, $description, $cover_image = null, $vinyl_image = null)
    {
        $setClauses = [
            'title=:title',
            'artist_id=:artist_id',
            'genre_id=:genre_id',
            'price=:price',
            'stock=:stock',
            'release_year=:release_year',
            'description=:description'
        ];
        if ($cover_image !== null) $setClauses[] = 'cover_image=:cover_image';
        if ($vinyl_image  !== null) $setClauses[] = 'vinyl_image=:vinyl_image';

        $query = "UPDATE vinyl_records_tbl SET " . implode(', ', $setClauses) . " WHERE vinyl_id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id',           $id,           PDO::PARAM_INT);
        $stmt->bindValue(':title',        $title,        PDO::PARAM_STR);
        $stmt->bindValue(':artist_id',    $artist_id,    $artist_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':genre_id',     $genre_id,     $genre_id  === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':price',        $price,        PDO::PARAM_STR);
        $stmt->bindValue(':stock',        $stock,        PDO::PARAM_INT);
        $stmt->bindValue(':release_year', $release_year, $release_year === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':description',  $description,  PDO::PARAM_STR);
        if ($cover_image !== null) $stmt->bindValue(':cover_image', $cover_image, PDO::PARAM_STR);
        if ($vinyl_image  !== null) $stmt->bindValue(':vinyl_image', $vinyl_image, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteVinyl($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM vinyl_records_tbl WHERE vinyl_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
