<?php
// EditProfileController.php
session_start();
include('../views/edit_profile.php');
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
    $newEmail = $_POST['new_email'];
    $currentPassword = $_POST['current_password'];
    $newPhone = $_POST['new_phone'];

    // Verify the current password before proceeding
    $checkPasswordQuery = "SELECT password FROM users WHERE id = :user_id";
    $stmtCheckPassword = $pdo->prepare($checkPasswordQuery);
    $stmtCheckPassword->bindParam(':user_id', $user_id);
    $stmtCheckPassword->execute();
    $hashedPassword = $stmtCheckPassword->fetchColumn();

    if (password_verify($currentPassword, $hashedPassword)) {
        // Password is correct, proceed with updates

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

        // Update the user's name, email, and phone
        $updateUserQuery = "UPDATE users SET name = :new_name, email = :new_email, phone = :new_phone WHERE id = :user_id";
        $stmtUpdateUser = $pdo->prepare($updateUserQuery);
        $stmtUpdateUser->bindParam(':new_name', $newName);
        $stmtUpdateUser->bindParam(':new_email', $newEmail);
        $stmtUpdateUser->bindParam(':new_phone', $newPhone);
        $stmtUpdateUser->bindParam(':user_id', $user_id);
        $stmtUpdateUser->execute();

        // Redirect back to the profile page after updating
        header('Location: profile.php');
        exit();
    } else {
        // Incorrect password, display an error message
        $errorMessage = "Incorrect password. Please enter the correct password.";
    }
}
?>
