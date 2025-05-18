<?php
require_once "includes/connect.inc.php";
@$CategoryID=$_GET["CategoryID"];
if($_SERVER["REQUEST_METHOD"]=="POST"){
$CategoryName=$_POST["CategoryName"];
$catadd=$_POST["catadd"];
if(isset($CategoryName)){
    if (empty($CategoryName)) {
        echo '<script> alert("Enter the name of the category");</script>';
    }else{
        $stmt = $conn->prepare("INSERT INTO Category (CategoryName) VALUES (:CategoryName)");
        $stmt->bindParam(':CategoryName', $CategoryName);
        $stmt->execute();
         echo '<script> alert("Category has been added succefuly");</script>';
    }
}
}
if (isset($CategoryID)) {
    $stmt = $conn->prepare("DELETE FROM Category WHERE CategoryID = :CategoryID");
        $stmt->bindParam(':CategoryID', $CategoryID);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
        echo '<script> alert("category deleted");</script>';
        } else {
        echo '<script> alert("category deleted faild");</script>';
        }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/control_panal.css">
</head>
<body>
    <?php
    ?>
    <div class="sidbar_container">
        <div class="sidebar" >
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
        <div class="content_cat">
            <form action="control_panel.php" method="post">
                <label for="category">add new category</label>
                <input type="text" name="CategoryName" id="category">
                <br>
                <button class="add" type="submit" name="catadd">Add Category</button>
            </form>
            <br>
            <table>
                <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
                    <?php
                     $stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM Category");
                    $stmt->execute();                
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($result as $row){
                    ?>
                    <td><?php echo htmlspecialchars($row['CategoryID']); ?></td>
                    <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                    <td><a href="control_panel.php?CategoryID=<?php echo htmlspecialchars($row['CategoryID']); ?>"><button type="submit" class="delete">delete</button></a></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>

    </div>
    
</body>
</html>