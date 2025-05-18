<?php
require_once "includes/connect.inc.php";
include("file/header.php");
session_start();
?>

    <main>
        <?php
        $stmt = $conn->prepare("SELECT * FROM Product");
        $stmt->execute();                
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($result as $row){
        ?>
        <div class="product">
            <div class="product_img">
                <img src="<?php echo htmlspecialchars($row['ImageURL']);?>"><a href="details.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>"></a>
            </div>
            <?php
            $stmt = $conn->prepare("SELECT CategoryName FROM Category WHERE CategoryID=:CategoryID");
            $stmt->bindParam(":CategoryID",$row["CategoryID"]);
            $stmt->execute();                
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="product_content">
            <div class="product_section">
                <a href="section.php?CategoryID=<?php echo htmlspecialchars($row["CategoryID"])?>"><?php echo htmlspecialchars($res['CategoryName']); ?></a>
            </div>
            <div class="product_name">
                <a href="details.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>"><?php echo htmlspecialchars($row['ProductName']); ?></a>
            </div>
            <div class="product_price">
                <a href="details.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>">price &nbsp; $<?php echo htmlspecialchars($row['ProductPrice']);?></a>
            </div>
            <div class="product_descrpition">
                <a href="details.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>"><i class="fa-solid fa-eye"></i> <?php echo htmlspecialchars($row['ProductDesc']); ?></a>
            </div>
            <div class="qty_input">
                <form action="cart.php?ProductID=<?php echo htmlspecialchars($row['ProductID']);?>" method="post">
                <button class="qty_count_mins">-</button>
                <input type="number" id="quantity" name="q" value="1" min="0" max="10">
                <input type="hidden" name="h_name" value="<?php echo htmlspecialchars($row['ProductName']); ?>">
                <input type="hidden" name="h_price" value="<?php echo htmlspecialchars($row['ProductPrice']);?>">
                <input type="hidden" name="h_img" value="<?php echo htmlspecialchars($row['ImageURL']);?>">
                
                <button class="qty_count_add">+</button>                
            </div><br>
            <div class="submit">
                <a href="">
                <button class="addto_cart" type="submit" name="add" value="add_cart"><i class="fa-solid fa-cart-plus">&nbsp; &nbsp;</i> Add to cart</button>
                </a>
            </div>
            </form>
            </div>
        </div>
        <?php
        }
        ?>
    </main>
    <?php
    include("file/footer.php");
    ?>