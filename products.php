<?php
require_once "includes/connect.inc.php";
@$ProductID=$_GET["ProductID"];
if (isset($ProductID)) {
    $stmt = $conn->prepare("DELETE FROM Product WHERE ProductID = :ProductID");
    $stmt->bindParam(':ProductID', $ProductID);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
    echo '<script> alert("Product deleted");</script>';
    } else {
    echo '<script> alert("Product deleted faild");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porducts Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/products.css">
</head>
<body>
    <?php
    ?>
    <div class="sidbar_container">
    <div class="sidebar">
        <h1>Products Page</h1>
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
    <div class="content_cat">
        <div class="table-container">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php
                     $stmt = $conn->prepare("SELECT * FROM Product");
                    $stmt->execute();                
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($result as $row){
                    ?>
                        <td data-label="ID"><?php echo htmlspecialchars($row['ProductID']);?></td>
                        <td data-label="Product"><?php echo htmlspecialchars($row['ProductName']); ?></td>
                        <td data-label="Image"><img src="<?php echo htmlspecialchars($row['ImageURL']); ?>" alt="Product Image"></td>
                        <td data-label="Price"><?php echo htmlspecialchars($row['ProductPrice']); ?></td>
                        <td data-label="Description"><?php echo htmlspecialchars($row['ProductDesc']); ?></td>
                        <td data-label="Qty"><?php echo htmlspecialchars($row['ProductQ']); ?></td>
                        <?php
                        $stmt = $conn->prepare("SELECT CategoryName FROM Category WHERE CategoryID=:CategoryID");
                        $stmt->bindParam(":CategoryID",$row["CategoryID"]);
                        $stmt->execute();                
                        $res = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <td data-label="Category"><?php echo htmlspecialchars($res['CategoryName']); ?></td>
                        <td data-label="Actions" class="action-btns">
                            <a href="update_product.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>"><button class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</button></a>
                            <a href="products.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>"><button class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</button></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
    
</body>
</html>