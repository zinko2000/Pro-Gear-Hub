<?php
// Enable error logging but don't display to users
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Ensure JSON output
header('Content-Type: application/json');
require_once '../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $items = [];
    
    if (isset($_SESSION['user_id'])) {
        error_log("Fetching cart for user: " . $_SESSION['user_id']);
        $stmt = $pdo->prepare("
            SELECT ci.id, p.name, CAST(p.price AS DECIMAL(10,2)) as price, 
                   ci.quantity, p.image_path as image
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_SESSION['cart'])) {
        error_log("Fetching guest cart items");
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $stmt = $pdo->prepare("
                SELECT id, name, CAST(price AS DECIMAL(10,2)) as price, image_path as image 
                FROM products 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $items[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image']
                ];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
    
} catch (Exception $e) {
    // Log the full error
    error_log("Cart Error: " . $e->getMessage());
    
    // Return JSON error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred',
        'message' => 'Could not load cart items'
    ]);
    exit;
}