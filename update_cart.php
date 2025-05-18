<?php
require_once "includes/cart_functions.inc.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        updateCartItem($productId, $quantity);
    }
    
    header("Location: cart.php");
    exit();
}
?>