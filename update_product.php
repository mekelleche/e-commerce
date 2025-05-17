<?php
require_once "includes/connect.inc.php";

// Get product data if ID is provided
$product = null;
if(isset($_GET['ProductID'])) {
    $ProductID = $_GET['ProductID'];
    
    // Fetch product data
    $stmt = $conn->prepare("SELECT * FROM Product WHERE ProductID = :ProductID");
    $stmt->bindParam(':ProductID', $ProductID);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
   
}

// Handle form submission
// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proupdate'])) {
    $productId = $_POST["ProductID"];
    $CategoryID = $_POST["CategoryID"];
    $ProductName = $_POST["ProductName"];
    $ProductDesc = $_POST["ProductDesc"];
    $ProductPrice = $_POST["ProductPrice"];
    $ProductQ = $_POST["ProductQ"];
    
    // Initialize with current image
    
    
    // Check if new image was uploaded
    if(isset($_FILES["ImageURL"]) && $_FILES["ImageURL"]["error"] == UPLOAD_ERR_OK) {
        $targetDir = "images/";
        $fileName = basename($_FILES["ImageURL"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Validate image type
        if(!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo '<script>alert("Only JPG, JPEG, PNG & GIF files are allowed");</script>';
            exit();
        }
        
        // Move uploaded file
    }else{
         $targetFile = $product["ImageURL"];
    }
    
    // Validate required fields
    if(empty($ProductName) || empty($ProductDesc) || empty($ProductPrice) || empty($ProductQ) || empty($CategoryID)) {
        echo '<script>alert("Please fill in all required fields");</script>';
    } else {
        try {
            // Prepare update statement
            $stmt = $conn->prepare("UPDATE Product SET 
                CategoryID = :CategoryID,
                ProductName = :ProductName,
                ProductDesc = :ProductDesc,
                ProductPrice = :ProductPrice,
                ProductQ = :ProductQ,
                ImageURL = :ImageURL
                WHERE ProductID = :ProductID");
            
            $stmt->bindParam(':CategoryID', $CategoryID);
            $stmt->bindParam(':ProductName', $ProductName);
            $stmt->bindParam(':ProductDesc', $ProductDesc);
            $stmt->bindParam(':ProductPrice', $ProductPrice);
            $stmt->bindParam(':ProductQ', $ProductQ);
            $stmt->bindParam(':ImageURL', $targetFile);
            $stmt->bindParam(':ProductID', $productId);
            
            if($stmt->execute()) {
                echo '<script>alert("Product updated successfully!");
                      window.location.href = "products.php";</script>';
            } else {
                echo '<script>alert("Error updating product");</script>';
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
    <title>Update Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .form_product {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .button:hover {
            background-color: #45a049;
        }
        .current-image {
            margin: 10px 0;
        }
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="form_product">
        <h1>Update Product</h1>
        <form action="update_product.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="ProductID" value="<?php echo htmlspecialchars($product['ProductID'] ?? ''); ?>">
            
            <label for="name">Product Name</label>
            <input type="text" name="ProductName" id="name" value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>
            
            <label for="file">Product Image</label>
            <?php if(isset($product['ImageURL']) && !empty($product['ImageURL'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?php echo htmlspecialchars($product['ImageURL']); ?>" alt="Current Product Image">
                </div>
                <p>Upload new image to replace:</p>
            <?php endif; ?>
            <input type="file" name="ImageURL" id="file">
            
            <label for="price">Product price</label>
            <input type="text" name="ProductPrice" id="price" value="<?php echo htmlspecialchars($product['ProductPrice'] ?? ''); ?>" required>
            
            <label for="Descrpition">Product Description</label>
            <input type="text" name="ProductDesc" id="Descrpition" value="<?php echo htmlspecialchars($product['ProductDesc'] ?? ''); ?>" required>
            
            <label for="Quantity">Product Quantity</label>
            <input type="number" name="ProductQ" id="Quantity" value="<?php echo htmlspecialchars($product['ProductQ'] ?? 1); ?>" min="1" max="10" required>
            
            <div>
                <label for="Category">Category</label>
                <select name="CategoryID" id="Category" required>
                    <?php
                    $stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM Category");
                    $stmt->execute();                
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($result as $row):
                        $selected = (isset($product['CategoryID']) && $product['CategoryID'] == $row['CategoryID']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo htmlspecialchars($row['CategoryID']); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($row['CategoryName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <br>
            <button class="button" type="submit" name="proupdate">Update Product</button>  
        </form>
    </div>
</body>
</html>