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
        $query = "SELECT COUNT(*) FROM orders_tbl";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchColumn();
    }

    public function totalRevenue()
    {
        $query = "SELECT SUM(total_amount) FROM orders_tbl WHERE status != 'Cancelled'";
        $response = $this->conn->prepare($query);
        $response->execute();
        return $response->fetchColumn() ?? 0;
    }

    public function getRecent($limit = 5)
    {
        $query = "SELECT o.*, u.firstName, u.lastName, u.email
                  FROM orders_tbl o
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
FROM orders_tbl 
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
FROM orders_tbl
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
FROM orders_tbl
WHERE order_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)
GROUP BY DATE_FORMAT(order_date, '%Y-%m-01')
ORDER BY month ASC";
        $response = $this->conn->prepare($query);
        $response->bindParam(':months', $months, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChartData($period = '6')
    {
        // Build the WHERE date clause
        switch ($period) {
            case '3':
                $dateFilter = "order_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
                $months = 3;
                break;
            case '12':
                $dateFilter = "order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
                $months = 12;
                break;
            case 'ytd':
                $dateFilter = "YEAR(order_date) = YEAR(NOW())";
                $months = (int)date('n');
                break;
            case 'all':
                $dateFilter = "1=1";
                $months = 24;
                break;
            default: // '6'
                $dateFilter = "order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
                $months = 6;
                break;
        }

        $revQ = $this->conn->query(
            "SELECT DATE_FORMAT(order_date,'%Y-%m-01') as month, SUM(total_amount) as revenue
             FROM orders_tbl WHERE status != 'Cancelled' AND $dateFilter
             GROUP BY DATE_FORMAT(order_date,'%Y-%m-01') ORDER BY month ASC"
        );
        $revenueRows = $revQ->fetchAll(PDO::FETCH_ASSOC);

        $ordQ = $this->conn->query(
            "SELECT DATE_FORMAT(order_date,'%Y-%m-01') as month, COUNT(*) as count
             FROM orders_tbl WHERE $dateFilter
             GROUP BY DATE_FORMAT(order_date,'%Y-%m-01') ORDER BY month ASC"
        );
        $orderRows = $ordQ->fetchAll(PDO::FETCH_ASSOC);

        $statusRows = $this->conn->query(
            "SELECT status, COUNT(*) as count FROM orders_tbl GROUP BY status"
        )->fetchAll(PDO::FETCH_ASSOC);

        $labels    = [];
        $revData   = [];
        $ordData   = [];

        $revMap = [];
        foreach ($revenueRows as $r) $revMap[$r['month']] = (float)$r['revenue'];
        $ordMap = [];
        foreach ($orderRows as $r) $ordMap[$r['month']] = (int)$r['count'];

        if ($period === 'all') {
            $allMonths = array_unique(array_merge(array_keys($revMap), array_keys($ordMap)));
            sort($allMonths);
            foreach ($allMonths as $m) {
                $labels[]  = date('M y', strtotime($m));
                $revData[] = $revMap[$m] ?? 0;
                $ordData[] = $ordMap[$m] ?? 0;
            }
        } else {
            for ($i = $months - 1; $i >= 0; $i--) {
                $key = date('Y-m-01', strtotime("-$i months"));
                $labels[]  = date('M', strtotime($key));
                $revData[] = $revMap[$key] ?? 0;
                $ordData[] = $ordMap[$key] ?? 0;
            }
        }

        return [
            'labels'     => $labels,
            'revenue'    => $revData,
            'orders'     => $ordData,
            'statusRows' => $statusRows,
        ];
    }
}
