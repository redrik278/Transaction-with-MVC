<?php
session_start();
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Balance Check</title>
</head>
<body>

    <?php if ($userData): ?>
        <h2>Welcome, <?php echo $userData['name']; ?>!</h2>
        <img src="<?php echo $userData['profile_photo']; ?>" alt="Profile Picture" width="100">

        <!-- Your Balance Check Form Goes Here -->
        <form action="../controllers/transfer_money_controller.php" method="post">
            <!-- Display current balance -->
            <p>Your current balance is: $<?php echo getCurrentBalance($pdo, $user_id); ?></p>
            <button class="transfer-money" onclick="window.location.href='transfer_money_view.php'">Transfer Money</button>

        </form>

        <p><a href="profile.php" class="back-to-profile">Back to Profile</a></p>
    <?php else: ?>
        <p>Error retrieving user data.</p>
    <?php endif; ?>

</body>
</html>

<?php

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
