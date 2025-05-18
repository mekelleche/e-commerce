<?php
session_start();
$CustomerName = $_SESSION['customer_name'] ?? 'Guest';
$CustomerID = $_SESSION['customer_id'] ?? null;

// Redirect if not logged in
if (!$CustomerID) {
    echo '<script>alert("Please login to continue with checkout");</script>';
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

require_once "includes/connect.inc.php";
include("file/header.php");

// Check if cart is empty
$stmt = $conn->prepare("SELECT * FROM Cart");
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($cartItems) == 0) {
    echo '<script>alert("Your cart is empty");</script>';
    echo '<script>window.location.href = "products.php";</script>';
    exit;
}

// Process checkout
if (isset($_POST['place_order'])) {
    // Get form data
    $shippingAddress = $_POST['shipping_address'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? '';
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($cartItems as $item) {
        $totalAmount += $item['CartPrice'] * $item['CartQ'];
    }
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Create order
        $stmt = $conn->prepare("INSERT INTO `Order` (CustomerID, TotalAmount) VALUES (:CustomerID, :TotalAmount)");
        $stmt->bindParam(':CustomerID', $CustomerID);
        $stmt->bindParam(':TotalAmount', $totalAmount);
        $stmt->execute();
        
        // Get the order ID
        $orderID = $conn->lastInsertId();
        
        // Create order details for each cart item
        foreach ($cartItems as $item) {
            // Get the ProductID from Products table based on name
            $stmtProduct = $conn->prepare("SELECT ProductID FROM Product WHERE ProductName = :ProductName");
            $stmtProduct->bindParam(':ProductName', $item['CartName']);
            $stmtProduct->execute();
            $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
            
            $productID = $product['ProductID'] ?? 0; // Fallback if not found
            $quantity = $item['CartQ'];
            $unitPrice = $item['CartPrice'];
            $subtotal = $quantity * $unitPrice;
            
            $stmt = $conn->prepare("INSERT INTO OrderDetails (OrderID, ProductID, Quantity, UnitPrice, Subtotal) 
                                   VALUES (:OrderID, :ProductID, :Quantity, :UnitPrice, :Subtotal)");
            $stmt->bindParam(':OrderID', $orderID);
            $stmt->bindParam(':ProductID', $productID);
            $stmt->bindParam(':Quantity', $quantity);
            $stmt->bindParam(':UnitPrice', $unitPrice);
            $stmt->bindParam(':Subtotal', $subtotal);
            $stmt->execute();
        }
        
        // Clear the cart
        $stmt = $conn->prepare("DELETE FROM Cart");
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to order confirmation page
        echo '<script>alert("Your order has been placed successfully!");</script>';
        echo '<script>window.location.href = "profile.php";</script>';
        exit;
        
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo '<script>alert("Error processing your order: ' . $e->getMessage() . '");</script>';
    }
}

// Calculate the total
$grandTotal = 0;
foreach ($cartItems as $item) {
    $grandTotal += $item['CartPrice'] * $item['CartQ'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .checkout-sections {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .order-review {
            flex: 1;
            min-width: 300px;
        }
        
        .checkout-form {
            flex: 1;
            min-width: 300px;
        }
        
        .cart-summary {
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .place-order-btn {
            background-color: #6A89A7;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .place-order-btn:hover {
            background-color: #5A7894;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout</h1>
        
        <div class="checkout-sections">
            <div class="order-review">
                <h2>Order Review</h2>
                
                <div class="cart-summary">
                    <table>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                        
                        <?php foreach($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['CartName']); ?></td>
                            <td>$<?php echo number_format($item['CartPrice'], 2); ?></td>
                            <td><?php echo $item['CartQ']; ?></td>
                            <td>$<?php echo number_format($item['CartPrice'] * $item['CartQ'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                            <td><strong>$<?php echo number_format($grandTotal, 2); ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="checkout-form">
                <h2>Shipping & Payment Details</h2>
                
                <form method="post" action="">
                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($CustomerName); ?>" required>
                    </div>
                                    
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address:</label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method:</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select a payment method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                        </select>
                    </div>
                    
                    <div id="credit_card_details" style="display: none;">
                        <div class="form-group">
                            <label for="card_number">Card Number:</label>
                            <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
                        </div>
                        
                        <div class="form-group">
                            <label for="card_expiry">Expiry Date:</label>
                            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY">
                        </div>
                        
                        <div class="form-group">
                            <label for="card_cvv">CVV:</label>
                            <input type="text" id="card_cvv" name="card_cvv" placeholder="XXX">
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Show/hide credit card details based on payment method selection
        document.getElementById('payment_method').addEventListener('change', function() {
            const creditCardDetails = document.getElementById('credit_card_details');
            if (this.value === 'credit_card') {
                creditCardDetails.style.display = 'block';
            } else {
                creditCardDetails.style.display = 'none';
            }
        });
    </script>
</body>
</html>

<?php include("file/footer.php"); ?>