<?php
// These must be the very first lines
ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../config.php';

try {
    // 1. Today's Sales
    $todaySales = $pdo->query(
        "SELECT IFNULL(SUM(p.price * ci.quantity), 0) as total 
         FROM cart_items ci
         JOIN products p ON ci.product_id = p.id
         JOIN carts c ON ci.cart_id = c.id
         WHERE DATE(c.created_at) = CURDATE()"
    )->fetchColumn();

    // 2. New Orders (Today)
    $newOrders = $pdo->query(
        "SELECT COUNT(*) FROM carts WHERE DATE(created_at) = CURDATE()"
    )->fetchColumn();

    // 3. Total Products
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    // 4. Recent Orders (Last 5)
    $recentOrders = $pdo->query(
        "SELECT c.id, u.username, c.created_at, 
                SUM(p.price * ci.quantity) as total
         FROM carts c
         LEFT JOIN users u ON c.user_id = u.id
         LEFT JOIN cart_items ci ON c.id = ci.cart_id
         LEFT JOIN products p ON ci.product_id = p.id
         GROUP BY c.id
         ORDER BY c.created_at DESC
         LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);

    // 5. Sales Analytics (Last 7 Days)
    $salesAnalytics = $pdo->query(
        "SELECT DATE(c.created_at) as date, 
                COUNT(c.id) as order_count,
                IFNULL(SUM(p.price * ci.quantity), 0) as revenue
         FROM carts c
         LEFT JOIN cart_items ci ON c.id = ci.cart_id
         LEFT JOIN products p ON ci.product_id = p.id
         WHERE c.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
         GROUP BY DATE(c.created_at)
         ORDER BY date ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    // 6. Recent Products (Last 5 Added)
    $recentProducts = $pdo->query(
        "SELECT * FROM products ORDER BY created_at DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);

    // Validate data before output
    if (!is_numeric($todaySales) || !is_numeric($newOrders)) {
        throw new RuntimeException('Invalid data from database');
    }

    // Clean output buffer and send JSON
    ob_end_clean();
    http_response_code(200);
    $recentOrders = array_map(function($order) {
    $order['total'] = (float)($order['total'] ?? 0);
    return $order;
}, $recentOrders);
    echo json_encode([
        'success' => true,
        'todaySales' => (float)$todaySales,
        'newOrders' => (int)$newOrders,
        'totalProducts' => (int)$totalProducts,
        'recentOrders' => $recentOrders,
        'salesAnalytics' => $salesAnalytics,
        'recentProducts' => $recentProducts
    ]);
    exit();

} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'debug' => (ini_get('display_errors') ? $e->getMessage() : null)
    ]);
    exit();
}
?>