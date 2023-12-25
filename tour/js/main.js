// main.js

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
                url: 'controllers/RegistrationController.php',
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
        var password = $("#password").val();

        // Check if the name is empty
        if (name === "") {
            alert("Please enter your name.");
            return false;
        }

        // Check if the email is empty or not in a valid format
        if (email === "" || !isValidEmail(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        // Check if the password is at least 6 characters long
        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }

        // You can add more specific validation checks as needed

        return true;
    }

    function isValidEmail(email) {
        // Basic email validation using a regular expression
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
