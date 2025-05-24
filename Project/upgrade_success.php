<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];

if ($user["account_type"] != "premium") {
    header("Location: upgrade.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Upgrade Berhasil</title>
    <style>
    :root {
        --primary: #4361ee;
        --success: #4cc9f0;
        --light: #f8f9fa;
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

    .success-container {
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

    .success-icon {
        font-size: 72px;
        color: var(--success);
        margin: 20px 0;
    }

    .btn-dashboard {
        background-color: var(--primary);
        color: var(--white);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin-top: 30px;
    }

    .btn-dashboard:hover {
        background-color: #3a56d4;
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
    </style>
</head>

<body>
    <div class="success-container">
        <h2>Upgrade Berhasil!</h2>
        <div class="success-icon">✓</div>
        <p>Selamat! Akun Anda sekarang telah diupgrade ke Premium.</p>
        <p>Anda sekarang dapat menikmati semua fitur eksklusif kami.</p>
        <a href="dashboard.php" class="btn-dashboard">Kembali ke Dashboard</a>
    </div>
</body>

</html>