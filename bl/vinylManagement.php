<?php

require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/vinylModel.php';
require_once __DIR__ . '/../model/artistModel.php';
require_once __DIR__ . '/../model/genreModel.php';
require_once __DIR__ . '/../model/orderModel.php';

class VinylManagement
{
    private $vinylModel;
    private $artistModel;
    private $genreModel;
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->vinylModel  = new VinylRecord($db);
        $this->artistModel = new Artist($db);
        $this->genreModel  = new Genre($db);
        $this->orderModel  = new Order($db);
    }

    public function getAllVinyls()  { return $this->vinylModel->getAllVinyls(); }
    public function getVinyl($id)   { return $this->vinylModel->getVinylById($id); }
    public function countVinyls()   { return $this->vinylModel->countVinyls(); }
    public function getArtists()    { return $this->artistModel->getAll(); }
    public function getGenres()     { return $this->genreModel->getAll(); }
    public function countOrders()   { return $this->orderModel->countOrders(); }
    public function totalRevenue()  { return $this->orderModel->totalRevenue(); }
    public function getRecentOrders($n = 5) { return $this->orderModel->getRecent($n); }
    public function getRevenueByMonth($months = 6) { return $this->orderModel->getRevenueByMonth($months); }
    public function getOrderStatusDistribution() { return $this->orderModel->getOrderStatusDistribution(); }
    public function getOrdersByMonth($months = 6) { return $this->orderModel->getOrdersByMonth($months); }
    public function getChartData($period = '6') { return $this->orderModel->getChartData($period); }

    public function addVinyl($title, $artist_id, $genre_id, $price, $stock, $year, $desc, $cover_image = null, $vinyl_image = null)
    {
        try {
            echo $this->vinylModel->createVinyl($title, $artist_id, $genre_id, $price, $stock, $year, $desc, $cover_image, $vinyl_image) ? 'true' : 'false';
        } catch (Exception $e) {
            http_response_code(500); echo $e->getMessage(); exit;
        }
    }

    public function editVinyl($id, $title, $artist_id, $genre_id, $price, $stock, $year, $desc, $cover_image = null, $vinyl_image = null)
    {
        try {
            echo $this->vinylModel->updateVinyl($id, $title, $artist_id, $genre_id, $price, $stock, $year, $desc, $cover_image, $vinyl_image) ? 'true' : 'false';
        } catch (Exception $e) {
            http_response_code(500); echo $e->getMessage(); exit;
        }
    }

    public function removeVinyl($id)
    {
        try {
            echo $this->vinylModel->deleteVinyl($id) ? 'true' : 'false';
        } catch (Exception $e) {
            http_response_code(500); echo $e->getMessage(); exit;
        }
    }
}
