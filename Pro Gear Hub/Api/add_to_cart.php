<?php
require_once '../config.php';
require_once 'cart_functions.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$productId = (int)($data['product_id'] ?? 0);
$quantity = (int)($data['quantity'] ?? 1);

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Verify product exists
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$productId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

if (addToCart($productId, $quantity)) {
    echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
}
?>