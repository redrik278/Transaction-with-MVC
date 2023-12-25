<?php
session_start();
require_once('../model/Database.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Create Account</title>
</head>
<body>

<?php if ($userData): ?>
    <h2>Welcome, <?php echo $userData['name']; ?>!</h2>
    <img src="<?php echo $userData['profile_photo']; ?>" alt="Profile Picture" width="100">

    <div id="registrationMessage"></div>

    <!-- Your Create Account Form Goes Here -->
    <form id="registrationForm" method="post">
        <label for="account_name">Account Name:</label>
        <input type="text" id="account_name" name="account_name" placeholder="Enter account name" required><br>

        <label for="account_number">Account Number:</label>
        <input type="number" id="account_number" name="account_number" placeholder="Enter 8-digit account number" required><br>
            
        <label for="initial_balance">Initial Balance:</label>
        <input type="number" id="initial_balance" name="initial_balance" placeholder="Enter initial balance (positive number)" required><br>

        <input type="submit" value="Create Account">
    </form>

    <p><a href="profile.php" class="back-to-profile">Back to Profile</a></p>

    <script>
        $(document).ready(function () {
            $("#registrationForm").submit(function (event) {
                event.preventDefault();

                // Validate form using JavaScript
                if (validateForm()) {
                    // Display a loading message or spinner
                    $("#registrationMessage").html("Creating account...");

                    // Use FormData to handle form data
                    var formData = new FormData(this);

                    $.ajax({
                        type: 'POST',
                        url: '../controllers/create_account_controller.php',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            // Update the message div with the response
                            $("#registrationMessage").html(response);

                            // Clear the input fields on success
                            $("#account_name, #account_number, #initial_balance").val('');
                        },
                        error: function () {
                            $("#registrationMessage").html("Error occurred during account creation.");
                        }
                    });
                }
            });

            function validateForm() {
                var accountNumber = $("#account_number").val();
                var initialBalance = $("#initial_balance").val();

                // Validate account number to be exactly 8 digits
                if (!/^\d{8}$/.test(accountNumber)) {
                    alert("Please enter a valid 8-digit account number.");
                    return false;
                }

                // Validate initial balance to be a positive number
                if (!/^\d+(\.\d{1,2})?$/.test(initialBalance) || initialBalance <= 0) {
                    alert("Please enter a valid positive number for the Initial Balance.");
                    return false;
                }

                return true;
            }
        });
    </script>

<?php else: ?>
    <p>Error retrieving user data.</p>
<?php endif; ?>

</body>
</html>
