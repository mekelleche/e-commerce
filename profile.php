<?php
session_start();
require_once "includes/connect.inc.php";

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo '<script>alert("Please login to view your profile");</script>';
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

$CustomerID = $_SESSION['customer_id'];
$CustomerName = $_SESSION['customer_name'] ?? 'Customer';

// Get customer details
try {
    $stmt = $conn->prepare("SELECT * FROM Customer WHERE CustomerID = :CustomerID");
    $stmt->bindParam(':CustomerID', $CustomerID);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<script>alert("Error fetching customer details: ' . $e->getMessage() . '");</script>';
}

// Get order history
try {
    $stmt = $conn->prepare("SELECT o.*, COUNT(od.OrderDetailID) as TotalItems 
                           FROM `Order` o
                           LEFT JOIN OrderDetails od ON o.OrderID = od.OrderID
                           WHERE o.CustomerID = :CustomerID
                           GROUP BY o.OrderID
                           ORDER BY o.OrderDate DESC");
    $stmt->bindParam(':CustomerID', $CustomerID);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<script>alert("Error fetching order history: ' . $e->getMessage() . '");</script>';
    $orders = [];
}

include("file/header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }
        
        .profile-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 20px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background-color: #d32f2f;
        }
        
        .section-title {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .order-history {
            margin-top: 30px;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .order-table th, .order-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        .order-table th {
            background-color: #f8f9fa;
        }
        
        .order-row:hover {
            background-color: #f5f5f5;
        }
        
        .view-details-btn {
            background-color: #4CAF50;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .view-details-btn:hover {
            background-color: #45a049;
        }
        
        .order-details {
            margin-top: 15px;
            border: 1px solid #ddd;
            padding: 15px;
            display: none;
        }
        
        .order-details.active {
            display: block;
        }
        
        .order-details-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .order-details-items th, .order-details-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .no-orders {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="images/profile.jpg" alt="Profile Image" class="profile-image">
            <div>
                <h1><?php echo htmlspecialchars($CustomerName); ?>'s Profile</h1>
                <?php if (isset($customer)): ?>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['Email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['Phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-actions">
            <h2>My Account</h2>
            <a href="includes/logout.inc.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="order-history">
            <h3 class="section-title">Order History</h3>
            
            <?php if (count($orders) > 0): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Items</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr class="order-row">
                        <td>#<?php echo $order['OrderID']; ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($order['OrderDate'])); ?></td>
                        <td>$<?php echo number_format($order['TotalAmount'], 2); ?></td>
                        <td><?php echo $order['TotalItems']; ?> item(s)</td>
                        <td>
                            <button class="view-details-btn" onclick="toggleOrderDetails(<?php echo $order['OrderID']; ?>)">
                                View Details
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="order-details" id="order-details-<?php echo $order['OrderID']; ?>">
                            <h4>Order #<?php echo $order['OrderID']; ?> Details</h4>
                            
                            <?php
                            // Get order details
                            $stmt = $conn->prepare("SELECT od.*, p.ProductName 
                                                  FROM OrderDetails od
                                                  LEFT JOIN Product p ON od.ProductID = p.ProductID
                                                  WHERE od.OrderID = :OrderID");
                            $stmt->bindParam(':OrderID', $order['OrderID']);
                            $stmt->execute();
                            $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            
                            <table class="order-details-items">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderDetails as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['ProductName'] ?? 'Product #'.$item['ProductID']); ?></td>
                                        <td><?php echo $item['Quantity']; ?></td>
                                        <td>$<?php echo number_format($item['UnitPrice'], 2); ?></td>
                                        <td>$<?php echo number_format($item['Subtotal'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                        <td><strong>$<?php echo number_format($order['TotalAmount'], 2); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="home.php">Start shopping</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function toggleOrderDetails(orderId) {
            const detailsElement = document.getElementById('order-details-' + orderId);
            if (detailsElement.classList.contains('active')) {
                detailsElement.classList.remove('active');
            } else {
                // Hide all other active details first
                const allDetails = document.querySelectorAll('.order-details.active');
                allDetails.forEach(detail => {
                    detail.classList.remove('active');
                });
                
                // Show the selected details
                detailsElement.classList.add('active');
            }
        }
    </script>
</body>
</html>

<?php include("file/footer.php"); ?>