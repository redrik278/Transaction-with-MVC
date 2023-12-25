<?php
require_once('../model/Database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Function to safely escape user inputs
    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $password = password_hash(sanitize_input($_POST['password']), PASSWORD_BCRYPT);

    // Validate email format using a regular expression
    $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($emailRegex, $email)) {
        echo "Error: Invalid email format.";
        exit();
    }

    // Validate phone number to be numeric and 11 digits
    $phoneRegex = '/^[0-9]{11}$/';
    if (!preg_match($phoneRegex, $phone)) {
        echo "Error: Please enter a valid 11-digit numeric phone number.";
        exit();
    }

    // Check if the email already exists
    $checkEmailQuery = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmtCheck = $pdo->prepare($checkEmailQuery);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();
    $emailExists = $stmtCheck->fetchColumn();

    if ($emailExists) {
        // Email already exists, handle accordingly (display error message, redirect, etc.)
        echo "Error: Email already exists. Please choose a different email.";
        exit();
    }

    // Validate password using a regular expression (customize based on your requirements)
    $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    if (!preg_match($passwordRegex, $_POST['password'])) {
        echo "Error: Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit.";
        exit();
    }

    // Handle profile picture upload
    $profilePicture = $_FILES['profile_picture']['name'];
    $profilePictureTmp = $_FILES['profile_picture']['tmp_name'];
    $profilePicturePath = 'uploads/' . $profilePicture;

    // Ensure the 'uploads' directory exists and has proper permissions
    if (!is_dir('uploads')) {
        mkdir('uploads');
    }

    move_uploaded_file($profilePictureTmp, $profilePicturePath);

    // Insert user data into the 'users' table
    $insertUserQuery = "INSERT INTO users (name, phone, email, password, profile_photo) VALUES (:name, :phone, :email, :password, :profile_photo)";
    $stmt = $pdo->prepare($insertUserQuery);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':profile_photo', $profilePicturePath);

    try {
        $stmt->execute();
        // Send a success message
        echo "Registration successful! You can now login.";
    } catch (PDOException $e) {
        // Handle any database errors
        echo "Error: " . $e->getMessage();
    }
}
?>
