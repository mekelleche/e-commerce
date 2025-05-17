<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $username=$_POST["username"];
    $pwd=$_POST["pwd"];
    if ($username=="admin@gmail.com" && $pwd=="admin"){
        header("Location: ../control_panel.php");
    }else{
        header("Location: ../home.php");
    }
}