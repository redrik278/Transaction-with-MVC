<?php
session_start();

// Include your database connection code here
require_once('../model/Database.php');

// Function to handle money transfer
function transferMoney($user_id, $targetAccountNumber, $amount, $pdo) {
    try {
        $pdo->beginTransaction();

        // Update the balance of the user's account
        $updateUserBalanceQuery = "UPDATE accounts SET balance = balance - :amount WHERE user_id = :user_id AND balance >= :amount";
        $stmtUpdateUserBalance = $pdo->prepare($updateUserBalanceQuery);
        $stmtUpdateUserBalance->bindParam(':amount', $amount);
        $stmtUpdateUserBalance->bindParam(':user_id', $user_id);
        $stmtUpdateUserBalance->execute();

        $rowsAffected = $stmtUpdateUserBalance->rowCount();

        if ($rowsAffected === 0) {
            throw new Exception('Insufficient balance for the transfer.');
        }

        // Update the balance of the target account
        $updateTargetBalanceQuery = "UPDATE accounts SET balance = balance + :amount WHERE account_number = :target_account_number";
        $stmtUpdateTargetBalance = $pdo->prepare($updateTargetBalanceQuery);
        $stmtUpdateTargetBalance->bindParam(':amount', $amount);
        $stmtUpdateTargetBalance->bindParam(':target_account_number', $targetAccountNumber);
        $stmtUpdateTargetBalance->execute();

        // Insert a record into the transactions table for the transfer history
        $insertTransactionQuery = "INSERT INTO transactions (user_id, amount, description) VALUES (:user_id, :amount, 'Money Transfer')";
        $stmtInsertTransaction = $pdo->prepare($insertTransactionQuery);
        $stmtInsertTransaction->bindParam(':user_id', $user_id);
        $stmtInsertTransaction->bindParam(':amount', $amount);
        $stmtInsertTransaction->execute();

        $pdo->commit();

        return true; // Transfer successful
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Handle the exception (e.g., log the error)
        return false; // Transfer failed
    } catch (Exception $e) {
        $pdo->rollBack();
        // Handle the exception (e.g., log the error)
        return false; // Transfer failed
    }
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

// Fetch user details from the database based on the session information
$user_id = $_SESSION['user_id'];

// Handle the money transfer logic if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetAccountNumber = $_POST['target_account_number'];
    $amount = $_POST['amount'];

    // Validate the target account number and amount
    if (empty($targetAccountNumber) || empty($amount)) {
        $error = 'Please fill in all fields.';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = 'Please enter a valid positive amount.';
    } else {
        // Perform the money transfer and update the balance in the accounts table
        $transferResult = transferMoney($user_id, $targetAccountNumber, $amount, $pdo);

        if ($transferResult) {
            $success = 'Money successfully transferred.';
        } else {
            $error = 'Error transferring money. Please try again.';
        }
    }
}

// Redirect back to the transfer money view (whether success or failure)
header('Location: ../views/transfer_money_view.php');
exit();
?>
