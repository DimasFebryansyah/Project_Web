<?php
session_start();
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        header("Location: login.php?error=user_not_found");
        exit();
    }

    // Bandingkan langsung TANPA password_verify()
    if ($password === $user["password"]) {
        // Jika login berhasil
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user"] = [
            "email" => $user["email"],
            "account_type" => $user["account_type"]
        ];
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
}
if (isset($_SESSION['account_deleted'])) {
    unset($_SESSION['account_deleted']); // Hapus session setelah digunakan
    echo '<div class="modal-success" id="successModal">
            <div class="modal-content-success">
                <div class="modal-success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h3>Akun Berhasil Dihapus</h3>
                <p>Akun Anda telah berhasil dihapus dari sistem.</p>
                <button class="btn btn-success mt-3" onclick="document.getElementById(\'successModal\').style.display=\'none\'">OK</button>
            </div>
          </div>
          <script>
          // Tampilkan modal saat halaman dimuat
          document.addEventListener("DOMContentLoaded", function() {
              document.getElementById("successModal").style.display = "flex";
          });
          </script>';}
// If it's not a POST request or login failed, show the login page
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GACORPTN</title>
    <link rel="stylesheet" href="css/stylelog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <a href="index.html"><span class="logo-primary">GACOR</span><span class="logo-accent">PTN</span></a>
            </div>
            <h1>Masuk ke Akun Anda</h1>
        </div>

        <!-- Tampilkan pesan kesalahan jika login gagal -->
        <?php if (isset($_GET['error'])): ?>
        <div id="error-message" style="display: block; color: red;">
            <p>
                <?php 
                    if ($_GET['error'] === 'user_not_found') {
                        echo 'Email tidak ditemukan!';
                    } elseif ($_GET['error'] === 'invalid_credentials') {
                        echo 'Email atau password salah!';
                    } else {
                        echo 'Terjadi kesalahan saat login!';
                    }
                    ?>
            </p>
        </div>
        <?php endif; ?>

        <form class="login-form" action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="anda@contoh.com" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-continue">
                Masuk
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="login-footer">
            <p>Belum punya akun? <a href="register.php">Buat Akun</a></p>
        </div>
    </div>

    <script>
    // Toggle password visibility
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Show error message if present in URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("error")) {
            document.getElementById("error-message").style.display = "block";
        }
    });
    </script>
</body>

</html>