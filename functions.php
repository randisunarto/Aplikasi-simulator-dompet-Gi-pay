<?php
// Fungsi untuk menyambungkan ke database
function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "e_money"; // Ganti dengan nama database Anda

    // Membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Memeriksa koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Fungsi untuk menambahkan pengguna ke database
function addUserToDatabase($username, $password, $full_name, $phone_number, $email) {
    $conn = getDatabaseConnection();

    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo "Error in SQL prepare: " . $conn->error;
        $conn->close();
        return;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "Error: Username sudah ada.";
        $conn->close();
        return;
    }

    // Tidak menggunakan hashing
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, phone_number, email) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo "Error in SQL prepare: " . $conn->error;
        $conn->close();
        return;
    }

    $stmt->bind_param("sssss", $username, $password, $full_name, $phone_number, $email);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "User successfully added!";
    } else {
        echo "Error adding user: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}


// Fungsi untuk menambahkan pedagang ke database
function addMerchantToDatabase($full_name, $password, $store_name, $phone_number, $email) {
    $conn = getDatabaseConnection();

    // Cek apakah email sudah ada
    $stmt = $conn->prepare("SELECT COUNT(*) FROM merchants WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "Error: Email sudah ada.";
        $conn->close();
        return;
    }

    // Tidak menggunakan hashing
    $stmt = $conn->prepare("INSERT INTO merchants (full_name, password, store_name, phone_number, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $password, $store_name, $phone_number, $email);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}


// Fungsi untuk memperbarui saldo pengguna
function updateUserBalance($user_id, $amount) {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// Fungsi untuk mentransfer dana ke rekening bank
function transferToBankAccount($bank_account_data, $amount) {
    // Simulasikan transfer dana ke rekening bank
    // Di dunia nyata, Anda akan memanggil API bank atau gateway pembayaran di sini

    // Misalkan transfer berhasil, kita catat transaksi di database
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare("INSERT INTO transactions (bank_account_data, amount, status) VALUES (?, ?, 'completed')");
    $stmt->bind_param("sd", $bank_account_data, $amount);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// Fungsi untuk memperbarui status pengguna
function updateUserStatus($user_id, $status) {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// Fungsi untuk memperbarui status pedagang
function updateMerchantStatus($merchant_id, $status) {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare("UPDATE merchants SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $merchant_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
