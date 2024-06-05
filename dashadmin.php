<?php
session_start();
include 'config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Handle blocking user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block_user_id'])) {
    $block_user_id = $_POST['block_user_id'];
    $block_sql = "UPDATE users SET is_blocked = 1 WHERE id = ?";
    $stmt = $conn->prepare($block_sql);
    $stmt->bind_param('i', $block_user_id);
    $stmt->execute();
}

// Handle setting discount
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_percentage'], $_POST['discount_start_date'])) {
    $discount_percentage = $_POST['discount_percentage'];
    $discount_start_date = $_POST['discount_start_date'];
    
    $discount_sql = "INSERT INTO setting (discount_percentage, discount_start_date) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE discount_percentage = VALUES(discount_percentage), discount_start_date = VALUES(discount_start_date)";
    $stmt = $conn->prepare($discount_sql);
    $stmt->bind_param('ds', $discount_percentage, $discount_start_date);
    $stmt->execute();
}

// Query untuk mengambil data pengguna
$sql = "SELECT id, username, full_name, phone_number, email, is_blocked FROM users";
$result = $conn->query($sql);
$users = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    $error_message = 'No users found';
}

// Query to get current discount settings
$discount_sql = "SELECT discount_percentage, discount_start_date FROM setting ORDER BY id DESC LIMIT 1";
$discount_result = $conn->query($discount_sql);
$discount = $discount_result->fetch_assoc();

// Query to get sign-up statistics
$public_users_count_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'public'";
$shop_owners_count_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'shop_owner'";

$public_users_count_result = $conn->query($public_users_count_sql);
$shop_owners_count_result = $conn->query($shop_owners_count_sql);

$public_users_count = $public_users_count_result->fetch_assoc()['count'];
$shop_owners_count = $shop_owners_count_result->fetch_assoc()['count'];

// Handle user details request
$user_details = null;
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $detail_sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($detail_sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user_details = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashadmin.css">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>

        <div class="grid-container">
            <div class="user-list card">
                <h2>Daftar Pengguna</h2>
                <?php if (!empty($users)) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo $user['is_blocked'] ? 'Blocked' : 'Active'; ?></td>
                                    <td>
                                        <?php if (!$user['is_blocked']) { ?>
                                            <form method="POST" action="dashadmin.php" style="display:inline;">
                                                <input type="hidden" name="block_user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit">Block</button>
                                            </form>
                                        <?php } ?>
                                        <a href="dashadmin.php?user_id=<?php echo $user['id']; ?>">View Details</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p><?php echo $error_message; ?></p>
                <?php } ?>
            </div>

            <?php if ($user_details) { ?>
                <div class="user-details card">
                    <h2>Detail Pengguna</h2>
                    <p>ID: <?php echo htmlspecialchars($user_details['id']); ?></p>
                    <p>Username: <?php echo htmlspecialchars($user_details['username']); ?></p>
                    <p>Full Name: <?php echo htmlspecialchars($user_details['full_name']); ?></p>
                    <p>Phone Number: <?php echo htmlspecialchars($user_details['phone_number']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($user_details['email']); ?></p>
                    <p>Status: <?php echo $user_details['is_blocked'] ? 'Blocked' : 'Active'; ?></p>
                </div>
            <?php } ?>

            <div class="discount-settings card">
                <h2>Pengaturan Diskon Pembayaran</h2>
                <form method="POST" action="dashadmin.php">
                    <label for="discount_percentage">Persentase Diskon:</label>
                    <input type="number" step="0.01" name="discount_percentage" value="<?php echo htmlspecialchars($discount['discount_percentage']); ?>" required>
                    
                    <label for="discount_start_date">Tanggal Mulai:</label>
                    <input type="date" name="discount_start_date" value="<?php echo htmlspecialchars($discount['discount_start_date']); ?>" required>
                    
                    <button type="submit">Simpan</button>
                </form>
            </div>

            <div class="signup-stats card">
                <h2>Statistik Sign Up</h2>
                <p>Pengguna Publik: <?php echo $public_users_count; ?></p>
                <p>Pemilik Toko: <?php echo $shop_owners_count; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
