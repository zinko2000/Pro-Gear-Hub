<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['product_id'] ?? null;
    
    if (!$productId) {
        throw new Exception('Product ID required');
    }
    
    if (isset($_SESSION['user_id'])) {
        // Remove from database
        $stmt = $pdo->prepare("
            DELETE ci FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = :user_id AND ci.product_id = :product_id
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':product_id' => $productId
        ]);
        
        // Check if cart is now empty
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $count = $stmt->fetch()['count'];
        
        if ($count == 0) {
            // Optional: Delete the empty cart
        }
    } elseif (isset($_SESSION['cart'][$productId])) {
        // Remove from session
        unset($_SESSION['cart'][$productId]);
        
        // Check if cart is empty
        if (empty($_SESSION['cart'])) {
            // Optional: Clean up completely
            unset($_SESSION['cart']);
        }
    }
    
    // Return updated cart
    require 'get_cart_items.php';
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>