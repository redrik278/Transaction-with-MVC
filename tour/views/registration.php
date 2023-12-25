<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../js/main.js"></script>
    <script>
        $(document).ready(function () {
        $("#registrationForm").submit(function (event) {
            event.preventDefault();

            // Validate form using JavaScript
            if (validateForm()) {
                // Display a loading message or spinner
                $("#registrationMessage").html("Registering...");

                // Use FormData to handle file uploads
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '../controllers/RegistrationController.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // Update the message div with the response
                        $("#registrationMessage").html(response);
                    },
                    error: function () {
                        $("#registrationMessage").html("Error occurred during registration.");
                    }
                });
            }
        });

        function validateForm() {
            // Basic JavaScript form validation
            var name = $("#name").val();
            var email = $("#email").val();
            var phone = $("#phone").val();
            var password = $("#password").val();

            if (name === "" || email === "" || phone === "" || password === "") {
                alert("Please fill in all required fields.");
                return false;
            }

            // Validate email format using a simple regular expression
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Validate phone number to be numeric and 11 digits
            var phoneRegex = /^[0-9]{11}$/;
            if (!phoneRegex.test(phone)) {
                alert("Please enter a valid 11-digit numeric phone number.");
                return false;
            }

            // Validate password to be at least 8 characters long
            if (!/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(password)) {
                    alert("Password must be at least 8 characters long and contain at least one uppercase and one lowercase letter.");
                    return;
            }

            // You can add more specific validation checks as needed

            return true;
        }
    });
    </script>
</head>
<body>
    <h2>User Registration</h2>
    <div id="registrationMessage"></div>

    <form id="registrationForm" enctype="multipart/form-data">
        <!-- Your form fields go here -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required placeholder="Enter your full name"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter a valid email address"><br>

        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" pattern="[0-9]{11}" title="Please enter a valid 11-digit numeric phone number" placeholder="Enter your 11-digit phone number"><br>

        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture"><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter a password (min. 8 characters)"><br>

        <input type="submit" value="Register">
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
</body>
</html>

