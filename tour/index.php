<?php
// index.php

// Start the session
session_start();

require_once 'config.php';

header('Location: views/login.php');


// Include the HTML footer
require_once 'views/templates/footer.php';
?>
