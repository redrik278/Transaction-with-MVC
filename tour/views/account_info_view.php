<?php
session_start();
//account_info_view.php
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Account Information</title>
</head>
<body>

    <?php if ($userData && $accountData): ?>
        <h2>Welcome, <?php echo $userData['name']; ?>!</h2>
        <img src="<?php echo $userData['profile_photo']; ?>" alt="Profile Picture" width="100">

        <p>Email: <?php echo $userData['email']; ?></p>
        <p>Phone: <?php echo $userData['phone']; ?></p>

        <!-- Display account number -->
        <p>Account Number: <?php echo $accountData['account_number']; ?></p>

        <!-- Add other user details as needed -->

        <!-- "Edit Profile" button and link -->
        <p><a href="edit_profile.php" class="edit-profile">Edit Profile</a></p>

        <!-- Buttons for Create Account and Balance Check -->
        <button class="balance-check" onclick="window.location.href='balance_check_view.php'">Balance Check</button>
        <button class="transfer-money" onclick="window.location.href='transfer_money_view.php'">Transfer Money</button>
        <button class="transaction-history" onclick="window.location.href='transaction_history.php'">Transaction History</button>
        <p><a href="profile.php">Back to Profile</a></p>
        <p><a href="logout.php" class="logout">Logout</a></p>
    <?php else: ?>
        <p>Error retrieving user data.</p>
    <?php endif; ?>

</body>
</html>
