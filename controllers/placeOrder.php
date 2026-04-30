<?php
session_start();
header('Content-Type: application/json');

require_once '../model/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input      = json_decode(file_get_contents('php://input'), true);
$vinyl_id   = isset($input['vinyl_id'])   ? (int)$input['vinyl_id']   : 0;
$total      = isset($input['total'])      ? (float)$input['total']     : 0;
$userID     = isset($_SESSION['userID'])  ? (int)$_SESSION['userID']  : null;

if (!$vinyl_id || $total <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();

    // Check stock
    $stmt = $db->prepare("SELECT stock FROM vinyl_records WHERE vinyl_id = :id");
    $stmt->execute([':id' => $vinyl_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['stock'] < 1) {
        echo json_encode(['success' => false, 'message' => 'Out of stock']);
        exit;
    }

    // Insert order
    $stmt = $db->prepare("
        INSERT INTO orders (userID, total_amount, status, order_date)
        VALUES (:userID, :total, 'Pending', NOW())
    ");
    $stmt->execute([':userID' => $userID, ':total' => $total]);
    $order_id = $db->lastInsertId();

    // Decrement stock
    $db->prepare("UPDATE vinyl_records SET stock = stock - 1 WHERE vinyl_id = :id")
       ->execute([':id' => $vinyl_id]);

    // Return fresh chart data for live update
    $rev = $db->prepare("
        SELECT DATE_FORMAT(order_date, '%Y-%m-01') as month, SUM(total_amount) as revenue
        FROM orders WHERE status != 'Cancelled'
        AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(order_date, '%Y-%m-01') ORDER BY month ASC
    ");
    $rev->execute();
    $revRows = $rev->fetchAll(PDO::FETCH_ASSOC);

    $ord = $db->prepare("
        SELECT DATE_FORMAT(order_date, '%Y-%m-01') as month, COUNT(*) as count
        FROM orders
        WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(order_date, '%Y-%m-01') ORDER BY month ASC
    ");
    $ord->execute();
    $ordRows = $ord->fetchAll(PDO::FETCH_ASSOC);

    $status = $db->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $status->execute();
    $statusRows = $status->fetchAll(PDO::FETCH_ASSOC);

    $totalRevenue = $db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
    $totalOrders  = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    echo json_encode([
        'success'      => true,
        'order_id'     => $order_id,
        'totalRevenue' => (float)$totalRevenue,
        'totalOrders'  => (int)$totalOrders,
        'revenueRows'  => $revRows,
        'orderRows'    => $ordRows,
        'statusRows'   => $statusRows,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
