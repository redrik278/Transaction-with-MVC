<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <script>
        $(document).ready(function () {
            $("#loginForm").submit(function (event) {
                event.preventDefault();

                var email = $("#email").val();
                var password = $("#password").val();

                // Basic JavaScript form validation
                if (email === "" || password === "") {
                    alert("Please fill in all required fields.");
                    return;
                }

                // Password validation
                if (!/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(password)) {
                    alert("Password must be at least 8 characters long and contain at least one uppercase and one lowercase letter.");
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '../controllers/LoginController.php',
                    data: { email: email, password: password },
                    success: function (response) {
                        // Check if the login was successful
                        if (response === 'success') {
                            // Redirect to the profile page
                            window.location.href = 'profile.php';
                        } else {
                            // Update the message div with the response
                            $("#loginMessage").html(response);
                        }
                    },
                    error: function () {
                        $("#loginMessage").html("Error occurred during login.");
                    }
                });
            });
        });
    </script>
</head>
<body>
    <h2>User Login</h2>
    <div id="loginMessage"></div>

    <form id="loginForm">
        <!-- Your form fields go here -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email"><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter your password (min. 8 characters)"><br>

        <input type="submit" value="Login">
    </form>

    <p>Not registered? <a href="registration.php">Register here</a></p>
</body>
</html>
