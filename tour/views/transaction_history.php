<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

// Include your database connection code here
require_once('../model/Database.php');

// Fetch user details from the database based on the session information
$user_id = $_SESSION['user_id'];

// Fetch transaction history based on the user's user_id
$getTransactionHistoryQuery = "SELECT id, amount, description, date FROM transactions WHERE user_id = :user_id ORDER BY date DESC";
$stmtTransaction = $pdo->prepare($getTransactionHistoryQuery);
$stmtTransaction->bindParam(':user_id', $user_id);
$stmtTransaction->execute();
$transactionData = $stmtTransaction->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Transaction History</title>
</head>
<body>

    <h2>Transaction History</h2>

    <?php if ($transactionData): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactionData as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['date']; ?></td>
                        <td><?php echo $transaction['amount']; ?></td>
                        <td><?php echo $transaction['description']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No transactions found.</p>
    <?php endif; ?>

    <p><a href="account_info_view.php">Back to Account Information</a></p>

</body>
</html>
