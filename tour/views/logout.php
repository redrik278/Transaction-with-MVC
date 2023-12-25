<?php
// logout.php

// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
if (session_destroy()) {
    // Regenerate the session ID to help prevent session fixation
    session_regenerate_id(true);

    // Redirect to the login page or any other page you prefer
    header("Location: login.php");
    exit();
} else {
    // Handle session destruction failure
    echo "Logout failed. Please try again.";
}
?>
