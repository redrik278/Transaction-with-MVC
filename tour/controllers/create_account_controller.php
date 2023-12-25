<?php
session_start();
require_once('../model/Database.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
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

// Handle form submission for creating an account
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and insert account information into the database
    $accountName = $_POST['account_name'];
    $accountNumber = $_POST['account_number'];
    $initialBalance = $_POST['initial_balance'];

    // Add your validation logic here
    if (empty($accountName) || empty($accountNumber) || empty($initialBalance)) {
        echo "Please fill in all required fields.";
        exit();
    }

    // Validate account number format (you can customize this based on your requirements)
    if (!preg_match('/^\d{8}$/', $accountNumber)) {
        echo "Invalid account number format. Please use an 8-digit number.";
        exit();
    }

    // Validate initial balance as a numeric value
    if (!is_numeric($initialBalance)) {
        echo "Invalid initial balance. Please enter a valid numeric value.";
        exit();
    }

    // Insert the account information into the accounts table
    $insertAccountQuery = "INSERT INTO accounts (user_id, account_number, balance) VALUES (:user_id, :account_number, :initial_balance)";
    $stmtInsertAccount = $pdo->prepare($insertAccountQuery);
    $stmtInsertAccount->bindParam(':user_id', $user_id);
    $stmtInsertAccount->bindParam(':account_number', $accountNumber);
    $stmtInsertAccount->bindParam(':initial_balance', $initialBalance);

    if ($stmtInsertAccount->execute()) {
        // Send a success message back to the client
        echo "Account '$accountName' created successfully!";
    } else {
        // Send an error message back to the client
        echo "Error creating the account.";
    }
} else {
    // Handle cases where the request is not POST (e.g., direct access to this file)
    echo "Invalid request.";
}
?>
