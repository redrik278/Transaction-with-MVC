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
    <title>User Profile</title>
</head>
<body>

    <?php if ($userData): ?>
        <h2>Welcome, <?php echo $userData['name']; ?>!</h2>
        <p><img src="<?php echo $userData['profile_photo']; ?>" alt="Profile Picture" width="100"></p>

        <!-- <p>Email: <?php echo $userData['email']; ?></p>
        <p>Phone: <?php echo $userData['phone']; ?></p> -->

        <!-- "Edit Profile" button and link -->
        <button class="account-info" onclick="window.location.href='edit_profile.php'">Edit Profile</button>

        <!-- Buttons for Account Information and Create Account -->
        <button class="account-info" onclick="window.location.href='account_info_view.php'">Account Information</button>
        <button class="create-account" onclick="window.location.href='create_account_view.php'">Create Account</button>
        <!-- Add other user details as needed -->

        <p><a href="logout.php" class="logout">Logout</a></p>
    <?php else: ?>
        <p>Error retrieving user data.</p>
    <?php endif; ?>

</body>
</html>
