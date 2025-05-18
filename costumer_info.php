<?php
require_once "includes/connect.inc.php";
// Handle delete customer
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $customerID = $_GET['delete'];
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // First delete related order details
        $stmt = $conn->prepare("DELETE od FROM OrderDetails od
                               INNER JOIN `Order` o ON od.OrderID = o.OrderID
                               WHERE o.CustomerID = :CustomerID");
        $stmt->bindParam(':CustomerID', $customerID);
        $stmt->execute();
        
        // Then delete orders
        $stmt = $conn->prepare("DELETE FROM `Order` WHERE CustomerID = :CustomerID");
        $stmt->bindParam(':CustomerID', $customerID);
        $stmt->execute();
        
        // Finally delete the customer
        $stmt = $conn->prepare("DELETE FROM Customer WHERE CustomerID = :CustomerID");
        $stmt->bindParam(':CustomerID', $customerID);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        echo '<script>alert("Customer and all related data deleted successfully");</script>';
        echo '<script>window.location.href = "costumer_info.php";</script>';
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo '<script>alert("Error deleting customer: ' . $e->getMessage() . '");</script>';
    }
}

// Get all customers
try {
    $stmt = $conn->prepare("SELECT * FROM Customer ORDER BY CustomerID DESC");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<script>alert("Error fetching customers: ' . $e->getMessage() . '");</script>';
    $customers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Customer Information</title>
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
        
        .customers-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .customers-table th, .customers-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .customers-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .customers-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        
        .no-customers {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 30px;
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
            <h1>Customer Information</h1>
        </div>
        
        <?php if (count($customers) > 0): ?>
        <table class="customers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo $customer['CustomerID']; ?></td>
                    <td><?php echo htmlspecialchars($customer['FirstName'] . ' ' . $customer['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($customer['Email']); ?></td>
                    <td><?php echo htmlspecialchars($customer['Phone']); ?></td>
                    <td><?php echo htmlspecialchars($customer['Address']); ?></td>
                    <td>
                        <a href="costumer_info.php?delete=<?php echo $customer['CustomerID']; ?>" class="delete-btn" onclick="return confirm('Warning: This will delete the customer and all their orders. Are you sure you want to proceed?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-customers">
            <p>No customers found in the database.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>