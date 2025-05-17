<?php
require_once "includes/connect.inc.php";
include("file/header.php");

if(isset($_GET['ProductID'])) {
    $ProductID = $_GET['ProductID'];
    
    // Fetch product data
    $stmt = $conn->prepare("SELECT * FROM Product WHERE ProductID = :ProductID");
    $stmt->bindParam(':ProductID', $ProductID);
    $stmt->execute();
    $Product = $stmt->fetch(PDO::FETCH_ASSOC);
   
}

?>
<head>
    <style>
        /* Product Details Page Styles */
/* Product Details Page - Centered Layout */
main {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px); /* Adjust based on header/footer height */
    padding: 40px 20px;
}

.container {
    display: flex;
    max-width: 1200px;
    width: 100%;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.product_img {
    flex: 1;
    min-width: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f9f9f9;
    padding: 30px;
}

.product_img img {
    max-width: 100%;
    max-height: 500px;
    object-fit: contain;
    border-radius: 5px;
}

.product_info {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.product_name {
    color: #2c3e50;
    font-size: 2.2rem;
    margin: 0;
    line-height: 1.2;
}

.product_category a {
    color: #6A89A7;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block; /* Better for hover effects */
    position: relative; /* For underline effect */
}

.product_category a:hover {
    color: #4A6B8A;
    transform: translateY(-1px);
}

/* Optional: Add underline effect on hover */
.product_category a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -2px;
    left: 0;
    background-color: #4A6B8A;
    transition: width 0.3s ease;
}

.product_category a:hover::after {
    width: 100%;
}

.product_price {
    color: #E74C3C;
    font-size: 1.8rem;
    margin: 10px 0;
}

.product_Desc {
    color: #555;
    font-size: 1.1rem;
    line-height: 1.6;
    margin: 20px 0;
}
/* Rest of your existing product styles... */

/* Responsive Adjustments */
@media (max-width: 992px) {
    .container {
        flex-direction: column;
    }
    
    .product_img {
        min-width: 100%;
        padding: 20px;
    }
    
    .product_info {
        padding: 30px;
    }
}

@media (max-width: 768px) {
    .product_img {
        height: 300px;
    }
    
    .product_img img {
        max-height: 280px;
    }
    
    .product_info {
        padding: 20px;
    }
}
    </style>
</head>
<main>
    <div class="container">
        <div class="product_img">
            <img src="<?php echo htmlspecialchars($Product['ImageURL']);?>">
        </div>
        <div class="product_info">
            <h1 class="product_name"><?php echo htmlspecialchars($Product['ProductName']); ?></h1>
            <?php
            $stmt = $conn->prepare("SELECT CategoryName FROM Category WHERE CategoryID=:CategoryID");
            $stmt->bindParam(":CategoryID",$Product["CategoryID"]);
            $stmt->execute();                
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <h2 class="product_category"><a href="section.php?CategoryID=<?php echo htmlspecialchars($Product["CategoryID"])?>"><?php echo htmlspecialchars($res['CategoryName']);?></a></h2>
            <h3 class="product_price">price &nbsp; <?php echo htmlspecialchars($Product['ProductPrice']);?></h3>
            <h4 class="product_Desc"><?php echo htmlspecialchars($Product['ProductDesc']); ?></h4>
        <div class="qty_input">
                <button class="qty_count_mins">-</button>
                <input type="number" id="quantity" name="" value="1" min="0" max="10">
                <button class="qty_count_add">+</button>                
        </div><br>
        <div class="submit">
                <a href="">
                <button class="addto_cart" type="submit" name=""><i class="fa-solid fa-cart-plus">&nbsp; &nbsp;</i> Add to cart</button>
                </a>
        </div>
        </div>

    </div>

</main>

<?php
include("file/footer.php");
 ?>