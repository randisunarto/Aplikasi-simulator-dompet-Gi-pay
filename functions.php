<?php
require_once 'config.php';

// Function to add user to database
function addUserToDatabase($username, $password, $fullName, $phoneNumber, $email) {
    global $conn;
    try {
        // Periksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Username sudah ada. Silakan pilih username lain.");
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, phone_number, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password_hash, $fullName, $phoneNumber, $email);
        if ($stmt->execute()) {
            echo "User berhasil ditambahkan ke database!";
        } else {
            throw new Exception("Gagal menambahkan user ke database.");
        }
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Function to add merchant to database
function addMerchantToDatabase($username, $password, $fullName, $storeName, $storeAddress, $phoneNumber, $email) {
    global $conn;
    try {
        // Periksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT * FROM merchants WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Username sudah ada. Silakan pilih username lain.");
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO merchants (username, password, full_name, store_name, store_address, phone_number, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $username, $password_hash, $fullName, $storeName, $storeAddress, $phoneNumber, $email);
        if ($stmt->execute()) {
            echo "Merchant berhasil ditambahkan ke database!";
        } else {
            throw new Exception("Gagal menambahkan merchant ke database.");
        }
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Function to update user balance
function updateUserBalance($userId, $amount) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $userId);
        if ($stmt->execute()) {
            echo "Saldo pengguna berhasil diperbarui!";
        } else {
            throw new Exception("Gagal memperbarui saldo pengguna.");
        }
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Function to update virtual account balance
function updateVirtualAccountBalance($virtualAccountId, $amount) {
    global $conn;
    $query = "UPDATE virtual_accounts SET balance = balance + $amount WHERE id = $virtualAccountId";
    return $conn->query($query);
}

// Function to update merchant balance
function updateMerchantBalance($merchantId, $amount) {
    global $conn;
    $query = "UPDATE merchants SET balance = balance + $amount WHERE id = $merchantId";
    return $conn->query($query);
}

// Function to add transaction to history
function addTransactionHistory($userId, $merchantId, $amount) {
    global $conn;
    $query = "INSERT INTO transactions (user_id, merchant_id, amount) VALUES ($userId, $merchantId, $amount)";
    return $conn->query($query);
}

// Function to retrieve transaction history
function getTransactionHistory($userId) {
    global $conn;
    $query = "SELECT * FROM transactions WHERE user_id = $userId";
    return $conn->query($query);
}

// Function to retrieve merchant balance
function getMerchantBalance($merchantId) {
    global $conn;
    $query = "SELECT balance FROM merchants WHERE id = $merchantId";
    $result = $conn->query($query);
    return $result->fetch_assoc()['balance'];
}

// Function to transfer funds to bank account
function transferToBankAccount($bankAccountData, $amount) {
    // Implement bank transfer logic here
}

// Function to retrieve payment statistics
function getPaymentStatistics($merchantId, $startDate, $endDate) {
    global $conn;
    $query = "SELECT SUM(amount) AS total FROM transactions WHERE merchant_id = $merchantId AND date BETWEEN '$startDate' AND '$endDate'";
    $result = $conn->query($query);
    return $result->fetch_assoc()['total'];
}

// Function to retrieve withdrawal statistics
function getWithdrawalStatistics($merchantId, $startDate, $endDate) {
    // Implement withdrawal statistics logic here
}

// Function to update user status
function updateUserStatus($userId, $status) {
    global $conn;
    $query = "UPDATE users SET status = '$status' WHERE id = $userId";
    return $conn->query($query);
}

// Function to update merchant status
function updateMerchantStatus($merchantId, $status) {
    global $conn;
    $query = "UPDATE merchants SET status = '$status' WHERE id = $merchantId";
    return $conn->query($query);
}
?>
