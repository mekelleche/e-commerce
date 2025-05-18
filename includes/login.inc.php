<?php
session_start();
require_once "connect.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["pwd"]);

    if (empty($username) || empty($password)) {
        header("location: ../index.php?error=emptyfields");
        exit();
    }
    if($username == "admin@gmail.com" && $password == "admin"){
        header("location: ../control_panel.php");
    }else{

    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM Customer WHERE Email = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($password, $user['Password'])) {
                // Login successful
                $_SESSION['customer_id'] = $user['CustomerID'];
                $_SESSION['customer_email'] = $user['Email'];
                $_SESSION['customer_name'] = $user['FirstName'] . ' ' . $user['LastName'];
                
                header("location: ../home.php");
                exit();
            } else {
                // Wrong password
                header("location: ../index.php?error=wrongcredentials");
                exit();
            }
        } else {
            // User not found
            header("location: ../index.php?error=wrongcredentials");
            exit();
        }
    } catch (PDOException $e) {
        header("location: ../index.php?error=sqlerror");
        exit();
    }
}
} else {
    // Not a POST request
    header("location: ../index.php");
    exit();
}