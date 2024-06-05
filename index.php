<?php
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Register user    
        addUserToDatabase($_POST['username'], $_POST['password'], $_POST['full_name'], $_POST['phone_number'], $_POST['email']);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gi Pay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="login-box">
        <div class="left">
            <h1>Sign up User</h1>
            <form action="index.php" method="POST">
                <input type="text" name="username" placeholder="Username" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="text" name="full_name" placeholder="Full Name" required />
                <input type="text" name="phone_number" placeholder="Phone Number" required />
                <input type="text" name="email" placeholder="Email" required />
                <input type="submit" name="signup_submit" value="Sign me up" formaction="sign-inuser.php" />
            </form>
        </div>

        <div class="right">
            <span class="loginwith">Sign in with<br />social network</span>
            <form>
                <button class="social-signin facebook" formaction="sign-inuser.php">Sign In User</button>
                <button class="social-signin twitter" formaction="admin.php">Admin</button>
                <button class="social-signin google" formaction="merchant.php">Sign up Merchant</button>
            </form>
        </div>
        <div class="or">OR</div>
    </div>
</body>
</html>
