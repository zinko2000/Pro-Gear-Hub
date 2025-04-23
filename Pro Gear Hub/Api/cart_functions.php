<?php
require_once '../config.php';

function addToCart($productId, $quantity = 1) {
    if (isset($_SESSION['user_id'])) {
        return addToDatabaseCart($_SESSION['user_id'], $productId, $quantity);
    } else {
        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;
        return true;
    }
}

function getCartCount() {
    if (isset($_SESSION['user_id'])) {
        return getDatabaseCartCount($_SESSION['user_id']);
    } else {
        return array_sum($_SESSION['cart']);
    }
}

// ... [include the other functions from previous example]
?>