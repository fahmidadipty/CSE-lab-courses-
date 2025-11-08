<?php
session_start();
include 'db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $user_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

header("Location: ad_usermanagement.php");
exit;
?>
