<?php
// Start a session to store user login information.
session_start();

// Include the 'functions.php' file containing necessary functions.
require "functions.php";

// Handle user signup form submission.
if(isset($_POST['signup'])) {
    // Retrieve user input from the signup form.
    $email = $_POST['uemail'];
    $username = $_POST['username'];
    
    // Check if the username is already taken; if so, redirect to the signup page.
    if(check_username($username)) {
        header("Location: signup/");
    }
    
    // Hash the user's password for security.
    $passwordHash = password_hash($_POST['upassword'], PASSWORD_DEFAULT);

    // Create a new user record in the database and log the user in.
    create_user($email, $username, $passwordHash);
    login($email);
    
    // Redirect the user to the main page.
    header("Location: ./");
}

// Handle Google user signup form submission.
if(isset($_POST['Gsignup'])) {
    // Retrieve user input from the Google signup form.
    $email = $_POST['g_email'];
    $username = $_POST['username'];
    
    // Check if the username is already taken; if so, redirect to the signup page.
    if(check_username($username)) {
        header("Location: signup/");
    }

    // Create a new Google user record in the database and log the user in.
    create_google_user($email, $username);
    login($email);
    
    // Redirect the user to the main page.
    header("Location: ./");
}

// Handle user signin form submission.
if(isset($_POST['signin'])) {
    // Retrieve user input from the signin form.
    $email = $_POST['uemail'];
    $password = $_POST['upassword'];

    // Check if the user account is active and exists.
    if(check_active_user($email)) {
        // Retrieve the stored password hash for the user.
        $passwordHash = retrievePassword($email);

        // Verify the entered password against the stored hash.
        if(password_verify($password, $passwordHash)) {
            // If the password is correct, log the user in.
            login($email);
            header("Location: ./");
        } else {
            echo "Incorrect password"; // Display an error message for an incorrect password.
        }   
    } else {
        echo "User does not exist"; // Display an error message for a non-existent user.
    }
}
?>
