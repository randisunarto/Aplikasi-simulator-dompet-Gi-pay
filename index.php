
<?php
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Register user
    if (isset($_POST['register_user'])) {
        addUserToDatabase($_POST['username'], $_POST['password'], $_POST['full_name'], $_POST['phone_number'], $_POST['email']);
    }

    // Register merchant
    if (isset($_POST['register_merchant'])) {
        addMerchantToDatabase($_POST['username'], $_POST['password'], $_POST['full_name'], $_POST['store_name'], $_POST['store_address'], $_POST['phone_number'], $_POST['email']);
    }

    // Update balance
    if (isset($_POST['update_balance'])) {
        updateUserBalance($_POST['user_id'], $_POST['amount']);
    }

    // Transfer funds
    if (isset($_POST['transfer_funds'])) {
        transferToBankAccount($_POST['bank_account_data'], $_POST['amount']);
    }

    // Update user status
    if (isset($_POST['update_status'])) {
        updateUserStatus($_POST['user_id'], $_POST['status']);
    }

    // Update merchant status
    if (isset($_POST['update_merchant_status'])) {
        updateMerchantStatus($_POST['merchant_id'], $_POST['status']);
    }
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
    
    <input type="text" name="username" placeholder="Username" />
     <input type="text" name="Password" placeholder="password" />
    <input type="text" name="Full Name" placeholder="Full Name" />
    <input type="password" name="Phone Number" placeholder="Phone Number" />
    <input type="password" name="Email" placeholder="Email" />
    
    <input type="submit" name="signup_submit" value="Sign me up" />
  </div>
  
  <div class="right">
    <span class="loginwith">Sign in with<br />social network</span>
    
    <form>
    <button class="social-signin facebook" formaction="sign-inuser.php">Sign In User</button>
    <button class="social-signin twitter" formaction="admin.php">Admin</button>
    <button class="social-signin google" formaction="merchan.php">Sign up Merchant</button>
  </form>

  </div>
  <div class="or">OR</div>
</div>
</body>
</html>