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
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function totalRevenue()
    {
        $stmt = $this->conn->prepare("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'");
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    public function getRecent($limit = 5)
    {
        $query = "SELECT o.*, u.firstName, u.lastName, u.email
                  FROM orders o
                  LEFT JOIN users_tbl u ON o.userID = u.userID
                  ORDER BY o.order_date DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByMonth($months = 6)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                DATE_FORMAT(order_date, '%Y-%m-01') as month,
                SUM(total_amount) as revenue
            FROM orders 
            WHERE status != 'Cancelled'
            AND order_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(order_date, '%Y-%m-01')
            ORDER BY month ASC
        ");
        $stmt->bindParam(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderStatusDistribution()
    {
        $stmt = $this->conn->prepare("
            SELECT status, COUNT(*) as count
            FROM orders
            GROUP BY status
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByMonth($months = 6)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                DATE_FORMAT(order_date, '%Y-%m-01') as month,
                COUNT(*) as count
            FROM orders
            WHERE order_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(order_date, '%Y-%m-01')
            ORDER BY month ASC
        ");
        $stmt->bindParam(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
