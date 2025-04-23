<?php
require_once '../config.php';

// Set headers first to prevent any output before JSON
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Disable error display in production (enable during development)
error_reporting(0);
ini_set('display_errors', 0);

// Validate and sanitize inputs
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : null;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : null;
$sort = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'featured';

// Allowed sort values for security
$allowedSorts = ['featured', 'price-low', 'price-high', 'rating', 'newest'];
$sort = in_array($sort, $allowedSorts) ? $sort : 'featured';

try {
    // Basic query
    $sql = "SELECT id, name, description, price, original_price, category, 
                   rating, review_count, image_path, is_new 
            FROM products WHERE 1=1";
    $params = [];

    // Add category filter if provided
    if ($category && $category !== 'all') {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }

    // Add price filter if provided
    if ($maxPrice && $maxPrice > 0) {
        $sql .= " AND price <= :maxPrice";
        $params[':maxPrice'] = $maxPrice;
    }

    // Apply sorting
    switch ($sort) {
        case 'price-low': 
            $sql .= " ORDER BY price ASC"; 
            break;
        case 'price-high': 
            $sql .= " ORDER BY price DESC"; 
            break;
        case 'rating': 
            $sql .= " ORDER BY rating DESC"; 
            break;
        case 'newest': 
            $sql .= " ORDER BY created_at DESC"; 
            break;
        default: 
            $sql .= " ORDER BY is_new DESC, rating DESC";
    }

    // Prepare and execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format response
    $response = [
        'success' => true,
        'data' => $products,
        'meta' => [
            'count' => count($products),
            'filters' => [
                'category' => $category,
                'maxPrice' => $maxPrice,
                'sort' => $sort
            ]
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => 'Could not retrieve products'
    ]);
    error_log('Product API Error: ' . $e->getMessage());
    
} catch (Exception $e) {
    // General error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => 'An unexpected error occurred'
    ]);
    error_log('General Error in product.php: ' . $e->getMessage());
}

file_put_contents('debug.log', json_encode($response));
?>