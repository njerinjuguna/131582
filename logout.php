<?php
// Start a session to manage user login information.
session_start();

// Include the 'functions.php' file containing necessary functions.
require "functions.php";

// Call the 'logout' function to log the user out by unsetting the session variable.
logout();

// Redirect the user to the main page after logout.
header("Location: ./");
?>
