<?php
session_start();

//balance_check_controller.php

// Include your database connection code here
require_once('../model/Database.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

// Fetch user details from the database based on the session information
$user_id = $_SESSION['user_id'];
$getUserQuery = "SELECT id, name, email, phone, profile_photo FROM users WHERE id = :user_id";
$stmtUser = $pdo->prepare($getUserQuery);
$stmtUser->bindParam(':user_id', $user_id);
$stmtUser->execute();
$userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Handle form submission for checking account balance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Display current balance
    $currentBalance = getCurrentBalance($pdo, $user_id);
}

// Redirect back to the balance check view with the current balance
header("Location: ../views/balance_check_view.php?currentBalance=$currentBalance");
exit();

function getCurrentBalance($pdo, $user_id) {
    // Retrieve the current balance from the accounts table
    $getBalanceQuery = "SELECT balance FROM accounts WHERE user_id = :user_id";
    $stmtBalance = $pdo->prepare($getBalanceQuery);
    $stmtBalance->bindParam(':user_id', $user_id);
    $stmtBalance->execute();
    $balance = $stmtBalance->fetchColumn();

    return $balance;
}
?>
