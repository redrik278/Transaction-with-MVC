<!-- personal_information.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script type="text/javascript" src="js/main.js"></script>
</head>

<body>

    <div class="container">
        <h2>Personal Information</h2>

        <div id="personalInfo">
            <p><strong>Name:</strong> <span id="name">Loading...</span></p>
            <p><strong>Email:</strong> <span id="email">Loading...</span></p>
            <p><strong>Phone:</strong> <span id="phone">Loading...</span></p>
            <!-- Add more personal information fields as needed -->
        </div>

        <button type="button" onclick="fetchPersonalInformation()">Refresh Information</button>
    </div>

    <script type="text/javascript" src="js/main.js"></script>
    <script type="text/javascript">
        // Fetch and display user personal information on page load
        window.onload = function () {
            fetchPersonalInformation();
        };

        function fetchPersonalInformation() {
            // AJAX request to fetch user personal information
            sendAjaxRequest('controllers/PersonalInformationController.php', 'GET', {}, displayPersonalInformation, displayPersonalInfoError);
        }

        function displayPersonalInformation(personalInfo) {
            // Display user personal information on the page
            document.getElementById('name').innerHTML = personalInfo.name;
            document.getElementById('email').innerHTML = personalInfo.email;
            document.getElementById('phone').innerHTML = personalInfo.phone;
            // Add more lines to display additional personal information fields as needed
        }

        function displayPersonalInfoError(status, message) {
            // Handle personal information fetch errors, e.g., display an error message
            alert("Failed to fetch personal information. Status: " + status + ", Message: " + message);
        }
    </script>

</body>

</html>
