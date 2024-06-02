<?php
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Register merchant
    if (isset($_POST['register_merchant'])) {
        addMerchantToDatabase($_POST['full_name'], $_POST['password'], $_POST['store_name'], $_POST['phone_number'], $_POST['email']);
    }
}
?>

<?php
session_start(); // Mulai sesi

require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signup_submit'])) {
        $full_name = $_POST['full_name'];
        $password = $_POST['password'];
        $store_name = $_POST['store_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        
        addMerchantToDatabase($full_name, $password, $store_name, $phone_number, $email);

        // Simpan informasi pengguna dalam sesi
        $_SESSION['full_name'] = $full_name;
        $_SESSION['store_name'] = $store_name;
        $_SESSION['phone_number'] = $phone_number;
        $_SESSION['email'] = $email;

        // Redirect ke dashboard
        header('Location: dashm.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gi Pay</title>
    <link rel="stylesheet" href="merchant.css">
</head>
<body>
   <div id="login-box">
      <div class="left">
        <h1>Sign up Merchant</h1>
        <form method="POST" action="">
          <input type="text" name="full_name" placeholder="Full Name" />
          <input type="password" name="password" placeholder="Password" />
          <input type="text" name="store_name" placeholder="Store Name" />
          <input type="text" name="phone_number" placeholder="Phone Number" />
          <input type="text" name="email" placeholder="Email" />
          <input type="submit" name="register_merchant" value="Sign me up" />
        </form>
      </div>
      <div class="right">
        <span class="loginwith">Sign in with<br />social network</span>
        <form>
          <button class="social-signin facebook" formaction="signinmer.php">Sign In Merchant</button>
          <button class="social-signin twitter" formaction="admin.php">Admin</button>
          <button class="social-signin user" formaction="index.php">Sign up User</button>
        </form>
      </div>
      <div class="or">OR</div>
   </div>
</body>
</html>
