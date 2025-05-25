<?php
session_start();
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $address = trim($_POST["address"]);
    $school = trim($_POST["school"]);
    $target_university = trim($_POST["target_university"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $account_type = $_POST["account_type"];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: register.php?error=email_exists");
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, address, school, target_university, email, password, account_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $full_name, $address, $school, $target_university, $email, $password, $account_type);

    if ($stmt->execute()) {
        // Store session data
        $_SESSION["user"] = [
            "full_name" => $full_name,
            "address" => $address,
            "school" => $school,
            "target_university" => $target_university,
            "email" => $email,
            "password" => $password,
            "account_type" => $account_type
        ];
        header("Location: register.php?success=registered");
        exit();
    } else {
        header("Location: register.php?error=registration_failed");
        exit();
    }
}

// If not a POST request or registration failed, show the form
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi | GACORPTN</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 15px;
        border: none;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
    }

    .btn-success {
        border-radius: 10px;
        padding: 10px 0;
        font-weight: 600;
    }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 400px;">
            <div class="text-center">
                <h2 class="mb-3">Buat Akun Baru</h2>
            </div>

            <!-- Error message display -->
            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger mb-3">
                <?php 
                    switch ($_GET['error']) {
                        case 'email_exists':
                            echo 'Email sudah terdaftar. Silakan gunakan email lain.';
                            break;
                        case 'registration_failed':
                            echo 'Pendaftaran gagal. Silakan coba lagi.';
                            break;
                        default:
                            echo 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    ?>
            </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="full_name" id="full_name" class="form-control"
                        placeholder="Masukkan nama lengkap" required
                        value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Masukkan alamat"
                        required
                        value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="school" class="form-label">Asal Sekolah</label>
                    <input type="text" name="school" id="school" class="form-control"
                        placeholder="Masukkan asal sekolah" required
                        value="<?php echo isset($_POST['school']) ? htmlspecialchars($_POST['school']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="target_university" class="form-label">Kampus Tujuan</label>
                    <input type="text" name="target_university" id="target_university" class="form-control"
                        placeholder="Masukkan kampus tujuan" required
                        value="<?php echo isset($_POST['target_university']) ? htmlspecialchars($_POST['target_university']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="anda@contoh.com"
                        required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••"
                        required>
                </div>

                <input type="hidden" name="account_type" value="normal">

                <button type="submit" class="btn btn-success w-100">Daftar</button>
            </form>

            <div class="text-center mt-3">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Success -->
    <div class="modal fade" id="registration-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Registrasi Berhasil!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p><strong>Email:</strong> <span
                            id="modal-email"><?php echo isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : ''; ?></span>
                    </p>
                    <p><strong>Password:</strong> <span
                            id="modal-password"><?php echo isset($_SESSION['user']['password']) ? htmlspecialchars($_SESSION['user']['password']) : ''; ?></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="window.location.href='login.php'">Lanjut ke Login</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Show success modal if registration was successful
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success') && urlParams.get('success') === 'registered') {
            const modal = new bootstrap.Modal(document.getElementById('registration-modal'));
            modal.show();
        }

        // Prevent modal from closing when clicking outside
        document.getElementById('registration-modal').addEventListener('show.bs.modal', function() {
            this.setAttribute('data-bs-backdrop', 'static');
            this.setAttribute('data-bs-keyboard', 'false');
        });
    });
    </script>
</body>

</html>