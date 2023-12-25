<?php
require_once('../model/Database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);

    // Validate the user credentials
    if (empty($email) || empty($password)) {
        echo "Please fill in all required fields.";
        exit();
    }

    // Additional email validation using a regular expression
    if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $email)) {
        echo "Invalid email format.";
        exit();
    }

    // Password validation using a regular expression (customize based on your requirements)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        echo "Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit.";
        exit();
    }

    $getUserQuery = "SELECT id, name, email, password FROM users WHERE email = :email";
    $stmtUser = $pdo->prepare($getUserQuery);
    $stmtUser->bindParam(':email', $email);
    $stmtUser->execute();
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($userData && password_verify($password, $userData['password'])) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_name'] = $userData['name'];
        $_SESSION['user_email'] = $userData['email'];
        echo 'success';
        exit();
    } else {
        echo "Invalid login credentials. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>
