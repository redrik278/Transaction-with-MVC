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
    <title>Transfer Money</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<h2>Transfer Money</h2>

<?php if ($userData && $accountData): ?>
    <h3>Your Account: <?php echo $accountData['account_number']; ?></h3>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Add a form for money transfer -->
    <form id="transferForm" method="post" action="../controllers/transfer_money_controller.php">
        <label for="target_account_number">Target Account Number:</label>
        <input type="text" id="target_account_number" name="target_account_number" placeholder="Enter target account number" required>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" placeholder="Enter amount" required>

        <button type="submit">Transfer Money</button>
    </form>

    <!-- "Back to Profile" and "Logout" links -->
    <p><a href="profile.php">Back to Profile</a></p>
    <p><a href="logout.php" class="logout">Logout</a></p>

    <script>
        $(document).ready(function () {
            $("#transferForm").submit(function (event) {
                event.preventDefault();

                // Validate form using JavaScript
                if (validateForm()) {
                    Display a loading message or spinner (optional)
                    $("#registrationMessage").html("Transferring money...");

                    // Use FormData to handle form data
                    var formData = new FormData(this);

                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            // Update the message div with the response
                            // $("#registrationMessage").html(response);

                            // Clear the input fields on success (optional)
                            $("#target_account_number, #amount").val('');

                            // Handle success or error response (you may need to customize this)
                            if (response === 'success') {
                                alert("Money transferred successfully!");
                            } else {
                                alert("Error: " + response);
                            }
                        },
                        error: function () {
                            alert("Error occurred during money transfer.");
                        }
                    });
                }
            });

            function validateForm() {
                var targetAccountNumber = $("#target_account_number").val();
                var amount = $("#amount").val();

                // Validate target account number to be a non-empty string
                if (targetAccountNumber.trim() === "") {
                    alert("Please enter a valid target account number.");
                    return false;
                }

                // Validate amount to be a positive number
                if (!/^\d+(\.\d{1,2})?$/.test(amount) || amount <= 0) {
                    alert("Please enter a valid positive number for the amount.");
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
