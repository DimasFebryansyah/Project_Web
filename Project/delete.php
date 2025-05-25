<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION["user"])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION["user"]["email"];
$message = '';
$showSuccessModal = false;

// Proses penghapusan akun
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    try {
        // Mulai transaksi
        $conn->begin_transaction();
        
        // Hapus data terkait user terlebih dahulu (jika ada tabel relasional)
        // Contoh: $conn->query("DELETE FROM orders WHERE user_email = '$email'");
        
        // Hapus akun user
        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $delete_result = $stmt->execute();
        
        if ($delete_result) {
            // Hapus session dan redirect ke halaman login
            session_unset();
            session_destroy();
            $conn->commit();
            
            // Set session untuk flash message di halaman login
            $_SESSION['account_deleted'] = true;
            $showSuccessModal = true;
        } else {
            throw new Exception("Gagal menghapus akun");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hapus Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    .delete-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .warning-message {
        background-color: #fff8e1;
        border-left: 5px solid #ffc107;
        padding: 15px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="delete-container">
            <h2 class="text-center mb-4">Hapus Akun</h2>

            <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="warning-message">
                <h5><i class="bi bi-exclamation-triangle-fill"></i> Peringatan!</h5>
                <p>Anda akan menghapus akun Anda secara permanen. Semua data yang terkait dengan akun ini akan hilang
                    dan tidak dapat dikembalikan.</p>
            </div>

            <form method="post">
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="confirmCheck" required>
                    <label class="form-check-label" for="confirmCheck">Saya mengerti konsekuensi dari penghapusan akun
                        ini</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Ya, Hapus Akun Saya</button>
                    <a href="dashboard.php" class="btn btn-secondary">Batalkan</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Success -->
    <?php if ($showSuccessModal): ?>
    <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="false"
        style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-success" id="successModalLabel"><i class="bi bi-check-circle-fill"></i>
                        Berhasil</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h4 class="my-3">Akun Berhasil Dihapus</h4>
                    <p>Akun Anda telah dihapus secara permanen dari sistem.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <a href="login.php" class="btn btn-success">Kembali ke Halaman Login</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validasi client-side sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!document.getElementById('confirmCheck').checked) {
            e.preventDefault();
            alert('Anda harus mencentang kotak konfirmasi terlebih dahulu');
        } else if (!confirm('Apakah Anda yakin ingin menghapus akun Anda secara permanen?')) {
            e.preventDefault();
        }
    });

    // Auto redirect setelah 5 detik jika modal success ditampilkan
    <?php if ($showSuccessModal): ?>
    setTimeout(function() {
        window.location.href = 'login.php';
    }, 5000);
    <?php endif; ?>
    </script>
</body>

</html>