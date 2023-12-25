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

// Handle form submission for updating user information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and update user information in the database (add your validation logic)
    $newName = $_POST['new_name'];
    $newPhone = $_POST['new_phone'];
    $newEmail = $_POST['new_email'];

    // Validate new name
    if (empty($newName)) {
        echo "Please provide a new name.";
        exit();
    }

    // Validate new email
    if (empty($newEmail) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $newEmail)) {
        echo "Please provide a valid new email address.";
        exit();
    }
    // Handle profile picture upload
    if ($_FILES['new_profile_picture']['size'] > 0) {
        $newProfilePicture = $_FILES['new_profile_picture']['name'];
        $newProfilePictureTmp = $_FILES['new_profile_picture']['tmp_name'];
        $newProfilePicturePath = 'uploads/' . $newProfilePicture;

        // Ensure the 'uploads' directory exists and has proper permissions
        if (!is_dir('uploads')) {
            mkdir('uploads');
        }

        move_uploaded_file($newProfilePictureTmp, $newProfilePicturePath);

        // Update the profile picture path in the database
        $updatePictureQuery = "UPDATE users SET profile_photo = :new_profile_picture WHERE id = :user_id";
        $stmtUpdatePicture = $pdo->prepare($updatePictureQuery);
        $stmtUpdatePicture->bindParam(':new_profile_picture', $newProfilePicturePath);
        $stmtUpdatePicture->bindParam(':user_id', $user_id);
        $stmtUpdatePicture->execute();
    }

    // Delete profile picture if the user clicks the delete button
    if (isset($_POST['delete_profile_picture'])) {
        unlink($userData['profile_photo']);
        $deletePictureQuery = "UPDATE users SET profile_photo = NULL WHERE id = :user_id";
        $stmtDeletePicture = $pdo->prepare($deletePictureQuery);
        $stmtDeletePicture->bindParam(':user_id', $user_id);
        $stmtDeletePicture->execute();
    }

    // Update the user's name, phone, and email
    $updateUserQuery = "UPDATE users SET name = :new_name, phone = :new_phone, email = :new_email WHERE id = :user_id";
    $stmtUpdateUser = $pdo->prepare($updateUserQuery);
    $stmtUpdateUser->bindParam(':new_name', $newName);
    $stmtUpdateUser->bindParam(':new_phone', $newPhone);
    $stmtUpdateUser->bindParam(':new_email', $newEmail);
    $stmtUpdateUser->bindParam(':user_id', $user_id);
    $stmtUpdateUser->execute();

    // Redirect back to the profile page after updating
    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Edit Profile</title>
    <script>
        // Client-side validation function
        function validateForm() {
            var newName = document.getElementById('new_name').value;
            var newPhone = document.getElementById('new_phone').value;
            var newEmail = document.getElementById('new_email').value;

            // Validate new name
            if (newName.trim() === "") {
                alert("Please provide a new name.");
                return false;
            }

            // Validate new email
            var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if (newEmail.trim() === "" || !emailRegex.test(newEmail)) {
                alert("Please provide a valid new email address.");
                return false;
            }

            // You can add more client-side validation here if needed

            return true; // Allow the form submission if all validations pass
        }
    </script>
</head>
<body>
    <h2>Edit Profile</h2>

    <?php if ($userData): ?>
        <form action="edit_profile.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="new_name">New Name:</label>
            <input type="text" id="new_name" name="new_name" value="<?php echo $userData['name']; ?>" required><br>

            <label for="new_phone">New Phone:</label>
            <input type="tel" id="new_phone" name="new_phone" value="<?php echo $userData['phone']; ?>"><br>

            <label for="new_email">New Email:</label>
            <input type="email" id="new_email" name="new_email" value="<?php echo $userData['email']; ?>" required><br>

            <label for="new_profile_picture">New Profile Picture:</label>
            <input type="file" id="new_profile_picture" name="new_profile_picture"><br>

            <!-- Add option to delete profile picture -->
            <input type="submit" name="delete_profile_picture" value="Delete Profile Picture">

            <input type="submit" value="Update Profile">
        </form>

        <p><a href="profile.php">Back to Profile</a></p>
    <?php else: ?>
        <p>Error retrieving user data.</p>
    <?php endif; ?>

</body>
</html>
