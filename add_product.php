<?php
require_once "includes/connect.inc.php";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proadd'])) {
    // Get form data
    $CategoryID = $_POST["CategoryID"];
    $ProductName = $_POST["ProductName"];
    $ProductDesc = $_POST["ProductDesc"];
    $ProductPrice = $_POST["ProductPrice"];
    $ProductQ = $_POST["ProductQ"];

    // Image handling
    $targetDir = "images/"; // Create this directory first
    $fileName = basename($_FILES["ImageURL"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validate required fields
    if (empty($ProductName) || empty($ProductDesc) || empty($ProductPrice) || empty($ProductQ) || empty($CategoryID)) {
        echo '<script>alert("Please fill in all required fields");</script>';
    } 
    // Validate image upload
    elseif (!isset($_FILES["ImageURL"]) || $_FILES["ImageURL"]["error"] != UPLOAD_ERR_OK) {
        echo '<script>alert("Please select a valid image file");</script>';
    }
    // Validate file type
    elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo '<script>alert("Only JPG, JPEG, PNG & GIF files are allowed");</script>';
    }
    else {
        try {
            // Move uploaded file
            
                // Prepare and execute SQL
                $stmt = $conn->prepare("INSERT INTO Product 
                    (CategoryID, ProductName, ProductDesc, ProductPrice, ProductQ, ImageURL) 
                    VALUES (:CategoryID, :ProductName, :ProductDesc, :ProductPrice, :ProductQ, :ImageURL)");
                
                $stmt->bindParam(':CategoryID', $CategoryID);
                $stmt->bindParam(':ProductName', $ProductName);
                $stmt->bindParam(':ProductDesc', $ProductDesc);
                $stmt->bindParam(':ProductPrice', $ProductPrice);
                $stmt->bindParam(':ProductQ', $ProductQ);
                $stmt->bindParam(':ImageURL', $targetFile); // Store the file path
                
                if ($stmt->execute()) {
                    echo '<script>alert("Product added successfully!");
                          window.location.href = "add_product.php";</script>';
                } else {
                    echo '<script>alert("Error adding product");</script>';
                }
             
        } catch(PDOException $e) {
            echo '<script>alert("Database error: ' . $e->getMessage() . '");</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/add_product.css">
</head>
<body>
<div class="sidebar" >
            <h1>Control Panel</h1>
            <ul>
                <li><a href="home.php" target="_blank">Home Page</a></li>
                <li><a href="products.php">Products Page</a></li>
                <li><a href="control_panel.php">Manage Category</a></li>
                <li><a href="">Costumers Info</a></li>
                <li><a href="">Costumers Orders</a></li>
                <li><a href="includes/logout.inc.php"><i class="fa-solid fa-right-from-bracket"></i> &nbsp;Logout</a></li>
            </ul>
        </div>
    <main>
        <div class="form_product">
            <h1>Add a Product</h1>
            <form action="add_product.php" method="post" enctype="multipart/form-data">
            <label for="name">Product Name</label>
            <input type="text" name="ProductName" id="name">
            <label for="file">Product Image</label>
            <input type="file" name="ImageURL" id="file">
            <label for="price">Product price</label>
            <input type="text" name="ProductPrice" id="price">
            <label for="Descrpition">Product Descrpition</label>
            <input type="text" name="ProductDesc" id="Descrpition">
            <label for="Quantity">Product Quantity</label>
            <input type="number" name="ProductQ" id="Quantity" value="1" min="1" max="10">
        <div>
            <label for="Category">Category</label>
            <select name="CategoryID" id="Category">
            <?php
            $stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM Category");
                    $stmt->execute();                
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($result as $row){
            ?>
            
            <option value="<?php echo htmlspecialchars($row['CategoryID']); ?>"><?php echo htmlspecialchars($row['CategoryName']); ?></option>
            <?php
                }
                ?>
                </select>
        </div><br>
        <button class="button" type="submit" name="proadd">Add Product</button>  
        </form>
        </div>
    </main>
</body>
</html>