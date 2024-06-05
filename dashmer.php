<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: signinmer.php');
    exit;
}

// Ambil data merchant dari database berdasarkan email
$email = $_SESSION['email'];
$query = $pdo->prepare("SELECT full_name, store_name, id, balance FROM merchants WHERE email = :email");
$query->execute(['email' => $email]);
$merchant = $query->fetch();

if (!$merchant) {
    // Jika data merchant tidak ditemukan
    echo "Merchant tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Toko</title>
    <link rel="stylesheet" href="dashm.css">
    <button id="logout-btn">Logout</button>
</head>
<body>
    <nav class="navbar">
        <!-- Konten navbar di sini -->
    </nav>
    <main class="main-content">
        <section id="profile" class="profile-section">
            <h2>Profil Toko</h2>
            <p>Nama: <?php echo htmlspecialchars($merchant['store_name']); ?></p>
            <p>ID Toko: <?php echo htmlspecialchars($merchant['id']); ?></p>
            <div class="saldo-section">
                <h3>Saldo Toko: Rp. <?php echo number_format($merchant['balance'], 2, ',', '.'); ?></h3>
                <button id="tarik-saldo-btn">Tarik Saldo</button>
            </div>
        </section>
        <section id="transaksi" class="transaksi-section">
            <h2>Statistik Transaksi</h2>
            <div class="date-filter">
                <label for="start-date">Dari Tanggal:</label>
                <input type="date" id="start-date">
                <label for="end-date">Sampai Tanggal:</label>
                <input type="date" id="end-date">
                <button id="filter-btn">Filter</button>
            </div>
            <div class="statistik-container">
                <!-- Bagian Statistik Pembayaran Harian -->
<div id="transaksi_pembayaran">
    <h3>Transaksi Pembayaran Harian</h3>
    <canvas id="paymentChart"></canvas>
    <ul>
        <?php if(isset($transaksi_pembayaran)): ?>
            <?php foreach ($transaksi_pembayaran as $transaksi): ?>
                <li><?php echo $transaksi['keterangan']; ?> - <?php echo $transaksi['jumlah']; ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Tidak ada data transaksi pembayaran harian.</li>
        <?php endif; ?>
    </ul>
</div>

<!-- Bagian Statistik Penarikan Harian -->
<div id="transaksi_penarikan">
    <h3>Transaksi Penarikan Harian</h3>
    <canvas id="withdrawalChart"></canvas>
    <ul>
        <?php if(isset($transaksi_penarikan)): ?>
            <?php foreach ($transaksi_penarikan as $transaksi): ?>
                <li><?php echo $transaksi['keterangan']; ?> - <?php echo $transaksi['jumlah']; ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Tidak ada data transaksi penarikan harian.</li>
        <?php endif; ?>
    </ul>
</div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

<!-- Modal for withdrawal -->
    <div id="tarik-saldo-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Tarik Saldo</h2>
            <form id="tarik-saldo-form">
                <label for="bank-account">Rekening Bank:</label>
                <select id="bank-account" name="bank-account">
                    <option value="bank1">Bank kocak</option>
                    <option value="bank2">Bank anjir</option>
                    <option value="bank3">Bank sampah</option>
                </select>
                <label for="jumlah-tarik">Jumlah yang Ditarik:</label>
                <input type="number" id="jumlah-tarik" name="jumlah-tarik" required>
                <button type="button" id="transfer-btn">Transfer</button>
            </form>
        </div>
    </div>

    <!-- Modal for confirmation -->
    <div id="konfirmasi-modal" class="modal">
        <div class="modal-content">
            <h2>Konfirmasi Penarikan</h2>
            <p>Anda akan menarik saldo sejumlah <span id="jumlah-konfirmasi"></span> ke rekening <span id="rekening-konfirmasi"></span>. Apakah Anda yakin?</p>
            <button id="konfirmasi-btn">Konfirmasi</button>
            <button class="close-btn">Batal</button>
        </div>
    </div>

    <!-- Modal untuk hasil penarikan -->
    <div id="hasil-modal" class="modal">
        <div class="modal-content">
            <h2>Hasil Penarikan</h2>
            <p id="hasil-pesan"></p>
            <button class="close-btn">Tutup</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="dashmer.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logoutBtn = document.getElementById('logout-btn');

            logoutBtn.addEventListener('click', () => {
                window.location.href = 'logout.php';
            });

            // Script untuk grafik Chart.js
            const ctxPayment = document.getElementById('paymentChart').getContext('2d');
            const ctxWithdrawal = document.getElementById('withdrawalChart').getContext('2d');

            new Chart(ctxPayment, {
                type: 'bar',
                data: {
                    labels: ['Tanggal 1', 'Tanggal 2', 'Tanggal 3'],
                    datasets: [{
                        label: 'Pembayaran',
                        data: [10000, 20000, 15000],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            new Chart(ctxWithdrawal, {
                type: 'bar',
                data: {
                    labels: ['Tanggal 1', 'Tanggal 2', 'Tanggal 3'],
                    datasets: [{
                        label: 'Penarikan',
                        data: [5000, 10000, 7000],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
