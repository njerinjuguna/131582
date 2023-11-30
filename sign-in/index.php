<?php

require "../header.htm";
require "../functions.php";
require "../vendor/autoload.php";

$redirectUri = 'https://otanga.co.ke/Projects/Chat-App-PHP/gauth.php';
   
$client = new Google_Client();
$client->setClientId(GClient::CLIENT_ID);
$client->setClientSecret(GClient::CLIENT_SECRET);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

?>


<div class="container">
    <form class="p-3 rounded uform" action="../forms.php" method="POST">
        <span><h3>Sign In</h3></span>
        
        <label for="">
            <span>Email: </span><br>
            <input type="email" name="uemail" required>
        </label>
        <br>

        <label for="">
            <span>Password: </span><br>
            <input type="password" name="upassword" required>
        </label>
        <br>

        <button class="m-2 btn mainbg white" type="submit" name="signin">Sign In</button><br>

        <a class="m-1 btn btn-outline-dark" href="<?php echo $client->createAuthUrl(); ?>" role="button" style="text-transform:none">
            <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />Login with Google
        </a>

        <br><span><a href="../sign-up">Sign up instead</a></span><br>
    </form>
</div>