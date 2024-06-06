<?php
session_start();
include 'config.php'; // Include database connection

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch merchant data from the database
    $query = $pdo->prepare("SELECT * FROM merchants WHERE email = :email");
    $query->execute(['email' => $email]);
    $merchant = $query->fetch(PDO::FETCH_ASSOC);

    if ($merchant) {
        if ($password === $merchant['password']) { // Membandingkan password plaintext
            // Set session and redirect
            $_SESSION['email'] = $email;
            header('Location: dashmer.php');
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Email tidak ditemukan!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <style media="screen">
    *,
    *:before,
    *:after {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
    }

    body {
      background-color: #2E8B57;
    }

    .background {
      width: 430px;
      height: 520px;
      position: absolute;
      transform: translate(-50%, -50%);
      left: 50%;
      top: 50%;
    }

    .background .shape {
      height: 200px;
      width: 200px;
      position: absolute;
      border-radius: 50%;
    }

    form {
      height: 570px;
      width: 400px;
      background-color: rgba(255, 255, 255, 0.13);
      position: absolute;
      transform: translate(-50%, -50%);
      top: 50%;
      left: 50%;
      border-radius: 10px;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
      padding: 50px 35px;
    }

    form * {
      font-family: 'Poppins', sans-serif;
      color: #ffffff;
      letter-spacing: 0.5px;
      outline: none;
      border: none;
    }

    form h3 {
      font-size: 32px;
      font-weight: 500;
      line-height: 42px;
      text-align: center;
    }

    label {
      display: block;
      margin-top: 30px;
      font-size: 16px;
      font-weight: 500;
    }

    input {
      display: block;
      height: 50px;
      width: 100%;
      background-color: rgba(255, 255, 255, 0.07);
      border-radius: 3px;
      padding: 0 10px;
      margin-top: 8px;
      font-size: 14px;
      font-weight: 300;
    }

    ::placeholder {
      color: #e5e5e5;
    }

    button {
      margin-top: 50px;
      width: 100%;
      background-color: #ffffff;
      color: #080710;
      padding: 15px 0;
      font-size: 18px;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
    }

    .social {
      margin-top: 30px;
      display: flex;
    }

    .social div {
      background: red;
      width: 150px;
      border-radius: 3px;
      padding: 5px 11px 11px 5px;
      background-color: rgba(255, 255, 255, 0.27);
      color: #eaf0fb;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      cursor: pointer;
    }

    .social div:hover {
      background-color: rgba(255, 255, 255, 0.47);
    }

    .social .fb {
      margin-left: 25px;
    }

    .social div a {
      color: inherit;
      text-decoration: none;
      display: block;
      width: 100%;
      height: 100%;
    }

    .error {
      color: red;
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="background">
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  <form method="POST" action="">
    <h3>Merchant Sign In</h3>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <label for="email">Email</label>
    <input type="email" placeholder="Email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" placeholder="Password" id="password" name="password" required>

    <button type="submit">Sign In</button>
    <div class="social">
      <div class="go">
        <a href="index.php">Sign up User</a>
      </div>
      <div class="fb">
        <a href="merchant.php">Sign up Merchant</a>
      </div>
    </div>
  </form>
</body>
</html>
