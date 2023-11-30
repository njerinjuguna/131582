<?php
// Include the 'functions.php' file containing necessary functions.
require "functions.php";

// Get the 'user' and 'code' parameters from the query string in the URL.
$user = $_GET['user'];
$code = $_GET['code'];

// Call the 'verify_opt_out' function to check if the provided 'user' and 'code' match in the database.
$bool = verify_opt_out($user, $code);

// If the 'user' and 'code' match in the database (valid deactivation), deactivate the account.
if ($bool) {
    // Call the 'activate' function with a status of 0 to deactivate the account.
    activate($user, 0);
    echo "Your account has been deactivated successfully.";
} else {
    // If the 'user' and 'code' do not match (invalid deactivation), display an error message.
    echo "Invalid deactivation code";
}
