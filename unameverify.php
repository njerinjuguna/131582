<?php
// Include the 'functions.php' file containing necessary functions.
require "functions.php";

// Check if the HTTP request method is POST.
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve the 'name' parameter from the POST data (presumably a username).
    $name = $_POST['name'];

    // Call the 'check_username' function to check if the username exists.
    $bool = check_username($name);

    // Encode the result as JSON and echo it.
    // If the username exists, echo 1; otherwise, echo 0.
    echo json_encode($bool ? 1 : 0);
}
?>
