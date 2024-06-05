<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: sign-inuser.php');
    exit;
}

$username = $_SESSION['username'];

// Ambil data pengguna dari database
$query = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$query->execute(['username' => $username]);
$user = $query->fetch(PDO::FETCH_ASSOC);

$saldo_format = number_format($user['balance'], 2, '.', ',');

if (!$user) {
    echo "Pengguna tidak ditemukan!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'topup':
            $amount = intval($_POST['amount']);
            $newBalance = $user['balance'] + $amount;

            $query = $pdo->prepare("UPDATE users SET balance = :balance WHERE username = :username");
            $query->execute(['balance' => $newBalance, 'username' => $username]);

            // Simpan riwayat top-up
            $query = $pdo->prepare("INSERT INTO transaction_history (username, type, amount) VALUES (:username, 'topup', :amount)");
            $query->execute(['username' => $username, 'amount' => $amount]);

            echo json_encode(['status' => 'success', 'balance' => $newBalance]);
            exit;

        case 'payment':
            $merchantId = $_POST['merchant-id'];
            $payAmount = intval($_POST['amount']);
            $password = $_POST['password'];

            // Verifikasi password
            if ($password === $user['password']) {
                // Verifikasi saldo cukup
                if ($user['balance'] >= $payAmount) {
                    // Kurangi saldo pengguna
                    $newUserBalance = $user['balance'] - $payAmount;

                    // Ambil data pedagang
                    $query = $pdo->prepare("SELECT store_name, balance FROM merchants WHERE id = :id");
                    $query->execute(['id' => $merchantId]);
                    $merchant = $query->fetch();

                    if ($merchant) {
                        $newMerchantBalance = $merchant['balance'] + $payAmount;

                        // Update saldo pengguna
                        $query = $pdo->prepare("UPDATE users SET balance = :balance WHERE username = :username");
                        $query->execute(['balance' => $newUserBalance, 'username' => $username]);

                        // Update saldo pedagang
                        $query = $pdo->prepare("UPDATE merchants SET balance = :balance WHERE id = :id");
                        $query->execute(['balance' => $newMerchantBalance, 'id' => $merchantId]);

                        // Simpan riwayat pembayaran
                        $query = $pdo->prepare("INSERT INTO transaction_history (username, type, amount) VALUES (:username, 'payment', :amount)");
                        $query->execute(['username' => $username, 'amount' => $payAmount]);

                        // Simpan payment in transaksi_pembayaran
                        $query = $pdo->prepare("INSERT INTO transaksi_pembayaran (tanggal, keterangan, jumlah) VALUES (NOW(), :keterangan, :jumlah)");
                        $query->execute(['keterangan' => "Payment to " . $merchant['store_name'], 'jumlah' => $payAmount]);

                        echo json_encode(['status' => 'success', 'balance' => $newUserBalance]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Merchant tidak ditemukan']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Saldo tidak cukup']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Password salah']);
            }
            exit;

        case 'logout':
            session_destroy();
            header('Location: index.php');
            exit;
    }
}

// Ambil riwayat transaksi dari database
$query = $pdo->prepare("SELECT * FROM transaction_history WHERE username = :username ORDER BY timestamp DESC");
$query->execute(['username' => $username]);
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="dashuser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <h2>Dashboard User GIPAY</h2>
        </div>
        <div class="navbar-center">
            <a href="#home">Beranda</a>
            <a href="#topup">Top-Up Saldo</a>
            <a href="#pay">Pembayaran</a>
            <a href="#history">Riwayat Transaksi</a>
        </div>
        <div class="navbar-right">
            <span class="profile-info"><i class="fas fa-user"></i> Nama User: <?php echo htmlspecialchars($user['username']); ?></span>
            <span class="saldo-info"><i class="fas fa-wallet"></i> Saldo: Rp. <?php echo $saldo_format; ?></span>
            <form method="post" action="">
                <input type="hidden" name="action" value="logout">
                <button type="submit" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Keluar</button>
            </form>
        </div>
    </nav>
    <main class="main-content">
        <section id="home" class="home-section">
            <h2>Selamat Datang<?php echo ', ' . htmlspecialchars($user['username']) . '!'; ?></h2>
            <span class="saldo-info">Saldo Anda: Rp. <?php echo number_format($user['balance'], 2, ',', '.'); ?></span>

        </section>
        <section id="topup" class="topup-section">
            <h2>Top-Up Saldo</h2>
            <form id="topup-form">
                <input type="hidden" name="action" value="topup">
                <label for="amount">Top-Up Jumlah:</label>
                <input type="number" id="amount" name="amount" required>
                <p>Transfer ke nomor akun virtual: <strong>1234567890</strong></p>
                <button type="submit" id="topup-btn">Top-Up</button>
            </form>
        </section>
        <section id="pay" class="pay-section">
            <h2>Pembayaran</h2>
            <form id="pay-form">
                <input type="hidden" name="action" value="payment">
                <label for="merchant-id">ID Penjual/Toko:</label>
                <input type="text" id="merchant-id" name="merchant-id" required>
                <label for="pay-amount">Jumlah Pembayaran:</label>
                <input type="number" id="pay-amount" name="amount" required>
                <label for="password">Password Gi-Pay:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" id="pay-btn">Bayar</button>
            </form>
        </section>
        <section id="history" class="history-section">
            <h2>Riwayat Transaksi</h2>
            <ul id="history-list">
                <?php foreach ($transactions as $transaction) : ?>
                    <li>
                        <strong><?php echo htmlspecialchars($transaction['type'] === 'topup' ? 'Top-Up' : 'Pembayaran'); ?>:</strong>
                        Rp. <?php echo htmlspecialchars($transaction['amount']); ?> -
                        <em><?php echo htmlspecialchars($transaction['timestamp']); ?></em>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
    <script src="dashboard.js"></script>
    <script>
        document.getElementById('topup-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const amount = document.getElementById('amount').value;
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: action=topup&amount=${amount}
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Top-Up Berhasil. Saldo Anda sekarang: Rp.' + data.balance);
                    location.reload();
                } else {
                    alert('Top-Up Gagal: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('pay-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const merchantId = document.getElementById('merchant-id').value;
            const payAmount = document.getElementById('pay-amount').value;
            const password = document.getElementById('password').value;
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: action=payment&merchant-id=${merchantId}&amount=${payAmount}&password=${password}
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Pembayaran Berhasil. Saldo Anda sekarang: Rp.' + data.balance);
                    location.reload();
                } else {
                    alert('Pembayaran Gagal: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>