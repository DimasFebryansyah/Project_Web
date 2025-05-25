<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION["user"];
$is_premium = ($user["account_type"] === "premium");

if (!isset($_SESSION['tryout_result'])) {
    header("Location: tryout.php");
    exit();
}

$result = $_SESSION['tryout_result'];
unset($_SESSION['tryout_result']); // Clear after showing
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tryout | GACORPTN</title>
    <link rel="stylesheet" href="css/styledash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .result-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 30px;
        text-align: center;
    }

    .score-display {
        font-size: 3rem;
        font-weight: bold;
        color: #4361ee;
        margin: 20px 0;
    }

    .tryout-premium .score-display {
        color: #b8860b;
    }

    .score-details {
        display: flex;
        justify-content: space-around;
        margin: 30px 0;
    }

    .score-box {
        padding: 20px;
        border-radius: 10px;
        background-color: #f8f9fa;
        width: 30%;
    }

    .tryout-premium .score-box {
        background-color: #fffaf0;
    }

    .action-buttons {
        margin-top: 30px;
    }

    .action-buttons button {
        margin: 0 10px;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-retry {
        background-color: #4361ee;
        color: white;
    }

    .tryout-premium .btn-retry {
        background-color: gold;
        color: #333;
    }

    .btn-dashboard {
        background-color: #6c757d;
        color: white;
    }

    @media (max-width: 768px) {
        .score-details {
            flex-direction: column;
        }

        .score-box {
            width: 100%;
            margin-bottom: 15px;
        }
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <span class="logo-primary">GACOR</span><span class="logo-accent">PTN</span>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Beranda</a></li>
            <li><a href="tryout.php">Tryout</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li class="dropdown">
                <a href="#services">User <i class="fas fa-chevron-down dropdown-icon"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="change.php">Swith User</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                    <li><a href="delete.php" class="btn btn-danger">Hapus Akun</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero <?php echo $is_premium ? 'hero-premium' : 'hero-normal'; ?>">
        <div class="hero-content">
            <h1>Hasil Tryout <span><?php echo htmlspecialchars($user["email"]); ?></span>!</h1>
            <div id="account-status" class="status-box <?php echo $is_premium ? 'status-premium' : 'status-normal'; ?>">
                <p id="status-message">
                    <?php echo $is_premium ? '✨ Lihat analisis lengkap hasil tryout!' : '✅ Hasil tryout standar'; ?>
                </p>
            </div>
        </div>
    </section>

    <section class="services">
        <div class="section-header">
            <h2>Hasil Tryout UTBK</h2>
            <p class="section-subtitle">Lihat performa tryout terakhirmu</p>
        </div>

        <div class="result-container <?php echo $is_premium ? 'tryout-premium' : ''; ?>">
            <div class="score-display">
                <?php echo number_format($result['score'], 2); ?>%
            </div>

            <div class="score-details">
                <div class="score-box">
                    <h3>Total Soal</h3>
                    <p><?php echo $result['total_questions']; ?></p>
                </div>

                <div class="score-box">
                    <h3>Jawaban Benar</h3>
                    <p><?php echo $result['correct_answers']; ?></p>
                </div>

                <div class="score-box">
                    <h3>Persentase</h3>
                    <p><?php echo number_format($result['score'], 2); ?>%</p>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn-retry" onclick="window.location.href='tryout.php'">
                    <i class="fas fa-redo"></i> Coba Lagi
                </button>
                <button class="btn-dashboard" onclick="window.location.href='dashboard.php'">
                    <i class="fas fa-home"></i> Kembali ke Dashboard
                </button>
            </div>
        </div>
    </section>
</body>

</html>