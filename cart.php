<?php
session_start();
$CostumerName = $_SESSION['customer_name'];
require_once "includes/connect.inc.php";
include("file/header.php");

// Handle adding items to cart
$add = $_POST["add"] ?? null;
if (isset($add)) {
    $ProductID = $_POST["ProductID"] ?? ''; 
    $ProductName = $_POST["h_name"] ?? '';
    $ProductPrice = $_POST["h_price"] ?? '';
    $ImageURL = $_POST["h_img"] ?? '';
    $ProductQ = $_POST["q"] ?? 1;

    $stmt = $conn->prepare("SELECT * FROM Cart WHERE CartName = :CartName");
    $stmt->bindParam(":CartName", $ProductName);
    $stmt->execute();                
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($stmt->rowCount() > 0) {
        echo '<script>alert("Product already exists in cart");</script>';
    } else {
        $stmt = $conn->prepare("INSERT INTO Cart (CartName, CartPrice, CartImg, CartQ) VALUES (:CartName, :CartPrice, :CartImg, :CartQ)");
        $stmt->bindParam(':CartName', $ProductName);
        $stmt->bindParam(':CartPrice', $ProductPrice);
        $stmt->bindParam(':CartImg', $ImageURL);
        $stmt->bindParam(':CartQ', $ProductQ);
        $stmt->execute();
        echo '<script>alert("Product has been added to cart");</script>';
    }
}

// Handle quantity updates
if (isset($_POST['update'])) {
    $cartId = $_POST['cart_id'];
    $newQty = $_POST['new_qty'];
    
    $stmt = $conn->prepare("UPDATE Cart SET CartQ = :qty WHERE CartID = :id");
    $stmt->bindParam(':qty', $newQty);
    $stmt->bindParam(':id', $cartId);
    $stmt->execute();
    echo '<script>alert("Quantity updated");</script>';
}

// Handle item removal
if (isset($_GET['remove'])) {
    $cartId = $_GET['remove'];
    
    $stmt = $conn->prepare("DELETE FROM Cart WHERE CartID = :id");
    $stmt->bindParam(':id', $cartId);
    $stmt->execute();
    echo '<script>alert("Item removed from cart");</script>';
    echo '<script>window.location.href = "cart.php";</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.con_head {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.con_head img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
}

.cart_table {
    width: 100%;
    border-collapse: collapse;
}

.cart_table tr {
    border-bottom: 1px solid #ddd;
}

.cart_table td,
.cart_table th {
    padding: 12px;
    text-align: left;
    vertical-align: middle; /* Align all cell contents vertically */
}

.cart_table th {
    background-color: #f2f2f2;
}

.cart_table img {
    max-width: 80px;
    max-height: 80px;
}

.total-row {
    font-weight: bold;
    background-color: #f9f9f9;
}

.checkout-btn {
    display: block;
    width: 200px;
    padding: 10px;
    margin: 20px auto;
    text-align: center;
    background-color: #6A89A7; /* New background color */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.checkout-btn:hover {
    background-color: #5A7894; /* Slightly darker for hover */
}

/* Form styles for quantity update */
.action-form {
    display: inline-block;
    margin: 0;
}

.qty-input {
    width: 50px;
    padding: 5px;
    margin-right: 5px;
    text-align: center;
}

.update-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.update-btn:hover {
    background-color: #45a049;
}

/* Remove button styling */
.remove-btn {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s;
    display: inline-block;
    margin: 0 auto;
    line-height: normal;
    font-size: 14px;
    align-self: center;
}

.remove-btn:hover {
    background-color: #d32f2f;
}

/* Make the action cell a flex container */
.action-btns {
    display: flex;
    align-items: center;   /* Vertical centering */
    justify-content: center; /* Horizontal centering */
    height: 100%;
}
    
    </style>
</head>
<body>
    <div class="container">
        <div class="con_head">
            <img src="images/cart.jpg" alt="Profile Image">
            <h1><?php echo htmlspecialchars($CostumerName); ?>'s Cart</h1>
        </div>
        
        <?php
        $stmt = $conn->prepare("SELECT * FROM Cart");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($result) > 0): ?>
        <table class="cart_table">
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>                        
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
            
            <?php 
            $grandTotal = 0;
            foreach($result as $row): 
                $totalPrice = $row['CartPrice'] * $row['CartQ'];
                $grandTotal += $totalPrice;
            ?>
            <tr>
                <td><img src="<?php echo htmlspecialchars($row['CartImg']); ?>" alt="<?php echo htmlspecialchars($row['CartName']); ?>"></td>
                <td><?php echo htmlspecialchars($row['CartName']); ?></td>
                <td>
                    <form method="post" class="action-form">
                        <input type="hidden" name="cart_id" value="<?php echo $row['CartID']; ?>">
                        <input type="number" name="new_qty" value="<?php echo htmlspecialchars($row['CartQ']); ?>" min="1" class="qty-input">
                        <button type="submit" name="update" class="update-btn">Update</button>
                    </form>
                </td>
                <td>$<?php echo number_format($row['CartPrice'], 2); ?></td>
                <td>$<?php echo number_format($totalPrice, 2); ?></td>
                <td class="action-btns">
                    <a href="cart.php?remove=<?php echo $row['CartID']; ?>" class="remove-btn" onclick="return confirm('Are you sure you want to remove this item?')">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">Grand Total:</td>
                <td>$<?php echo number_format($grandTotal, 2); ?></td>
                <td></td>
            </tr>
        </table>
        
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        
        <?php else: ?>
        <p>Your cart is empty. <a href="products.php">Continue shopping</a></p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include("file/footer.php"); ?>