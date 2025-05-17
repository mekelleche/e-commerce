
<?php
require_once "includes/connect.inc.php";
include("file/header.php");
?>
    <main>
    <?php if (isset($_GET["btn_search"]) && !empty($_GET["search"])): ?>
        <?php
        $searchTerm = trim($_GET["search"]);
        $searchParam = "%$searchTerm%";
        
        $stmt = $conn->prepare("SELECT * FROM Product 
                       WHERE ProductDesc LIKE :search 
                       OR ProductName LIKE :search
                       OR CategoryID IN (SELECT CategoryID FROM Category WHERE CategoryName LIKE :search)");
        $stmt->bindParam(':search', $searchParam);
        $stmt->execute();                
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($result) > 0): ?>
           <!-- <h2 class="search-results-title">Search Results for "<?php echo htmlspecialchars($searchTerm); ?>"</h2>-->
            <?php foreach($result as $row): ?>
            <div class="product">
                <div class="product_img">
                    <img src="<?php echo htmlspecialchars($row['ImageURL']);?>"><a href=""></a>
                </div>
                <?php
                $stmt = $conn->prepare("SELECT CategoryName FROM Category WHERE CategoryID=:CategoryID");
                $stmt->bindParam(":CategoryID",$row["CategoryID"]);
                $stmt->execute();                
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="product_content">
                <div class="product_section">
                    <a href=""><?php echo htmlspecialchars($res['CategoryName']); ?></a>
                </div>
                <div class="product_name">
                    <a href=""><?php echo htmlspecialchars($row['ProductName']); ?></a>
                </div>
                <div class="product_price">
                    <a href="">price &nbsp; <?php echo htmlspecialchars($row['ProductPrice']);?></a>
                </div>
                <div class="product_descrpition">
                    <a href=""><i class="fa-solid fa-eye"></i> <?php echo htmlspecialchars($row['ProductDesc']); ?></a>
                </div>
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
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No products found matching "<?php echo htmlspecialchars($searchTerm); ?>"</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
    <?php
    include("file/footer.php");
    ?>