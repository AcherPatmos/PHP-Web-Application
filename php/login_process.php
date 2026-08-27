<?php

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the submitted values
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    /*
        TODO:
        - Connect to the database
        - Find the user by email
        - Verify the hashed password
        - Start a session
        - Redirect to the dashboard
    */

    echo "Login request received successfully.";

} else {

    header("Location: login.php");
    exit();

}