<?php
require_once '../config.php';

// Set headers first to prevent any output before JSON
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $cartCount = 0;
    
    // Check if user is logged in (database cart)
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        // Get the user's active cart
        $stmt = $pdo->prepare("
            SELECT SUM(ci.quantity) as total 
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            WHERE c.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartCount = (int)($result['total'] ?? 0);
        
    } 
    // Guest user (session cart)
    elseif (isset($_SESSION['cart'])) {
        $cartCount = array_sum($_SESSION['cart']);
    }
    
    // Successful response
    echo json_encode([
        'success' => true,
        'count' => $cartCount
    ]);
    
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'database_error',
        'message' => 'Could not retrieve cart count'
    ]);
    error_log('Cart Count Error: ' . $e->getMessage());
    
} catch (Exception $e) {
    // General error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'server_error',
        'message' => 'An unexpected error occurred'
    ]);
    error_log('General Error in get_cart_count.php: ' . $e->getMessage());
}
?>