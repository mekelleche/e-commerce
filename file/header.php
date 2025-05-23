<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
        <div class="logo">
            <h1>online store</h1>
            <img src="images/logo1.jpg">
        </div>
        <div class="search">
            <div class="search_bar">
                <form action="search.php" method="get">
                    <input type="text" class="search_input" name="search" placeholder="search for item">
                    <button class="button_search" name="btn_search"></button>
                </form>
            </div>
        </div>
    </header>
    
<nav>
    <div class="social">
        <ul>
            <li><a href="" target="_blank"><i class="fa-brands fa-facebook"></i></a></li>
            <li><a href="" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="https://github.com/mekelleche/e-commerce" target="_blank"><i class="fa-brands fa-github"></i></a></li>
        </ul>
    </div>
    <div class="section">
        <ul>
            
            <li><a href="home.php">Home</a></li>
            <?php
                $stmt = $conn->prepare("SELECT * FROM Category");
                $stmt->execute();                
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($result as $row){
            ?>
            <li><a href="section.php?CategoryID=<?php echo htmlspecialchars($row["CategoryID"])?>"><?php echo htmlspecialchars($row['CategoryName']); ?></a></li>
        <?php
        }
        ?>
        </ul>
    </div>    
</nav>
<div class="cart">
        <ul>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i></a></li>
            <li class="cart-icon"><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
            <?php
            $stmt = $conn->prepare("SELECT * FROM Cart");
            $stmt->execute();
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <span class="cart-counter"><?php echo htmlspecialchars(count($cartItems));?></span>
            </li>
            
        </ul>
    </div>