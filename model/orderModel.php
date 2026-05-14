<?php

class Order
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function countOrders()
    {
        $query = "SELECT COUNT(*) FROM orders";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchColumn();
    }

    public function totalRevenue()
    {
        $query = "SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchColumn() ?? 0;
    }

    public function getRecent($limit = 5)
    {
        $query = "SELECT o.*, u.firstName, u.lastName, u.email
                  FROM orders o
                  LEFT JOIN users_tbl u ON o.userID = u.userID
                  ORDER BY o.order_date DESC LIMIT :limit";
        $response = $this->conn->prepare($query);
        $response->bindParam(':limit', $limit, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByMonth($months = 6)
    {
        $query = "SELECT 
    DATE_FORMAT(order_date, '%Y-%m-01') as month,
    SUM(total_amount) as revenue
FROM orders 
WHERE status != 'Cancelled'
AND order_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)
GROUP BY DATE_FORMAT(order_date, '%Y-%m-01')
ORDER BY month ASC";
        $response = $this->conn->prepare($query);
        $response->bindParam(':months', $months, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderStatusDistribution()
    {
        $query = "SELECT status, COUNT(*) as count
FROM orders
GROUP BY status";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByMonth($months = 6)
    {
        $query = "SELECT 
    DATE_FORMAT(order_date, '%Y-%m-01') as month,
    COUNT(*) as count
FROM orders
WHERE order_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)
GROUP BY DATE_FORMAT(order_date, '%Y-%m-01')
ORDER BY month ASC";
        $response = $this->conn->prepare($query);
        $response->bindParam(':months', $months, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }
}
