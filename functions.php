<?php

// Enable displaying of error messages for debugging purposes.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Import necessary libraries for sending emails using PHPMailer.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

// Include a configuration file that contains sensitive information like database credentials.
require "config.php";

// Establish a connection to the database using credentials from the 'config.php' file.
$conn = new mysqli(Config::SERVER_NAME, Config::USER_NAME, Config::PASSWORD, Config::DB_NAME);

// Define a function called 'exchangeCode' that generates a random exchange code.
function exchangeCode($length)
{
    // Generate a sequence of random bytes with a length specified by '$length'.
    $bytes = random_bytes(($length) / 2);

    // Convert the random bytes to a hexadecimal string for use as an exchange code.
    $exchange_code = bin2hex($bytes);

    // Return the generated exchange code.
    return $exchange_code;
}

//send email on account creation with option to deactivate. Use deactivation code to authenticate deactivation.

// Function to create an opt-out record for a user with a unique deactivation code.
function create_opt_out($email)
{
    global $conn;

    // Generate a unique deactivation code.
    $dcode = exchangeCode(32);

    // Prepare a database query to insert the user's email and deactivation code.
    $stmt = $conn->prepare("INSERT INTO deactivate(uemail, dcode) VALUES(?,?)");
    $stmt->bind_param("ss", $email, $dcode);
    $stmt->execute();
    $stmt->close();

    // Send an opt-out email to the user with the deactivation code.
    send_opt_out_email($email, $dcode);
}

// Function to retrieve the deactivation code associated with a user's email.
function get_opt_code($email)
{
    global $conn;

    // Prepare a database query to select the deactivation code for the given email.
    $stmt = $conn->prepare("SELECT dcode FROM deactivate WHERE uemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    // Return the deactivation code for the user.
    return $result;
}

// Function to send an opt-out email to the user with a deactivation link.
function send_opt_out_email($email, $dcode)
{
    $url = "http://127.0.0.1/places";
    $link = $url . "opt-out.php?user=$email&code=$dcode";

    // Compose the email body with a link to opt out of We Live.
    $body = "<div style='background-color: #77d7c8;' > <div style='background-color: black; color: #77d7c8; text-align: center;'><h2>We Live</h2></div> <div style='text-align: center; color: black; background-color: #77d7c8; padding: 5%;'><p>Thank you for choosing We Live. </p><p>Visit <a href='$url'>We Live</a> to talk to friends in real time. </p><br><br><span style='font-size: 0.85em;'>Didn't sign up? <a href='$link'>Opt out</a> of We Live.</span> </div> </div>";

    // Compose an alternative text-only version of the email.
    $altbody = "Thank you for choosing We Live. Visit $url  to view places to stay. \n\nDidn't sign up? Use the link below to opt out of We Live. \n$link";

    $subject = "WELCOME";

    // Send the email to the user.
    send_email($email, $subject, $body, $altbody);
}

// Function to verify if a user's deactivation code matches the stored code.
function verify_opt_out($user, $code)
{
    global $conn;

    // Prepare a database query to check if the given code matches the stored code for a user.
    $stmt = $conn->prepare("SELECT uemail FROM deactivate WHERE dcode = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    // If the result matches the user, return true (valid deactivation), else return false.
    if ($result == $user) {
        return true;
    } else {
        return false;
    }
}

// Function to create a new user in the 'users' table.
function create_user($email, $username, $passwordHash, $active = 1)
{
    global $conn;

    // Prepare a database query to insert user information into the 'users' table.
    $stmt = $conn->prepare("INSERT INTO users(uemail, username, passwordHash, uactive) VALUES(?,?,?,?)");
    $stmt->bind_param("sssi", $email, $username, $passwordHash, $active);
    $stmt->execute();
    $stmt->close();

    // Create an opt-out record for the user.
    create_opt_out($email);
}

// Function to create a new Google user in the 'users_google' table.
function create_google_user($email, $username, $active = 1)
{
    global $conn;

    // Prepare a database query to insert Google user information into the 'users_google' table.
    $stmt = $conn->prepare("INSERT INTO users_google(g_email, username, uactive) VALUES(?,?,?)");
    $stmt->bind_param("ssi", $email, $username, $active);
    $stmt->execute();
    $stmt->close();
}

// Function to check if a user with a given email exists in the 'users' table.
function check_user($user)
{
    global $conn;

    // Prepare a database query to check if a user with the given email exists in the 'users' table.
    $stmt = $conn->prepare("SELECT id FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    // If a result is found, the user exists; otherwise, they do not.
    return isset($result) ? true : false;
}

// Function to check if a username is already taken (in both 'users' and 'users_google' tables).
function check_username($username)
{
    global $conn;

    // Check if the username exists in the 'users' table.
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    // If the username is not found in 'users', check the 'users_google' table.
    if (!isset($result)) {
        $stmt = $conn->prepare("SELECT id FROM users_google WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
    }

    // If a result is found, the username is already taken; otherwise, it is available.
    return isset($result) ? true : false;
}

// Function to check if a Google user with a given email exists in the 'users_google' table.
function check_google_user($user)
{
    global $conn;

    // Prepare a database query to check if a Google user with the given email exists in the 'users_google' table.
    $stmt = $conn->prepare("SELECT id FROM users_google WHERE g_email = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    // If a result is found, the Google user exists; otherwise, they do not.
    return isset($result) ? true : false;
}

// Function to check if a user's account is active (active status is 1) in the 'users' table.
function check_active($user)
{
    global $conn;
    
    // Prepare a database query to check the 'uactive' status of the user with the given email.
    $stmt = $conn->prepare("SELECT uactive FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    
    // If the 'uactive' status is 1, the user's account is active; otherwise, it is not.
    return $result == 1 ? true : false;
}

// Function to activate or deactivate a user's account in the 'users' table.
function activate($user, $active = 1)
{
    global $conn;
    
    // Prepare a database query to update the 'uactive' status of the user with the given email.
    $stmt = $conn->prepare("UPDATE users SET uactive = ? WHERE uemail = ?");
    $stmt->bind_param("is", $active, $user);
    $stmt->execute();
    $stmt->close();
}

// Function to check if a user is both registered and active.
function check_active_user($user)
{
    // Check if the user exists in the 'users' table and if their account is active.
    return check_user($user) && check_active($user) ? true : false;
}

// Function to check if a user's account is deactivated (active status is not 1).
function isDeactivated($user)
{
    // Check if the user exists in the 'users' table and if their account is not active.
    if (check_user($user)) {
        if (!check_active($user)) {
            return true; // User's account is deactivated.
        }
    }
    return false; // User's account is active or not found.
}

// Function to retrieve a user's password hash from the 'users' table using their email.
function retrievePassword($user)
{
    global $conn;
    
    // Prepare a database query to retrieve the password hash for the user with the given email.
    $stmt = $conn->prepare("SELECT passwordHash FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    
    // Return the retrieved password hash.
    return $result;
}

// Function to retrieve a user's email using their username.
function retrieveEmail($username)
{
    global $conn;
    
    // Prepare a database query to retrieve the email for the user with the given username in the 'users' table.
    $stmt = $conn->prepare("SELECT uemail FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    
    $len = strlen($result);
    
    // If the email is not found in 'users', check the 'users_google' table.
    if ($len < 1) {
        $stmt2 = $conn->prepare("SELECT g_email FROM users_google WHERE username = ?");
        $stmt2->bind_param("s", $username);
        $stmt2->execute();
        $stmt2->bind_result($result2);
        $stmt2->fetch();
        $stmt2->close();
        return $result2; // Return the email from 'users_google' if found.
    }
    
    // Return the email from 'users' if found, or an empty string if not found.
    return $result;
}


// Function to send an email using PHPMailer.
function send_email($user, $subject, $body, $altbody)
{
    // Create a new instance of PHPMailer for sending emails.
    $mail = new PHPMailer(true);
    
    // Configure PHPMailer to use SMTP for sending.
    $mail->isSMTP();
    $mail->Host = Config::SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = Config::SMTP_USER;
    $mail->Password   = Config::SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    
    // Set the email's sender and recipient.
    $mail->setFrom('no-reply@otanga.co.ke', 'We Live');
    $mail->addAddress($user);
    
    // Set the email format to HTML and provide subject, body, and alternative body.
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $altbody;
    
    // Send the email using PHPMailer.
    $mail->send();
}

// Function to log a user in by storing their username or email in a session variable.
function login($user)
{
    // Store the user's information in the session.
    $_SESSION['user'] = $user;
}

// Function to log a user out by unsetting the session variable.
function logout()
{
    // Unset the user's session variable to log them out.
    unset($_SESSION['user']);
}

// Function to check if a user is logged in, and if not, redirect them to a specified page.
function must_login($redirect)
{
    // Check if the user is not logged in (session variable not set).
    if (!(isset($_SESSION['user']))) {
        // Redirect the user to the specified page.
        header("Location: " . $redirect);
    }
}
