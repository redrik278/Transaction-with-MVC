<?php
session_start();
//account_info_controller.php
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

// Fetch account details based on the user's user_id
$getAccountQuery = "SELECT account_number FROM accounts WHERE user_id = :user_id";
$stmtAccount = $pdo->prepare($getAccountQuery);
$stmtAccount->bindParam(':user_id', $user_id);
$stmtAccount->execute();
$accountData = $stmtAccount->fetch(PDO::FETCH_ASSOC);

// Prepare data to be sent to the AJAX request
$responseData = array(
    'userData' => $userData,
    'accountData' => $accountData
);

// Send JSON response
header('Content-Type: application/json');
echo json_encode($responseData);
?>
