<?php
$servername = "localhost";  // Usually 'localhost' if you are running on the same server
$username = "root";  // Replace with your database username
$password = "";  // Replace with your database password
$dbname = "e_money";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


function getPDOConnection($host, $dbname, $username, $password) {
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
}
?>


<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=e_money', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>

