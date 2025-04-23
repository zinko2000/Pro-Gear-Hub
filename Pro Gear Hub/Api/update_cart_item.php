<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;

    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    // Update quantity in database or session
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("
            UPDATE cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            SET ci.quantity = :quantity
            WHERE c.user_id = :user_id AND ci.product_id = :product_id
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':product_id' => $productId,
            ':quantity' => $quantity
        ]);
    } elseif (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = $quantity;
    }

    // Return updated cart
    require 'get_cart_items.php';
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>