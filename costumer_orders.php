<?php
require_once "includes/connect.inc.php";


// Get all orders with customer information
try {
    $stmt = $conn->prepare("SELECT o.*, 
                          c.FirstName, c.LastName, c.Email, 
                          COUNT(od.OrderDetailID) as TotalItems 
                          FROM `Order` o
                          LEFT JOIN Customer c ON o.CustomerID = c.CustomerID
                          LEFT JOIN OrderDetails od ON o.OrderID = od.OrderID
                          GROUP BY o.OrderID
                          ORDER BY o.OrderDate DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<script>alert("Error fetching orders: ' . $e->getMessage() . '");</script>';
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Customer Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
        }
        
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            position: fixed;
        }
        
        .sidebar h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }
        
        .sidebar ul {
            list-style-type: none;
        }
        
        .sidebar li {
            margin-bottom: 15px;
        }
        
        .sidebar a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #3498db;
        }
        
        .sidebar i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .orders-table th, .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .orders-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .orders-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .view-details-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .view-details-btn:hover {
            background-color: #45a049;
        }
        
        .no-orders {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 30px;
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
        
        .status-badge.completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>Control Panel</h1>
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> Home Page</a></li>
            <li><a href="control_panel.php"><i class="fas fa-layer-group"></i> Manage Category</a></li>
            <li><a href="products.php"><i class="fa-brands fa-product-hunt"></i>Products Page</a></li>
            <li><a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
            <li><a href="costumer_info.php"><i class="fas fa-users"></i> Customers Info</a></li>
            <li><a href="costumer_orders.php"><i class="fas fa-shopping-cart"></i> Customers Orders</a></li>
            <li><a href="includes/logout.inc.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Customer Orders</h1>
        </div>
        
        <?php if (count($orders) > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Items</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['OrderID']; ?></td>
                    <td><?php echo htmlspecialchars($order['FirstName'] . ' ' . $order['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($order['Email']); ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($order['OrderDate'])); ?></td>
                    <td>$<?php echo number_format($order['TotalAmount'], 2); ?></td>
                    <td><?php echo $order['TotalItems']; ?> item(s)</td>
                    <td>
                        <button class="view-details-btn" onclick="toggleOrderDetails(<?php echo $order['OrderID']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="order-details" id="order-details-<?php echo $order['OrderID']; ?>">
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
            <p>No orders found in the database.</p>
        </div>
        <?php endif; ?>
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