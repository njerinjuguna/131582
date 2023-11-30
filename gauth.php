<?php
// Start a session to manage user login information.
session_start();

// Include necessary files and functions.
require "functions.php";
require "vendor/autoload.php"; // This is for Google API integration.
require "header.htm"; // Including the header HTML (it contains page structure).

// Create a new instance of the Google_Client class for OAuth authentication.
$client = new Google_Client();
$client->setClientId(GClient::CLIENT_ID); // Set Google OAuth client ID.
$client->setClientSecret(GClient::CLIENT_SECRET); // Set Google OAuth client secret.
$client->addScope("email"); // Add email scope for OAuth.
$client->addScope("profile"); // Add profile scope for OAuth.
$redirectUri = 'https://ehealth.co.ke/gauth.php'; // Set the OAuth redirect URI.
$client->setRedirectUri($redirectUri);

// Check if an authorization code is present in the query string.
if (isset($_GET['code'])) {
    // Fetch an access token using the authorization code.
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // If an access token is obtained, set it in the Google_Client.
    if (isset($token['access_token'])) {
        $client->setAccessToken($token['access_token']);
    } else {
        header("Location: sign-in/"); // Redirect if access token retrieval fails.
    }
 
    // Retrieve user information from Google.
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email; // Get the user's email from Google.
    $name = $google_account_info->name; // Get the user's name from Google.

    // Check if the Google user's email already exists in the database.
    if (check_google_user($email)) {
        login($email); // Log the user in.
        header("Location: ./"); // Redirect to the main page.
    } else {
        // Display a registration form for the Google user if their email is not found.
        echo '
            <div class="container" style="background-color: #77d7c8;">
            <form class="p-3 rounded uform" action="forms.php" method="POST" autocomplete="off">
                <span><h3>Username</h3></span>
    
                <label for="">
                    <span>Username: </span><br>
                    <input type="text" name="username" oninput="searchUsername(this.value)"><br>
                    <input type="text" name="g_email" value="'.$email.'" hidden>
                    <span id="usernameWarning" style="color: red !important;" hidden>Username already exists!</span>
                </label>
                <br>
    
                <button class="m-2 btn" id="signupBtn" type="submit" style="background-color: #77d7c8;" name="Gsignup">Sign Up</button>
            </form>
            </div>
        ';
    }
} else {
    header("Location: sign-in/"); // Redirect if no authorization code is present.
}
