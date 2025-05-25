<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];

if ($user["account_type"] == "premium") {
    echo "<p>Akun Anda sudah Premium!</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $user["email"];
    $stmt = $conn->prepare("UPDATE users SET account_type = 'premium' WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $_SESSION["user"]["account_type"] = "premium";
        echo "<p>Upgrade berhasil! Silakan kembali ke <a href='dashboard.php'>dashboard</a>.</p>";
    } else {
        echo "<p>Terjadi kesalahan. Coba lagi!</p>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Upgrade ke Premium</title>
    <style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --light: #f8f9fa;
        --dark: #212529;
        --white: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .upgrade-container {
        background-color: var(--white);
        border-radius: 10px;
        box-shadow: var(--shadow);
        padding: 40px;
        width: 100%;
        max-width: 500px;
        text-align: center;
        animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 28px;
    }

    .premium-badge {
        background-color: gold;
        color: var(--dark);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .benefits {
        text-align: left;
        margin: 30px 0;
    }

    .benefits li {
        margin-bottom: 10px;
        padding-left: 25px;
        position: relative;
        list-style-type: none;
    }

    .benefits li:before {
        content: "✓";
        color: var(--success);
        position: absolute;
        left: 0;
        font-weight: bold;
    }

    .btn-upgrade {
        background-color: var(--primary);
        color: var(--white);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 20px;
        width: 100%;
    }

    .btn-upgrade:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(67, 97, 238, 0.2);
    }

    .btn-upgrade:active {
        transform: translateY(0);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .success-message {
        color: #28a745;
        font-weight: bold;
        margin-top: 20px;
    }

    .error-message {
        color: #dc3545;
        font-weight: bold;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div class="upgrade-container">
        <h2>Upgrade Akun Anda</h2>
        <div class="premium-badge">PREMIUM</div>

        <div class="benefits">
            <h3>Dapatkan keuntungan:</h3>
            <ul>
                <li>Akses ke semua fitur premium</li>
                <li>Prioritas dukungan pelanggan</li>
                <li>Penyimpanan lebih besar</li>
                <li>Tanpa iklan</li>
                <li>Update fitur eksklusif</li>
            </ul>
        </div>

        <form action="payment.php" method="GET">
            <button type="submit" class="btn-upgrade">Upgrade Sekarang</button>
        </form>
    </div>
</body>

</html>