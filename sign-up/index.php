<?php

require "../header.htm";

?>


<div class="container" style="background-color: #77d7c8;">
    <form class="p-3 rounded uform" action="../forms.php" method="POST" autocomplete="off">
        <span><h3>Sign Up</h3></span>
        
        <label for="">
            <span>Email: </span><br>
            <input type="email" name="uemail">
        </label>
        <br>

        <label for="">
            <span>Username: </span><br>
            <input type="text" name="username" oninput="searchUsername(this.value)"><br>
            <span id="usernameWarning" style="color: red !important;" hidden>Username already exists!</span>
        </label>
        <br>

        <label for="">
            <span>Password: </span><br>
            <input type="password" name="upassword">
        </label>
        <br>

        <button class="m-2 btn mainbg white" id="signupBtn" type="submit" name="signup">Sign Up</button>

        <br><span><a href="../sign-in">Sign in instead</a></span><br>
    </form>
</div>